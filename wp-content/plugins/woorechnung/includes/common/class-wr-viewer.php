<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WR_Viewer' ) ):

/**
 * WooRechnung PDF Viewer Component
 *
 * @version  1.0.0
 * @package  WooRechnung\Common
 * @author   Zweischneider
 */
final class WR_Viewer
{
    /**
     * The plugin instance via dependency injection.
     *
     * @var WR_Plugin
     */
    private $_plugin;

    /**
     * Create a new instance of this viewer class.
     *
     * @param  WR_Plugin $plugin
     * @return void
     */
    public function __construct(WR_Plugin $plugin)
    {
        $this->_plugin = $plugin;
    }

    /**
     * View a PDF file, given its key and base64 data.
     *
     * @param string $key
     * @param string $data
     * @return void
     */
    public function view_pdf($key, $data)
    {
        if ( $this->_plugin->get_settings()->get_open_invoices() ) {
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="'.$key.'.pdf"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            echo base64_decode($data);
        } else {
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="' . $key . '.pdf"');
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Connection: keep-alive');
            header('Expires: 0');
            header('Pragma: public');
            echo base64_decode($data);
        }
    }
}

endif;
