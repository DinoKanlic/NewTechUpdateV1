<?php

if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Client_Error' ) ):

/**
 * WooRechnung Client Error Class
 *
 * This exception is thrown if the HTTP client is not capable to send
 * requests to the server application. This type of error is not to be
 * mistaken with the request error, which signals an erroneous HTTP
 * response returned from the server application.
 *
 * @class    WR_Client_Error
 * @version  1.0.0
 * @package  WooRechnung\Errors
 * @author   Zweischneider
 */
final class WR_Client_Error extends WR_Abstract_Error
{
    /**
     * The default error message for this exception type.
     *
     * @var string
     */
    const ERROR_MESSAGE = 'WooRechnung client-side error';

    /**
     * Create a new instance of this error.
     *
     * @param  int    $code
     * @return void
     */
    public function __construct( $code = 0 )
    {
        parent::__construct(self::ERROR_MESSAGE, $code);
    }

    /**
     * Render this error to a printable title and message.
     *
     * @return array
     */
    public function render_error()
    {
        switch ($this->getCode())
        {
            case CURLE_COULDNT_CONNECT: return $this->error_connection(); break;
            case CURLE_SSL_CONNECT_ERROR: return $this->error_ssl_connection(); break;
            case CURLE_OPERATION_TIMEDOUT: return $this->error_timeout(); break;
            default: return $this->error_unknown_reasons(); break;
        }
    }

    /**
     * Render an error concerning a connection problem.
     *
     * @return array
     */
    public function error_connection()
    {
        $title = __('Verbindungsfehler', 'woorechnung');
        $message = __('Es konnte keine Verbindung zur Serveranwendung hergestellt werden. Bitte überprüfe deine Internetverbindung.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an error concerning an ssl connection problem.
     *
     * @return array
     */
    public function error_ssl_connection()
    {
        $title = __('SSL-Fehler', 'woorechnung');
        $message = __('Es konnte keine sichere Verbindung über SSL hergestellt werden. Bitte überprüfe deine Serverkonfiguration.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an error concerning a a request timeout.
     *
     * @return array
     */
    public function error_timeout()
    {
        $title = __('Timeout der Anfrage', 'woorechnung');
        $message = __('Die Anfrage an den Server hat zu lange gedauert. Bitte versuche es erneut oder erhöhe das Timeout-Limit.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an error with an unknown reason.
     *
     * @return array
     */
    public function error_unknown_reasons()
    {
        $title = __('Ursache unklar', 'woorechnung');
        $message = __('Es ist zu einem unbekannten Fehler gekommen. Bitte kontaktiere den Support, wenn das Problem weiterhin besteht.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }
}

endif;
