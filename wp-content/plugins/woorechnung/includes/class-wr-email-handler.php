<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Email_Handler' ) ):

/**
 * WooRechnung Email Handler Module.
 *
 * @class    WR_Email_Handler
 * @version  1.0.0
 * @package  WooRechnung
 * @author   Zweischneider
 */
final class WR_Email_Handler extends WR_Abstract_Module
{
    /**
     * The alternative text body of invoice emails.
     *
     * @var string|null
     */
    private $_mail_alt_body = null;

    /**
     * Initialize the hooks of this module.
     *
     * @return void
     */
    public function init_hooks()
    {
        $this->add_action('phpmailer_init', 'configure_phpmailer', 8, 1);
        $this->add_action('woorechnung_invoice_created', 'process_mailing', 10, 1);
        $this->add_action('woocommerce_order_status_changed', 'process_mailing_delayed', 11, 3);
        $this->add_filter('woocommerce_email_attachments', 'process_appending', 10, 3); // keep 3 parameters to remain backwards compatible.
    }

    /**
     * Configure the PHPMailer instance.
     *
     * Add the alternative plain text body to the mailer.
     *
     * @param  PHPMailer $phpmailer
     * @return void
     */
    public function configure_phpmailer($phpmailer)
    {
        if ($phpmailer instanceof \PHPMailer\PHPMailer\PHPMailer) {
            if (!empty($this->_mail_alt_body)) {
                $body = $this->_mail_alt_body;
                $phpmailer->AltBody = $body;
                $this->_mail_alt_body = null;
            }
        }
    }

    /**
     * Process the mailing of an invoice.
     *
     * @param  WR_Order_Adapter
     * @return bool
     */
    public function process_mailing( WR_Order_Adapter $order )
    {
        // Abort if invoice is not to be sent as email
        $settings = $this->settings();
        if ( ! $settings->send_invoice_as_email() ) {
            return false;
        }

        // Abort if email is not to be sent for this state
        $status = $order->get_status();
        if ( ! $settings->send_email_for_state( $status ) ) {
            return false;
        }

        // Abort if invoice is not to be sent for its payment method
        $method = $order->get_payment_method();
        if ( ! $settings->send_email_for_method( $method ) ) {
            return false;
        }

        // Send the invoice as an own email
        return $this->send_invoice_as_email( $order );
    }

    /**
     * Actually send the invoice as an email.
     *
     * @param  WR_Order_Adapter $adapter
     * @return void
     */
    private function send_invoice_as_email( WR_Order_Adapter $order )
    {
        // Try to download and send the invoice via email
        // Add an order note to signal the successful sent

        try {
            $key = $order->get_invoice_key();
            $result = $this->client()->get_invoice( $key );
            $filepath = $this->store_invoice($order, $result['data']);
            $this->send_invoice($order, $filepath);
            $order->add_note_invoice_emailed();
            $order->set_sent_invoice_email();
            $this->logger()->send_invoice_as_email_success();
        }

        // Catch any exception that happens during the process
        // Log error and add error notice for user

        catch (Exception $exception) {
            $this->logger()->send_invoice_as_email_failed();
            $this->logger()->capture($exception);
        }
    }

    /**
     * Store invoice in temp folder to send it as email.
     *
     * @param  WR_Order_Adapter $order
     * @param  string   $data
     * @return string
     */
    private function store_invoice($order, $data)
    {
        // Construct filename and filepath
        $order_no = $order->get_id();
        $invoice_no = $order->get_invoice_number();
        $invoice_key = $order->get_invoice_key();
        $invoice_date = $order->get_invoice_date();
        $company = $order->get_billing_company();
        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();

        if (!empty($invoice_date)) {
            $invoice_date_time = new \DateTime($invoice_date);
            $invoice_date_day = $invoice_date_time->format('d');
            $invoice_date_month = $invoice_date_time->format('m');
            $invoice_date_year = $invoice_date_time->format('Y');
        }

        // Create directory to store the invoice in
        $directory = $this->plugin()->get_temp_path("invoices/{$invoice_key}");
        $file_name = $this->settings()->get_email_filename();
        $file_name = trim($file_name);

        // Replace filename placeholders
        $variables = $this->plugin()->get_invoice_filename_variables();
        $replaces = array();
        foreach ($variables as $name => $description) {
            $replaces['%'.$name.'%'] = isset($$name) ? $$name : '';
        }
        $file_name = str_replace(array_keys($replaces), array_values($replaces), $file_name);

        // Use default filename if settings is not set
        $file_name = empty($file_name) ? 'Rechnung' : $file_name;
        $file_name = "{$file_name}.pdf";

        // Remove characters that might interfere with the filepath
        $file_name = str_replace("/", "_", $file_name);
        $file_name = str_replace("\\", "_", $file_name);

        // Finally determine filepath and filedata
        $file_path = "{$directory}/{$file_name}";
        $file_data = base64_decode($data);

        // Make directory and put contents
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        file_put_contents($file_path, $file_data);

        return $file_path;
    }

    /**
     * Send the invoice as email, using the mailing component.
     *
     * @param  WR_Order_Adapter $adapter
     * @param  string $filepath
     * @return void
     */
    private function send_invoice($order, $filepath)
    {
        $settings = $this->settings();
        $mailer = $this->mailer();

        $customer = $order->get_billing_email();
        $copies = $settings->send_email_copies_to();
        $subject = $settings->get_email_subject();
        $content_html = $settings->get_email_content_html();
        $content_text = $settings->get_email_content_text();

        $search = array(
            '%order_no%',
            '%invoice_no%',
            '%first_name%',
            '%last_name%',
            '%page_title%'
        );

        $replace = array(
            $order->get_id(),
            $order->get_invoice_number(),
            $order->get_billing_first_name(),
            $order->get_billing_last_name(),
            wp_get_document_title()
        );

        $subject = str_replace($search, $replace, $subject);
        $content_text = str_replace($search, $replace, $content_text);
        $content_html = str_replace($search, $replace, $content_html);
        $content_html = empty($content_html) ? $content_text : $content_html;
        $this->_mail_alt_body = $content_text;

        $customer = apply_filters('woorechnung_filter_send_invoice_email_recipient', $customer, $order); // ->get_user());

        $mailer->add_recipient( $customer );
        $mailer->add_recipients( $copies );
        $mailer->set_subject( $subject );
        $mailer->set_contents( $content_html );
        $mailer->add_attachment( $filepath );
        $mailer->send_email();
    }

    /**
     * Get id of order or post object
     *
     * @param  object  $post
     * @return int|null
     */
    public function get_order_id($post)
    {
        $order_id = null;

        if (is_a($post, 'YITH_YWGC_Gift_Card')) {
            if (property_exists($post, 'order_id')) {
                $order_id = $post->order_id;
            }
        }

        if (is_null($order_id) && method_exists($post, 'get_id')) {
            $order_id = $post->get_id();
        }

        return $order_id;
    }

    /**
     * Process the trigger to append the invoice to the WooCommerce email.
     *
     * @param  array  $attachments
     * @param  string $status
     * @param  WC_Order $order
     * @return array
     */
    public function process_appending( $attachments, $type, $post, $mail = null )
    {
        // Avoid interference with other plugins
        if ( empty($post) || ! is_object($post) ) {
            return $attachments;
        }

        // Get and check order id.
        $order_id = $this->get_order_id($post);
        if (empty($order_id)) {
            $this->logger()->verbose('IF empty($order_id) IN', [
                'order_id' => $order_id
            ]);
            return $attachments;
        }

        $this->logger()->verbose('CALL', ['type' => $type, 'order_id' => $order_id]);

        // Load order using the adapter
        $order = new WR_Order_Adapter($order_id);

        // Check if order was loaded.
        // WooCommerce uses Wordpress posts to handle products and orders.
        // We doesn't know if we processing an email for a order or a product.
        // If it can't load the post as an order then we can ignore the mail.
        if (empty($order->get_order())) {
            $this->logger()->verbose('IF empty($order->get_order()) IN', [
                'post_id' => $order_id,
                'class of object' => get_class($post)
            ]);
            return $attachments;
        }

        // Load the user settings
        $settings = $this->settings();

        // Invoice already sent
        if ($order->has_invoice_appended_to_email()
            && $this->plugin()->is_email_to_customer($type)
            && $type != 'customer_invoice'
        ) {
            $this->logger()->verbose(
                'IF $order->has_invoice_appended_to_email() '.
                '&& $this->plugin()->is_email_to_customer("'.$type.'") '.
                '&& "'.$type.'" != "customer_invoice" IN'
            );
            return $attachments;
        }

        // Abort if invoice is not to be appended to email
        if (!$settings->append_invoice_to_email() ) {
            $this->logger()->verbose('IF !$settings->append_invoice_to_email() IN');
            return $attachments;
        }

        // Abort if invoice is not to be sent for its payment method
        $method = $order->get_payment_method();
        if (!$settings->send_email_for_method($method) ) {
            $this->logger()->verbose('IF !$settings->send_email_for_method("'.$method.'") IN');
            return $attachments;
        }

        # Abort email type has not been selected in settings
        if (!$settings->append_invoice_to_email_type($type)
            && $type != 'customer_invoice'
        ) {
            $this->logger()->verbose(
                'IF !$settings->append_invoice_to_email_type("'.$type.'") '.
                '&& "'.$type.'" != "customer_invoice" IN'
            );
            return $attachments;
        }

        $this->logger()->verbose('SUCCESS', [
            'order_id' => $order_id,
            'order_payment_method' => $method,
            'email_type' => $type
        ]);

        // Proceed attaching invoice to email
        return $this->append_invoice_to_email( $attachments, $order, $type );
    }

    /**
     * Download the invoice and append it to the WooCommerce email
     *
     * @param  array $attachments
     * @param  WR_Order_Adapter $order
     * @param  string $email_type
     * @return array
     */
    public function append_invoice_to_email( $attachments, WR_Order_Adapter $order, $email_type )
    {
        $this->logger()->verbose('CALL', ['order_id' => !empty($order) ? $order->get_id() : '']);

        // Try to download the invoice as PDF and attach it
        // Add a notice to the order and return attachments

        try {
            $key = $order->get_invoice_key();
            $result = $this->client()->get_invoice( $key );
            $filepath = $this->store_invoice($order, $result['data']);
            $result = array_merge($attachments, array($filepath));
            $this->logger()->append_invoice_to_email_success();
            if ($this->plugin()->is_email_to_customer($email_type)) {
                $order->add_note_invoice_emailed();
                $order->set_invoice_appended_to_email();
            }
            return $result;
        }

        // Catch any exception that happens during the process
        // Log error and add error notice for user

        catch (Exception $exception) {
            $this->logger()->append_invoice_to_email_failed();
            $this->logger()->capture($exception);
            return $attachments;
        }
    }

    public function process_mailing_delayed($order_id)
    {
        $order = new WR_Order_Adapter($order_id);

        if ($order->has_sent_invoice_email()) {
            return false;
        }

        $this->process_mailing($order);
        return true;
    }
}

endif;
