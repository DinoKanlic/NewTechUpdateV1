<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WR_Order_Adapter' ) ):

/**
 * WooRechnung Order Adapter Class
 *
 * This adapter is used to conveniently operate on the
 * WooCommerce WC_Order class within the plugin.
 *
 * @class    WR_Order_Adapter
 * @version  1.0.0
 * @package  WooRechnung\Support
 * @author   Zweischneider
 */
final class WR_Order_Adapter
{
    /**
     * The meta data key for invoice identifiers.
     *
     * @var string
     */
    const INVOICE_META_KEY = 'woorechnung_uuid';

    /**
     * The old meta key for invoice identifiers.
     *
     * @var string
     */
    const INVOICE_OLD_KEY = 'woo_invoices_invoice_id';

    /**
     * The meta key for invoice numbers.
     *
     * @var string
     */
    const INVOICE_NO_KEY = 'woorechnung_number';

    /**
     * The meta key for invoice numbers.
     *
     * @var string
     */
    const INVOICE_DATE = 'woorechnung_invoice_date';

    /**
     * The meta key for invoice emails.
     *
     * @var string
     */
    const INVOICE_EMAIL_KEY = 'woorechnung_email';

    /**
     * The meta key for invoice appended to email.
     *
     * @var string
     */
    const INVOICE_APPENDED_TO_EMAIL = 'woorechnung_invoice_appended_to_email';

    /**
     * The mapping of billing and shipping title to strings.
     *
     * This is used for the WooCommerce Germanized plugin that adds
     * billing and shipping title values.
     *
     * @var array
     */
    private static $map_titles = array( 1 => 'Herr', 2 => 'Frau' );

    /**
     * The order instance to operate on.
     *
     * @var WC_Order
     */
    private $order;

    /**
     * Create a new instance of the order adapter.
     *
     * @param  int $order_id
     * @return void
     */
    public function __construct( $order_id )
    {
        $this->load_order( $order_id );
    }

    /**
     * Make all order methods directly accessible.
     *
     * @param  string $method
     * @param  mixed  $args
     * @return mixed
     */
    public function __call( $method, $args )
    {
        if (empty($this->order)) {
            return null;
        }

        if (empty($args)) {
            return $this->order->{$method}();
        }

        return $this->order->{$method}($args);
    }

    /**
     * Load the actial order object using the ID.
     *
     * @param  int $order_id
     * @return void
     */
    private function load_order( $order_id )
    {
        $this->order = wc_get_order( $order_id );
    }

    /**
     * Return the order object this class operates on.
     *
     * @return WC_Order
     */
    public function get_order()
    {
        return $this->order;
    }

    /**
     * Decide if the order has an invoice meta key attached to.
     *
     * @return bool
     */
    public function has_invoice_key()
    {
        return $this->has_invoice_uuid() || $this->has_invoice_id();
    }

    /**
     * Get the meta key attached to this order.
     *
     * @return bool
     */
    public function get_invoice_key()
    {
        return $this->has_invoice_uuid() ? $this->get_invoice_uuid() : $this->get_invoice_id();
    }

    /**
     * Unset the invoice key (UUID or ID) for the order and save it.
     *
     * @return void
     */
    public function unset_invoice_key()
    {
        $this->order->delete_meta_data( self::INVOICE_META_KEY );
        $this->order->meta_exists( self::INVOICE_OLD_KEY);
        $this->order->save();
    }

    /**
     * Return the old invoice ID attached to the order.
     *
     * @return string|null
     */
    public function get_invoice_id()
    {
        return trim( $this->order->get_meta( self::INVOICE_OLD_KEY ) );
    }

    /**
     * Decide if this order has an old invoice ID attached to.
     *
     * @return bool
     */
    public function has_invoice_id()
    {
        return $this->order->meta_exists( self::INVOICE_OLD_KEY );
    }

    /**
     * Unset the invoice UUID for the order and save it.
     *
     * @return void
     */
    public function unset_invoice_id()
    {
        $this->order->delete_meta_data( self::INVOICE_OLD_KEY );
        $this->order->save();
    }

    /**
     * Decide if the order has an invoice UUID attached to.
     *
     * @return bool
     */
    public function has_invoice_uuid()
    {
        return $this->order->meta_exists( self::INVOICE_META_KEY );
    }

    /**
     * Return the invoice UUID attached to the order.
     *
     * @return string|null
     */
    public function get_invoice_uuid()
    {
        return trim( $this->order->get_meta( self::INVOICE_META_KEY, true ) );
    }

    /**
     * Set the invoice UUID for the order and save it.
     *
     * @param  string $uuid
     * @return void
     */
    public function set_invoice_uuid( $uuid )
    {
        $this->order->add_meta_data( self::INVOICE_META_KEY, $uuid );
        $this->order->save();
    }

    /**
     * Unset the invoice UUID for the order and save it.
     *
     * @return void
     */
    public function unset_invoice_uuid()
    {
        $this->order->delete_meta_data( self::INVOICE_META_KEY );
        $this->order->save();
    }

    /**
     * Decide if this order has an invoice number attached to.
     *
     * @return bool
     */
    public function has_invoice_number()
    {
        return $this->order->meta_exists( self::INVOICE_NO_KEY );
    }

    /**
     * Return the invoice number attached to the order.
     *
     * @return string|null
     */
    public function get_invoice_number()
    {
        return trim( $this->order->get_meta( self::INVOICE_NO_KEY, true ) );
    }

    /**
     * Set the invoice UUID for the order and save it.
     *
     * @param  string $number
     * @return void
     */
    public function set_invoice_number( $number )
    {
        $this->order->add_meta_data( self::INVOICE_NO_KEY, $number );
        $this->order->save();
    }

    /**
     * Unset the invoice UUID for the order and save it.
     *
     * @return void
     */
    public function unset_invoice_number()
    {
        $this->order->delete_meta_data( self::INVOICE_NO_KEY );
        $this->order->save();
    }

    /**
     * Return the invoice date attached to the order.
     *
     * @return string|null
     */
    public function get_invoice_date()
    {
        return trim( $this->order->get_meta( self::INVOICE_DATE, true ) );
    }

    /**
     * Set the invoice date for the order and save it.
     *
     * @param  string $number
     * @return void
     */
    public function set_invoice_date( $date )
    {
        $this->order->add_meta_data( self::INVOICE_DATE, $date );
        $this->order->save();
    }

    /**
     * Unset the invoice date for the order and save it.
     *
     * @return void
     */
    public function unset_invoice_date()
    {
        $this->order->delete_meta_data( self::INVOICE_DATE );
        $this->order->save();
    }    

    /**
     * Retrieve the value for the invoice email status.
     *
     * @return string
     */
    public function get_invoice_email()
    {
        return $this->order->get_meta( self::INVOICE_EMAIL_KEY, "no" );
    }

    /**
     * Set the invoice email value for the order and save it.
     *
     * @param  string $value
     * @return void
     */
    public function set_invoice_email( $value )
    {
        $this->order->add_meta_data( self::INVOICE_EMAIL_KEY, $value);
        $this->order->save();
    }

    /**
     * Decide if the invoice email has been sent to the customer.
     *
     * @return bool
     */
    public function has_sent_invoice_email()
    {
        return $this->get_invoice_email() === "yes";
    }

    /**
     * Set the invoice email to be sent for this order.
     *
     * @return void
     */
    public function set_sent_invoice_email()
    {
        $this->set_invoice_email("yes");
    }

    /**
     * Unset the invoice email value for the order and save it.
     *
     * @return void
     */
    public function unset_invoice_email()
    {
        $this->order->delete_meta_data( self::INVOICE_EMAIL_KEY );
        $this->order->save();
    }

    /**
     * Retrieve the value for the invoice appended to email status.
     *
     * @return string
     */
    public function get_invoice_appended_to_email()
    {
        return $this->order->get_meta( self::INVOICE_APPENDED_TO_EMAIL, "no" );
    }

    /**
     * Set the invoice appended to email value for the order and save it.
     *
     * @param  string $value
     * @return void
     */
    public function set_invoice_appended_to_email( $value = 'yes' )
    {
        $this->order->add_meta_data( self::INVOICE_APPENDED_TO_EMAIL, $value);
        $this->order->save();
    }

    /**
     * Decide if the invoice appended to email has been sent to the customer.
     *
     * @return bool
     */
    public function has_invoice_appended_to_email()
    {
        return $this->get_invoice_appended_to_email() === "yes";
    }

    /**
     * Unset the invoice appended to email value for the order and save it.
     *
     * @return void
     */
    public function unset_invoice_appended_to_email()
    {
        $this->order->delete_meta_data( self::INVOICE_APPENDED_TO_EMAIL );
        $this->order->save();
    }

    /**
     * Add a note to the order history.
     *
     * @param  string $message
     * @return void
     */
    public function add_note( $message )
    {
        $this->order->add_order_note( __( $message, 'woorechnung' ), false );
    }

    /**
     * Add a note that an invoice has been created.
     *
     * @return void
     */
    public function add_note_invoice_created()
    {
        $this->add_note( 'Rechnung erstellt' );
    }

    /**
     * Add a note that an invoice has been cancelled.
     *
     * @return void
     */
    public function add_note_invoice_cancelled()
    {
        $this->add_note( 'Rechnung storniert' );
    }

    /**
     * Add a note that an invoice has been completed.
     *
     * @return void
     */
    public function add_note_invoice_completed()
    {
        $this->add_note( 'Rechnung abgeschlossen' );
    }

    /**
     * Add a note that an invoice has been appended to the WooCommerce email.
     *
     * @return void
     */
    public function add_note_invoice_appended()
    {
        $this->add_note( 'Rechnung mit WooCommerce E-Mail versandt.' );
    }

    /**
     * Add a note that an invoice been sent as email.
     *
     * @return void
     */
    public function add_note_invoice_emailed()
    {
        $this->add_note( 'Rechnung als E-Mail versandt.' );
    }

    /**
     * Return the billing vat id for this order.
     *
     * This method returns the billing vat id added to the order object
     * by extensions like WooCommerce Germanized or other.
     *
     * @return string|null
     */
    public function get_billing_vat_id()
    {
        $meta_fields = [
            '_billing_vat_id',          // WP Plugin: Germanized
            '_billing_eu_vat_number',   // WP Plugin: EU VAT for WooCommerce
            '_vat_number',              // WP Plugin: EU VAT Number
            'vat_number',               // WP Plugin: EU VAT Assistant
        ];
        foreach ($meta_fields as $meta_field) {
            $billing_vat_id = $this->order->get_meta( $meta_field, true );
            if (!empty($billing_vat_id)) {
                return $billing_vat_id;
            }
        }
        return null;
    }

    /**
     * Return the shipping vat id for this order.
     *
     * This method returns the shipping vat id the WooCommerce
     * Germanized extension adds to the order object.
     *
     * @return string|null
     */
    public function get_shipping_vat_id()
    {
        $meta_fields = [
            '_shipping_vat_id',         // WP Plugin: Germanized
            '_shipping_eu_vat_number',  // WP Plugin: EU VAT for WooCommerce
        ];
        foreach ($meta_fields as $meta_field) {
            $billing_vat_id = $this->order->get_meta( $meta_field, true );
            if (!empty($billing_vat_id)) {
                return $billing_vat_id;
            }
        }
        return null;
    }

    /**
     * Check if a key represents a valid billing or shipping title key.
     *
     * @param  int|null $key
     * @return bool
     */
    private static function has_title( $key )
    {
        return array_key_exists( intval( $key ) , self::$map_titles );
    }

    /**
     * Map a billing or shipping title key to the correct string representation.
     *
     * @param  int|null $key
     * @return string|null
     */
    private static function map_title( $key )
    {
        return self::has_title( $key ) ? self::$map_titles[$key] : null;
    }

    /**
     * Return the billing title value for this order.
     *
     * @return string|null
     */
    public function get_billing_title()
    {
        $key = $this->order->get_meta( '_billing_title', true );
        return self::map_title( $key );
    }

    /**
     * Return the shipping title value for this order.
     *
     * @return string|null
     */
    public function get_shipping_title()
    {
        $key = $this->order->get_meta( '_shipping_title', true );
        return self::map_title( $key );
    }

    /**
     * Decide if this order has an active state.
     *
     * Active states are 'pending', 'processing', 'on-hold'
     * and 'completed'.
     *
     * @return bool
     */
    public function is_outstanding()
    {
        return $this->order->has_status( array('pending', 'processing', 'on-hold', 'completed') );
    }

    /**
     * Decide if this order has been cancelled.
     *
     * @return bool
     */
    public function is_cancelled()
    {
        return $this->order->has_status( array('cancelled', 'refunded') );
    }

    /**
     * Trigger invoice created action for this order.
     *
     * @return void
     */
    public function do_action_invoice_created()
    {
        do_action( 'woorechnung_invoice_created' , $this );
    }

    /**
     * Trigger invoice cancelled action for this order.
     *
     * @return void
     */
    public function do_action_invoice_cancelled()
    {
        do_action( 'woorechnung_invoice_cancelled' , $this );
    }
}

endif;
