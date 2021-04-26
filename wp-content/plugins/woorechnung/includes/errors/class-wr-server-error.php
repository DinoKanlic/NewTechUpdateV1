<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Server_Error' ) ):

/**
 * WooRechnung Server Error Class
 *
 * @version  1.0.0
 * @package  WooRechnung\Errors
 * @author   Zweischneider
 */
final class WR_Server_Error extends WR_Abstract_Error
{
    /**
     * The default error message for this exception type.
     *
     * @var string
     */
    const ERROR_MESSAGE = 'WooRechnung server-side error';

    /**
     * The trace id the client generates for a request.
     *
     * @var string
     */
    private $_trace_id;

    /**
     * Create a new instance of this error.
     *
     * @param  string $body
     * @param  int    $code
     * @return void
     */
    public function __construct($body, $code)
    {
        parent::__construct($body, $code);
    }

    /**
     * Set the trace id for this error instance.
     *
     * @param  string $_trace_id
     * @return void
     */
    public function set_trace_id($_trace_id)
    {
        $this->_trace_id = $_trace_id;
    }

    /**
     * Get the trace id for this error instance.
     *
     * @return string
     */
    public function get_trace_id()
    {
        return $this->_trace_id;
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
            case 400: return $this->error_bad_request(); break;
            case 401: return $this->error_authentication_failed(); break;
            case 402: return $this->error_upgrade_required(); break;
            case 403: return $this->error_access_forbidden(); break;
            case 404: return $this->error_resource_not_found(); break;
            case 415: return $this->error_unsupported_media_type(); break;
            case 422: return $this->error_unprocessable_entity(); break;
            case 429: return $this->error_too_many_requests(); break;
            case 500: return $this->error_server_internal(); break;
            case 501: return $this->error_method_not_implemented(); break;
            case 503: return $this->error_server_unavailable(); break;
            default:  return $this->error_unknown_reasons(); break;
        }
    }

    /**
     * Render an error concerning missing configuration.
     *
     * @return array
     */
    private function error_bad_request()
    {
        $title = __('Fehlende Konfiguration (Fehler 400)', 'woorechnung');
        $message = __('Bitte überprüfe, ob du deinen Rechnungsanbieter vollständig konfiguriert hast.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an error concerning the API authentication.
     *
     * @return array
     */
    private function error_authentication_failed()
    {
        $title = __('Authentifizierung fehlgeschlagen (Fehler 401)', 'woorechnung');
        $message = __('Bitte überprüfe, ob der in den Einstellungen festgelegte Shop-Key korrekt ist.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an error concerning the invoice limit.
     *
     * @return array
     */
    private function error_upgrade_required()
    {
        $title = __('Upgrade erforderlich (Fehler 402)', 'woorechnung');
        $message = __('Du kannst in diesem Monat keine weiteren Rechnungen mehr erstellen, da dein Limit erreicht ist.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an error concerning the authorization of a resource.
     *
     * @return array
     */
    private function error_access_forbidden()
    {
        $title = __('Autorisierung fehlgeschlagen (Fehler 403)', 'woorechnung');
        $message = __('Du hast keine Berechtigung, auf diese Rechnung zuzugreifen', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an error concerning the processing of a resource.
     *
     * Possible reasons for this errors are:
     * - the validation of the request data fails on the server side
     * - the connection to the invoice provider cannot be established
     * - the request to the invoice provider fails
     *
     * @return array
     */
    private function error_unprocessable_entity()
    {
        $title = __('Verarbeitung fehlgeschlagen (Fehler 422)', 'woorechnung');
        $message = '<p>Die Verarbeitung der Anfrage ist fehlgeschlagen. Bitte überprüfe die folgenden möglichen Fehlerursachen:</p>';
        $message .= '<ul>';
        $message .= '<li>Verfügt die Bestellung über alle notwendigen Daten zur Rechnungserstellung?</li>';
        $message .= '<li>Hast du die Steuern in deinem Onlineshop korrekt konfiguriert?</li>';
        $message .= '<li>Hast du die Verbindung zu deinem Rechnungsanbieter erfolgreich hergestellt?</li>';
        $message .= '<li>Kann dein Rechnungsanbieter alle Angaben in deiner Bestellung verarbeiten?</li>';
        $message .= '</ul>';
        $message .= '<p>Falls dir die Ursache des Problem unklar sein sollte, wende dich gerne an unseren Support.</p>';
        $message = __($message, 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render a too many requests error to the user.
     *
     * @return array
     */
    private function error_too_many_requests()
    {
        $title = __('Zu viele Anfragen (Fehler 429)', 'woorechnung');
        $message = __('Du hast zu viele Anfragen in zu kurzer Zeit an WooRechnung gesendet. Bitte warte einen Moment undversuche es dann erneut.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an internal server error to the user.
     *
     * @return array
     */
    private function error_server_internal()
    {
        $title = __('Fehler der Serveranwendung (Fehler 500)', 'woorechnung');
        $message = __('Es ist zu einem unerwarteten Fehler der Serveranwendung gekommen. Bitte wende dich an den Support.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an internal server error to the user.
     *
     * @return array
     */
    private function error_server_unavailable()
    {
        $title = __('Server nicht erreichbar (Fehler 503)', 'woorechnung');
        $message = __('Die Serveranwendung ist temporär nicht erreichbar. Bitte versuche es später noch einmal.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render an error concerning a missing implementation.
     *
     * @return array
     */
    private function error_method_not_implemented()
    {
        $title = __('Nocht nicht verfügbar (Fehler 501)', 'woorechnung');
        $message = __('Diese Funktion steht aktuell noch nicht zur Verfügung, wird aber in Zukunft umgesetzt.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render the error that a resource could not be found.
     *
     * @return array
     */
    private function error_resource_not_found()
    {
        $title = __('Rechnung nicht gefunden (Fehler 404)', 'woorechnung');
        $message = __('Die Rechnung wurde nicht gefunden. Bist du sicher, dass diese Rechnung existiert?', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }


    /**
     * Render the error that the requested media type is not supported.
     *
     * @return array
     */
    private function error_unsupported_media_type()
    {
        $title = __('Medientyp nicht unterstützt (Fehler 415)', 'woorechnung');
        $message = __('Der angeforderte Medientyp wird von WooRechnung nicht unterstützt.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    /**
     * Render anything else that might potentially happen but should not.
     *
     * @return array
     */
    private function error_unknown_reasons()
    {
        $title = __('Ursache unklar', 'woorechnung');
        $message = __('Es ist zu einem Fehler mit unbekannter Ursache gekommen. Bitte wende dich an den Support.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }
}

endif;
