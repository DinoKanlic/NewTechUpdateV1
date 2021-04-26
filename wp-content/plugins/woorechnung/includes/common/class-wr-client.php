<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WR_Client' ) ):

/**
 * WooRechnung HTTP Client Component
 *
 * @class    WR_Client
 * @extends  WR_Abstract_Client
 * @version  1.0.0
 * @package  WooRechnung\Common
 * @author   Zweischneider
 */
final class WR_Client extends WR_Abstract_Client
{
    /**
     * Download invoice as PDF.
     *
     * Retrieve an invoice given its key. A base64 encoded
     * string of the invoice pdf is returned from the server.
     *
     * @param  string $key
     * @return array
     */
    public function get_invoice( $key )
    {
        return $this->send_get( '/shop/invoices/' . $key );
    }

    /**
     * Create a new invoice from the order data.
     *
     * @param  object $data
     * @return array
     */
    public function create_invoice( $data )
    {
        return $this->send_post( '/shop/invoices', $data );
    }

    /**
     * Complete an invoice, given its key.
     *
     * @param  string $key
     * @return array
     */
    public function complete_invoice( $key )
    {
        return $this->send_put( '/shop/invoices/' . $key . '/complete' );
    }

    /**
     * Cancel an invoice, given its key.
     *
     * @param  string $key
     * @return array
     */
    public function cancel_invoice( $key )
    {
        return $this->send_put( '/shop/invoices/' . $key . '/cancel' );
    }

    /**
     * Refund an invoice, given its it and data.
     *
     * @param  string $key
     * @param  object $data
     * @return array
     */
    public function refund_invoice( $key, $data )
    {
        return $this->send_put( '/shop/invoices/' . $key . '/refund' );
    }
}

endif;
