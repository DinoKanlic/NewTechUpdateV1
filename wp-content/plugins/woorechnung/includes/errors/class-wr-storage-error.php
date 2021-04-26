<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Storage_Error' ) ):

/**
 * WooRechnung Storage Error Class
 *
 * @version  1.0.0
 * @package  WooRechnung\Errors
 * @author   Zweischneider
 */
final class WR_Storage_Error extends WR_Abstract_Error
{
    const READ_DIR_ERROR = 0;
    const WRITE_DIR_ERROR = 1;
    const READ_FILE_ERROR = 2;
    const WRITE_FILE_ERROR = 3;

    public function render_error()
    {
        switch( $this->getCode() )
        {
            case self::READ_DIR_ERROR: return $this->error_read_dir(); break;
            case self::WRITE_DIR_ERROR: return $this->error_write_dir(); break;
            case self::READ_FILE_ERROR: return $this->error_read_file(); break;
            case self::WRITE_FILE_ERROR: return $this->error_write_file(); break;
            default: return $this->error_unknown(); break;
        }
    }

    private function error_read_dir()
    {
        $title = _('Ordner Lesefehler', 'woorechnung');
        $message = _('Der Ordner konnte nicht gelesen werden.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    private function error_write_dir()
    {
        $title = _('Ordner Schreibfehler', 'woorechnung');
        $message = _('Der Ordner konnte nicht erstellt werden.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    private function error_read_file()
    {
        $title = _('Datei Lesefehler', 'woorechnung');
        $message = _('Die Datei konnte nicht gelesen werden.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    private function error_write_file()
    {
        $title = _('Datei Schreibfehler', 'woorechnung');
        $message = _('Die Datei konnte nicht erstellt werden.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

    private function error_unknown()
    {
        $title = _('Ursache unklar', 'woorechnung');
        $message = _('Es ist zu einem Fehler mit unbekannter Ursache gekommen. Bitte wende dich an den Support.', 'woorechnung');
        return array('title' => $title, 'message' => $message);
    }

}

endif;
