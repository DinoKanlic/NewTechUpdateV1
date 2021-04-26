<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WR_Settings' ) ):

/**
 * WooRechnung Settings Component.
 *
 * This class provides an interface to all user settings that this
 * plugin requires. The settings are stored and loaded using the WordPress
 * options API.
 *
 * @class    WR_Settings
 * @extends  WR_Abstract_Settings
 * @version  1.0.0
 * @package  WooRechnung\Common
 * @author   Zweischneider
 */
final class WR_Settings extends WR_Abstract_Settings
{
    /**
     * The (unprefixed) keys of all settings.
     *
     * @var array
     */
    protected $_keys = array(
        'shop_url',
        'shop_token',
        'create_invoices',
        'cancel_invoices',
        'invoice_for_states',
        'invoice_for_methods',
        'paid_for_methods',
        'zero_value_invoices',
        'open_invoices',
        'customer_link',
        'line_description',
        'article_name_shipping',
        'article_number_shipping',
        'order_number_prefix',
        'order_number_suffix',
        'invoice_email',
        'email_to_append_to',
        'email_for_states',
        'email_for_methods',
        'email_filename',
        'email_copy',
        'email_subject',
        'email_content_text',
        'email_content_html',
    );

    /**
     * Decide if the shop url setting has been set.
     *
     * @return bool
     */
    public function has_shop_url()
    {
        return ! $this->is_empty( 'shop_url' );
    }

    /**
     * Set the shop URL setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_shop_url ( $value )
    {
        $this->set( 'shop_url' , $value );
    }

    /**
     * Get the shop url to use for authentication.
     *
     * @return string|null
     */
    public function get_shop_url()
    {
        return trim( $this->get( 'shop_url', get_home_url() ) );
    }

    /**
     * Decide if the shop token setting has been set.
     *
     * @return bool
     */
    public function has_shop_token()
    {
        return ! $this->is_empty( 'shop_token' );
    }

    /**
     * Set the shop token setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_shop_token ( $value )
    {
        $this->set( 'shop_token' , $value );
    }

    /**
     * Get the shop token to use for authentication.
     *
     * @return string|null
     */
    public function get_shop_token()
    {
        return trim( $this->get( 'shop_token', '' ) );
    }

    /**
     * Decide if both the shop url and the shop token have been set.
     *
     * @return bool
     */
    public function has_shop_credentials()
    {
        return $this->has_shop_url() && $this->has_shop_token();
    }

    /**
     * Set the create invoices setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_create_invoices ( $value )
    {
        $this->set( 'create_invoices' ,  $value ? 'yes' : 'no' );
    }

    /**
     * Decide if the creation of invoices is active.
     *
     * @return bool
     */
    public function get_create_invoices()
    {
        return $this->get( 'create_invoices', 'no') === 'yes';
    }

    /**
     * Set the create invoices setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_invoice_for_states ( $value )
    {
        $this->set( 'invoice_for_states', is_array( $value ) ? $value : array() );
    }

    /**
     * Get the order states for which invoices are to be created automatically.
     *
     * @return array
     */
    public function get_invoice_for_states()
    {
        return $this->get( 'invoice_for_states', array() );
    }

    /**
     * Decide if an invoice is to be created for a particular order state.
     *
     * @param  string $state
     * @return bool
     */
    public function create_invoice_for_state( $state )
    {
        return in_array( $state, $this->get_invoice_for_states() );
    }

    /**
     * Set the create invoices for methods setting.
     *
     * @param  array $value
     * @return void
     */
    public function set_no_invoice_for_methods ( $value )
    {
        $this->set( 'no_invoice_for_methods' , is_array( $value ) ? $value : array() );
    }

    /**
     * Get the payment methods for which invoices are to be created automatically.
     *
     * @return array
     */
    public function get_no_invoice_for_methods()
    {
        return $this->get( 'no_invoice_for_methods' , array() );
    }

    /**
     * Set the invoices paid for payment methods setting.
     *
     * @param  array $value
     * @return void
     */
    public function set_paid_for_methods( $value )
    {
        $this->set( 'paid_for_methods' , is_array( $value ) ? $value : array() );
    }

    /**
     * Get the invoices paid for payment methods setting.
     *
     * @param  array $value
     * @return array
     */
    public function get_paid_for_methods()
    {
        return $this->get( 'paid_for_methods', array() );
    }

    /**
     * Decide if to mark an invoice as paid for a given status.
     *
     * @param  string $status
     * @return bool
     */
    public function mark_invoice_as_paid( $method )
    {
        return in_array( $method, $this->get_paid_for_methods() );
    }

    /**
     * Decide if an invoice is to be created for a particular payment method.
     *
     * @param  string $method
     * @return bool
     */
    public function create_invoice_for_method( $method )
    {
        return ! in_array( $method, $this->get_no_invoice_for_methods() );
    }

    /**
     * Set the cancel invoices setting.
     *
     * @param  bool $value
     * @return void
     */
    public function set_cancel_invoices( $value )
    {
        $this->set( 'cancel_invoices',  $value ? 'yes' : 'no' );
    }

    /**
     * Decide if the cancellation of invoices is active.
     *
     * @return bool
     */
    public function get_cancel_invoices()
    {
        return $this->get( 'cancel_invoices', 'no' ) === 'yes';
    }

    /**
     * Set the create zero value invoices setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_zero_value_invoices( $value )
    {
        $this->set( 'zero_value_invoices' , $value ? 'yes' : 'no' );
    }

    /**
     * Decide if invoices are to be created if they are of zero value.
     *
     * @return bool
     */
    public function get_zero_value_invoices()
    {
        return $this->get( 'zero_value_invoices', 'no' ) === 'yes';
    }

    /**
     * Decide if invoices are to be created for a given value.
     *
     * @return bool
     */
    public function create_invoice_for_value( $value )
    {
        return (floatval($value) != 0.0) || $this->get_zero_value_invoices();
    }

    /**
     * Decide if invoices are to be created for given parameters.
     *
     * @return bool
     */
    public function create_invoice_for( $status, $method, $value )
    {
        $result = $this->create_invoice_for_state( $status );
        $result &= $this->create_invoice_for_method( $method );
        $result &= $this->create_invoice_for_value( $value );
        return $result;
    }

    /**
     * Set the open invoices setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_open_invoices( $value )
    {
        $this->set( 'open_invoices', $value ? 'yes' : 'no' );
    }

    /**
     * Decide if invoices are to be opened instead of downloaded.
     *
     * @return bool
     */
    public function get_open_invoices()
    {
        return $this->get( 'open_invoices', 'no' ) === 'yes';
    }

    /**
     * Set the customer link setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_customer_link( $value )
    {
        $this->set( 'customer_link' , $value ? 'yes' : 'no' );
    }

    /**
     * Decide if ithe invoice is to be downloadable for the customer.
     *
     * @return bool
     */
    public function get_customer_link()
    {
        return $this->get( 'customer_link', 'no' ) === 'yes';
    }

    /**
     * Get the line description setting .
     *
     * @return string
     */
    public function get_line_description()
    {
        return $this->get( 'line_description' );
    }

    /**
     * Set the line description setting .
     *
     * @param  string $value
     * @return void
     */
    public function set_line_description( $value )
    {
        $this->set( 'line_description', $value );
    }

    /**
     * Set article name for shipping setting .
     *
     * @param  string $value
     * @return void
     */
    public function set_article_name_shipping( $value )
    {
        $this->set( 'article_name_shipping' , $value );
    }

    /**
     * Get the article name for shipping items
     *
     * @return string|null
     */
    public function get_article_name_shipping()
    {
        return $this->get( 'article_name_shipping' );
    }

    /**
     * Set article number for shipping setting .
     *
     * @param  string $value
     * @return void
     */
    public function set_article_number_shipping( $value )
    {
        $this->set( 'article_number_shipping' , $value );
    }

    /**
     * Get the article number for shipping items
     *
     * @return string|null
     */
    public function get_article_number_shipping()
    {
        return $this->get( 'article_number_shipping' );
    }

    /**
     * Set the order number prefix setting .
     *
     * @param  string $value
     * @return void
     */
    public function set_order_number_prefix( $value )
    {
        $this->set( 'order_number_prefix' , $value );
    }

    /**
     * Get the order number prefix for order invoices.
     *
     * @return string|null
     */
    public function get_order_number_prefix()
    {
        return $this->get( 'order_number_prefix' );
    }

    /**
     * Set the order number suffix setting .
     *
     * @param  string $value
     * @return void
     */
    public function set_order_number_suffix( $value )
    {
        $this->set( 'order_number_suffix' , $value );
    }

    /**
     * Get the order number suffix for order invoices.
     *
     * @return string|null
     */
    public function get_order_number_suffix()
    {
        return $this->get( 'order_number_suffix' );
    }

    /**
     * Add prefix and suffix to a given order number.
     *
     * @return string
     */
    public function get_order_number($number)
    {
        $prefix = $this->get_order_number_prefix();
        $suffix = $this->get_order_number_suffix();
        $result = $prefix . $number . $suffix;
        return $result;
    }

    /**
     * Set the invoice_email setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_invoice_email( $value )
    {
        $this->set( 'invoice_email' , $value );
    }

    /**
     * Get the invoice_email setting.
     *
     * @param  string $value
     * @return void
     */
    public function get_invoice_email( $value )
    {
        return $this->get( 'invoice_email' );
    }

    /**
     * Decide if invoices are to be created on order status 'completed'.
     *
     * @return bool
     */
    public function append_invoice_to_email()
    {
        return $this->get( 'invoice_email', 'append' ) === 'append';
    }

    /**
     * Decide if invoices are to be created on order status 'completed'.
     *
     * @return bool
     */
    public function send_invoice_as_email()
    {
        return $this->get( 'invoice_email', 'append' ) === 'separate';
    }

    /**
     * Set the setting to which email append invoices to.
     *
     * @param  string $value
     * @return void
     */
    public function set_email_to_append_to( $value )
    {
        $this->set( 'email_to_append_to' , $value );
    }

    /**
     * Get the setting to which email append invoices to.
     *
     * @return array
     */
    public function get_email_to_append_to()
    {
        $value = $this->get( 'email_to_append_to', array('customer_processing_order') );
        if (!is_array($value)) {
            $value = array($value);
        }
        return $value;
    }

    /**
     * Decide whether to append invoice to certain email type.
     *
     * @return bool
     */
    public function append_invoice_to_email_type( $value )
    {
        return in_array($value, $this->get_email_to_append_to());
    }

    /**
     * Set the email for states setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_email_for_states( $value )
    {
        $this->set( 'email_for_states' , $value );
    }

    /**
     * Get the status for which invoices are to be sent as an email.
     *
     * @return array
     */
    public function get_email_for_states()
    {
        return $this->get( 'email_for_states', array() );
    }

    /**
     * Decide if to send an email for a particular order status.
     *
     * @return bool
     */
    public function send_email_for_state( $state )
    {
        return in_array( $state, $this->get_email_for_states() );
    }

    /**
     * Set the email for methods setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_no_email_for_methods( $value )
    {
        $this->set( 'no_email_for_states', $value );
    }

    /**
     * Get the payment methods for which invoices are to be sent as an email.
     *
     * @return array
     */
    public function get_no_email_for_methods()
    {
        return $this->get( 'no_email_for_methods', array() );
    }

    /**
     * Decide if to send an email for a particular payment method.
     *
     * @param  string $method
     * @return bool
     */
    public function send_email_for_method( $method )
    {
        return ! in_array( $method, $this->get_no_email_for_methods() );
    }

    /**
     * Decide if to send an email for a given parameters.
     *
     * @param  string $state
     * @param  string $method
     * @return bool
     */
    public function send_email_for( $state, $method )
    {
        $result = $this->send_email_for_state( $state );
        $result &= $this->send_email_for_method( $method );
        return $result;
    }

    /**
     * Set the email filename setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_email_filename( $value )
    {
        $this->set( 'email_filename', $value );
    }

    /**
     * Get the name of the file that is to be sent via email.
     *
     * @return string|null
     */
    public function get_email_filename()
    {
        return $this->get( 'email_filename', 'Rechnung' );
    }

    /**
     * Set the email copy setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_email_copy( $value )
    {
        $this->set( 'email_copy', $value );
    }

    /**
     * Get the carbon copy of the email to be sent to the customer.
     *
     * @return string|null
     */
    public function get_email_copy()
    {
        return $this->get( 'email_copy' );
    }

    /**
     * Return an array of recipients from the email copy section.
     *
     * @return array
     */
    public function send_email_copies_to()
    {
        return explode( ',', trim( $this->get_email_copy() ) );
    }

    /**
     * Set the email filename setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_email_subject( $value )
    {
        $this->set( 'email_subject', $value );
    }

    /**
     * Get the subject of the email to be sent to the customer.
     *
     * @return string|null
     */
    public function get_email_subject()
    {
        return $this->get( 'email_subject' );
    }

    /**
     * Set the email content text setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_email_content_text( $value )
    {
        $this->set( 'email_content_text', $value );
    }

    /**
     * Get the text content of the email to be sent to the customer.
     *
     * @return string|null
     */
    public function get_email_content_text()
    {
        return $this->get( 'email_content_text', '' );
    }

    /**
     * Set the email filename setting.
     *
     * @param  string $value
     * @return void
     */
    public function set_email_content_html( $value )
    {
        $this->set('email_content_html', WR_Plugin::encode_html_content($value));
    }

    /**
     * Get the html content of the email to be sent to the customer.
     *
     * @return string|null
     */
    public function get_email_content_html()
    {
        return WR_Plugin::decode_html_content($this->get('email_content_html', ''));
    }
}

endif;
