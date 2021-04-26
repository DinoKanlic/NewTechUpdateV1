<?php

if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Export_Error' ) ):

/**
 * WooRechnung Export Error Class
 *
 * @class    WR_Export_Error
 * @version  1.0.0
 * @package  WooRechnung\Errors
 * @author   Zweischneider
 */
final class WR_Export_Error extends WR_Abstract_Error
{
    /**
     * The default error message for this exception type.
     *
     * @var string
     */
    const ERROR_MESSAGE = 'WooRechnung bulk-export error';

    /**
     * Create a new instance of this error.
     *
     * @return void
     */
    public function __construct($status = 0)
    {
        parent::__construct(self::ERROR_MESSAGE, $status);
    }

    /**
     * Render this error to a printable title and message.
     *
     * @return array
     */
    public function render_error()
    {
        $status = $this->getCode();
        $title = __("Export fehlgeschlagen (Fehler {$status})", 'woorechnung');
        $message = __('Die Rechnungen konnten nicht exportiert werden.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }
}

endif;
