<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WR_Order_Handler' ) ):

/**
 * WooRechnung Order Handler Module.
 *
 * This class automatically processes order on status
 * update and performs the subsequent action if the preconditions
 * fir the user settings.
 *
 * @version  1.0.0
 * @package  WooRechnung
 * @author   Zweischneider
 */
final class WR_Order_Handler extends WR_Abstract_Module
{
    /**
     * The order states this handler reacts to.
     *
     * @var array
     */
    private static $_states = array(
        'pending',
        'processing',
        'on-hold',
        'completed',
        'cancelled',
        'refunded',
    );

    /**
     * Initialize the hooks of this module.
     *
     * @return void
     */
    public function init_hooks()
    {
        // Action for processing order after order fully created
        $this->add_action('woocommerce_gzd_checkout_order_before_confirmation', 'process_order_object', 9, 1);

        // Actions for processing order on status update
        foreach ( self::$_states as $status ) {
            $this->add_action("woocommerce_order_status_{$status}", 'process_order', 9, 1);
        }
    }

    /**
     * Automatically process the order without status update (e.g. on order created).
     *
     * @param  WC_Order $order
     * @return bool
     */
    public function process_order_object( $order )
    {
        $order_id = !empty($order) ? $order->get_id() : '';
        $this->logger()->verbose('CALL', ['order_id' => $order_id, 'wp_filter' => current_filter()]);
        $order = new WR_Order_Adapter($order_id);
        $this->maybe_create_invoice($order);
    }

    /**
     * Automatically process the order on a status update.
     *
     * @param  int $order_id
     * @return bool
     */
    public function process_order( $order_id )
    {
        $this->logger()->verbose('CALL', ['order_id' => $order_id, 'wp_filter' => current_filter()]);

        $order = new WR_Order_Adapter($order_id);

        if ( $order->is_outstanding( $order ) ) {
            $this->logger()->verbose('IF $order->is_outstanding($order)');
            return $this->maybe_create_invoice( $order );
        }

        if ( $order->is_cancelled() ) {
            $this->logger()->verbose('IF $order->is_cancelled()');
            return $this->maybe_cancel_invoice( $order );
        }
    }

    /**
     * Maybe create an invoice if all prerequisites are fullfilled.
     *
     * @param  WR_Order_Adapter
     * @return bool
     */
    private function maybe_create_invoice( $order )
    {
        $order_id = !empty($order) ? $order->get_id() : '';
        $this->logger()->verbose('CALL', ['order_id' => $order_id]);

        if ( $order->has_invoice_key() ) {
            return false;
        }

        $settings = $this->settings();
        if ( ! $settings->get_create_invoices() ) {
            $this->logger()->verbose('IF !$settings->get_create_invoices() IN');
            return false;
        }

        $status = $order->get_status();
        if ( ! $settings->create_invoice_for_state( $status ) ) {
            $this->logger()->verbose('IF !$settings->create_invoice_for_state("'.$status.'") IN', [
                'settings' => ['invoice_for_states' => $settings->get_invoice_for_states()]
            ]);
            return false;
        }

        $method = $order->get_payment_method();
        if ( ! $settings->create_invoice_for_method( $method ) ) {
            $this->logger()->verbose('IF !$settings->create_invoice_for_method("'.$method.'") IN');
            return false;
        }

        $value = $order->get_total();
        if ( ! $settings->create_invoice_for_value( $value ) ) {
            $this->logger()->verbose('IF !$settings->create_invoice_for_value('.$value.') IN');
            return false;
        }

        $this->logger()->verbose('SUCCESS', [
            'order_id' => $order_id,
            'order_status' => $status,
            'order_payment_method' => $method,
            'order_total' => $value
        ]);

        $this->create_invoice($order);
        return true;
    }

    /**
     * Actually create an invoice for a given order.
     *
     * @param  WR_Order_Adapter $order
     * @return void
     */
    private function create_invoice( $order )
    {
        $this->logger()->verbose('CALL', ['order_id' => !empty($order) ? $order->get_id() : '']);

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
            $order->do_action_invoice_created();
            $this->logger()->create_invoice_success();
        }

        // Catch any exception that might happen during the process
        // Log the exception

        catch (Exception $exception) {
            $this->logger()->create_invoice_failed();
            $this->logger()->capture( $exception );
        }
    }

    /**
     * Maybe cancel an invoice if all prerequisites are fullfilled.
     *
     * @param  WR_Order_Adapter
     * @return bool
     */
    private function maybe_cancel_invoice( $order )
    {
        if ( ! $order->has_invoice_key() ) {
            return false;
        }

        $settings = $this->settings();
        if ( ! $settings->get_cancel_invoices() ) {
            return false;
        }

        $this->cancel_invoice( $order );
        return true;
    }

    /**
     * Actually cancel an invoice for a given order.
     *
     * @param  WR_Order_Adapter $order
     * @return void
     */
    private function cancel_invoice( $order )
    {


        // Cancel the invoice by calling the API method
        // Add an order note that the invoice was cancelled
        try {
            $key = $order->get_invoice_key();
            $this->client()->cancel_invoice( $key );
            $order->add_note_invoice_cancelled();
            $order->do_action_invoice_cancelled();
            $this->logger()->cancel_invoice_success();
        }

        // Catch any exception that happens during cancellation
        // Log the exception

        catch (Exception $exception) {
            $this->logger()->cancel_invoice_failed();
            $this->logger()->capture( $exception );
        }
    }
}

endif;
