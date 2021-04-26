<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Order_Action' ) ):

/**
 * WooRechnung Order Action Module.
 *
 * This class implements the button to be added to the WooCommerce
 * list of orders in the admin panel. When the button is clicked,
 * either a new invoice is created or an existing invoice is downloaded
 * and shown to the user.
 *
 * @version  1.0.0
 * @package  WooRechnung\Admin
 * @author   Zweischneider
 */
final class WR_Order_Action extends WR_Abstract_Module
{
    /**
     * Initialize the hooks of this module.
     *
     * @return void
     */
    public function init_hooks()
    {
        if ( is_admin() ) {
            $this->add_action('woocommerce_admin_order_actions_end', 'add_invoice_button');
            $this->add_action('wp_ajax_woorechnung_invoice', 'handle_invoice_button_ajax');
        }
    }

    /**
     * Callback to add the invoice button to the order.
     *
     * @param  WC_Order $order
     * @return void
     */
    public function add_invoice_button( $order )
    {
        $adapter = new WR_Order_Adapter( $order->get_id() );
        $params = $this->prepare_params( $adapter );
        $button = $this->prepare_button( $params );
        echo $button;
    }

    /**
     * Prepare the parameters for the invoice button.
     *
     * The button requires a URI target with the action to trigger on click,
     * the CSS class for the icon and the textthat matches the action to
     * trigger (create or download the invoice).
     *
     * @param  WR_Order_Adapter $order
     * @return array
     */
    private function prepare_params( WR_Order_Adapter $order )
    {
        // Prepare the target URL for the action button

        $invoice = $order->get_invoice_key();
        $params = array('action' => 'woorechnung_invoice', 'order_id' => $order->get_id());
        $target = 'admin-ajax.php?' . http_build_query($params);
        $target = wp_nonce_url( admin_url($target), 'woorechnung_invoice');

        $icon_create = 'button icon-pdf-add';
        $icon_fetch = 'button icon-pdf';
        $text_create = __('Rechnung erstellen', 'woorechnung');
        $text_fetch = __('Rechnung abrufen', 'woorechnung');

        // Return the parameters
        $result = array();
        $result['target'] = $target;
        $result['class'] = empty($invoice) ? $icon_create : $icon_fetch;
        $result['text'] = empty($invoice) ? $text_create : $text_fetch;
        return $result;
    }

    /**
     * Prepare the button in that it can be printed.
     *
     * @param  array $params
     * @return string
     */
    private function prepare_button($params)
    {
        $result  = '<a ';
        $result .= 'class="'.$params['class'].'" ';
        $result .= 'href="'.$params['target'].'" ';
        $result .= 'alt="'.$params['text'].'" ';
        $result .= 'title="'.$params['text'].'" ';
        $result .= 'aria-label="'.$params['text'].'" ';
        $result .= 'data-tip="'.$params['text'].'" ';
        $result .= 'target="_BLANK"';
        $result .= '></a>';
        return $result;
    }

    /**
     * Handle the ajax action when the button is clicked.
     *
     * If there is no invoice key attached to the order th button was
     * triggered for, a new invoice is created first. Afterwards, the
     * invoice is fetched and displayed as PDF.
     *
     * @retur void
     */
    public function handle_invoice_button_ajax()
    {
        // Fetch the order id parameter
        $order_id = $_GET['order_id'];
        $order = new WR_Order_Adapter($order_id);

        // Create invoice if necessary
        if (!$order->has_invoice_key()) {
            $this->create_invoice( $order );
        }

        // Show the invoice for the order
        $this->show_invoice( $order );
    }

    /**
     * Create a new invoice by sending a request to the API.
     *
     * @param  WR_Order_Adapter $order
     * @return string
     */
    private function create_invoice( WR_Order_Adapter $order )
    {
        // Try to create an invoice by using the API
        // On Success the UUID is stored and a note added

        try {
            $model = $this->factory()->create_invoice( $order );
            $result = $this->client()->create_invoice( $model );
            $order->set_invoice_uuid($result['uuid']);
            $order->set_invoice_number($result['number']);
            $order->set_invoice_date( $result['invoice_date'] );
            $order->add_note_invoice_created();
            $this->logger()->create_invoice_success();
        }

        // Catch any exception that might happen during the process
        // Log the exception and let the handler exit properly

        catch (Exception $exception) {
            $this->logger()->create_invoice_failed();
            $this->logger()->capture($exception);
            $this->handler()->handle($exception);
        }
    }

    /**
     * Fetch and display the invoice PDF.
     *
     * @param  WR_Order_Adapter $order
     * @return void
     */
    private function show_invoice( WR_Order_Adapter $order )
    {
        // Try to create an invoice by using the API
        // On success, the invoice data is displayed or downloaded

        try {
            $key = $order->get_invoice_key();
            $result = $this->client()->get_invoice( $key );
            $this->viewer()->view_pdf($key, $result['data']);
            $this->logger()->fetch_invoice_success();
        }

        // Catch any exception that might happen during the process
        // Log the exception and let the handler exit properly

        catch (Exception $exception) {
            $this->logger()->fetch_invoice_failed();
            $this->logger()->capture($exception);
            $this->handler()->handle($exception);
        }
    }

}

endif;
