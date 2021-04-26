<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Bulk_Actions' ) ):

/**
 * WooRechnung Bulk Actions Class
 *
 * This class provides two bulk actions that can be applied
 * to a bunch of orders in the order list. Firstly, all invoices
 * created for those orders can be fetched and exported as a
 * ZIP archive. Secondly, all invoices created can be reset.
 *
 * @version  1.0.0
 * @package  WooRechnung\Admin
 * @author   Zweischneider
 */
final class WR_Bulk_Actions extends WR_Abstract_Module
{
    /**
     * Initialize the hooks of this module.
     *
     * @return void
     */
    public function init_hooks()
    {
        if ( is_admin() ) {
            $this->add_filter('bulk_actions-edit-shop_order', 'add_bulk_actions', 20, 1 );
            $this->add_filter('handle_bulk_actions-edit-shop_order', 'handle_bulk_action', 10, 3);
        }
    }

    /**
     * The callback function to add the bulk actions to the dropdown.
     *
     * @param  array $actions
     * @return array
     */
    public function add_bulk_actions($actions)
    {
        $actions = is_array($actions) ? $actions : array();
        $actions['export_invoices'] = __('Rechnungen exportieren', 'woorechnung');
        $actions['reset_invoices'] = __('Rechnungen zurücksetzen', 'woorechnung');
        return $actions;
    }

    /**
     * The callback function for the dropdown options.
     *
     * @param  string $redirect_to
     * @param  string $action
     * @param  array  $post_ids
     * @return array
     */
    public function handle_bulk_action($redirect_to, $action, $post_ids)
    {
        switch($action)
        {
            case 'reset_invoices':
            $this->reset_invoices($post_ids);
            return $redirect_to;

            case 'export_invoices':
            $this->export_invoices($post_ids);
            return $redirect_to;

            default:
            return $redirect_to;
        }
    }

    /**
     * Reset all invoice ids for the selected orders.
     *
     * @param  array $post_ids
     * @return void
     */
    public function reset_invoices( $post_ids )
    {
        foreach ( $post_ids as $order_id ) {
            $order = new WR_Order_Adapter( $order_id );
            $order->unset_invoice_key();
            $order->unset_invoice_number();
            $order->unset_invoice_date();
        }
    }

    /**
     * Export all selected invoices using a ZIP archive.
     *
     * @param  array $post_ids
     * @return void
     */
    private function export_invoices( $post_ids )
    {
        $callback = array($this, 'retrieve_invoice');
        $invoices = array_map($callback, $post_ids);
        $invoices = array_filter($invoices);
        $this->create_archive($invoices);
    }

    /**
     * Retrieve an invoice for a given order.
     *
     * Invoices are either created just in time if not before
     * or simply downloaded if a key already exists.
     *
     * @param  mixed $order_id
     * @return array|null
     */
    private function retrieve_invoice( $order_id )
    {
        // Wrap up the order using the adapter
        $order = new WR_Order_Adapter( $order_id );

        // Order already has an invoice key, fetch data
        // Return invoice key and data

        if ( $order->has_invoice_key() ) {
            $invoice['order_id'] = $order_id;
            $invoice['invoice_key'] = $order->get_invoice_key();
            $invoice['invoice_number'] = $order->get_invoice_number();
            $invoice['invoice_data'] = $this->fetch_invoice( $order );
            return ! empty ( $invoice['invoice_data'] ) ? $invoice : null;
        }

        // Invoice does not exist yet, create it
        // Fetch invoice afterwards and return key and data

        else {
            $invoice['order_id'] = $order_id;
            $invoice['invoice_key'] = $this->create_invoice( $order );
            $invoice['invoice_data'] = $this->fetch_invoice( $order );
            $invoice['invoice_number'] = $order->get_invoice_number();
            return ! empty ( $invoice['invoice_data'] ) ? $invoice : null;
        }
    }

    /**
     * Fetch an existing invoice for a given order.
     *
     * @param  WR_Order_Adapter $order
     * @return string|null
     */
    private function fetch_invoice( WR_Order_Adapter $order )
    {
        // Try to create an invoice by using the API
        // On success, the invoice data is returned

        try {
            $key = $order->get_invoice_key();
            $result = $this->client()->get_invoice( $key );
            $this->logger()->fetch_invoice_success();
            return $result['data'];
        }

        // Catch any exception that might happen during the process
        // Log the exception and return null instead

        catch (Exception $exception) {
            $this->logger()->fetch_invoice_failed();
            $this->logger()->capture($exception);
            return null;
        }
    }

    /**
     * Create a new invoice for a given order.
     *
     * @param  WR_Order_Adapter $order
     * @return string|null
     */
    private function create_invoice( WR_Order_Adapter $order )
    {
        // Try to create an invoice for a given order
        // Store the invoice UUID on success an add an order note
        // Trigger the action that an invoice has been created

        try {
            $model = $this->factory()->create_invoice( $order );
            $result = $this->client()->create_invoice( $model );
            $order->set_invoice_uuid( $result['uuid'] );
            $order->set_invoice_number( $result['number'] );
            $order->set_invoice_date( $result['invoice_date'] );
            $order->add_note_invoice_created();
            $this->logger()->create_invoice_success();
            return $result['uuid'];
        }

        // Catch any exception that might happen during the process
        // Log the exception and return null instead of the key

        catch (Exception $exception) {
            $this->logger()->create_invoice_failed();
            $this->logger()->capture( $exception );
            return null;
        }
    }

    /**
     * Create the ZIP archive for all invoice files.
     *
     * @param  array $invoices
     * @return string|null
     */
    private function create_archive( $invoices )
    {
        // Prepare export filepath
        $export_file = md5( time() ) . '.zip';
        $export_dir = $this->plugin()->get_exports_path();
        $export_path = $export_dir . $export_file;

        if (!is_dir( $export_dir )) {
            mkdir( $export_dir, 0777, true );
        }

        // Create the zip archive
        $archive = new ZipArchive();
        $mode = ZipArchive::CREATE | ZipArchive::EXCL;
        $result = $archive->open( $export_path, $mode );

        // Check if an error occurred
        if ( $result !== true ) {
            $exception = new WR_Export_Error( $result );
            $this->handler()->handle( $exception );
        }

        // Add invoices to archive
        foreach ( $invoices as $invoice ) {
            $order_id = $invoice['order_id'];
            $invoice_key = $invoice['invoice_key'];
            $invoice_number = $invoice['invoice_number'];
            $invoice_data = $invoice['invoice_data'];
            $filedata = base64_decode($invoice['invoice_data']);
            $filename = "Bestellung_{$invoice['order_id']}.pdf";

            if ( ! empty($invoice_number) ) {
                $filename = "Rechnung_{$invoice_number}.pdf";
                $filename = str_replace(" ", "_", $filename);
                $filename = str_replace("/", "_", $filename);
                $filename = str_replace("\\", "_", $filename);
            }

            $archive->addFromString($filename, $filedata);
        }

        // Close the zip archive
        $result = $archive->close();

        // Check if an error occurred
        if ( $result !== true ) {
            $exception = new WR_Export_Error( $result );
            $this->handler()->handle( $exception );
        }

        $filename = 'Rechnungen_' . date( 'd_m_Y' ) . '.zip';
        $filesize = filesize( $export_path );

        // Send content
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
        echo @readfile( $export_path );

        // Unlink and exit
        @unlink( $export_path );
        exit();
    }


}

endif;
