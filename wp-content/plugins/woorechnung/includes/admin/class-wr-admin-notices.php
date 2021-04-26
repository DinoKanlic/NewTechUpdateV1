<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Admin_Notices' ) ):

/**
 * WooRechnung Admin Notices Class
 *
 * @version  1.0.0
 * @package  WooRechnung\Admin
 * @author   Zweischneider
 */
final class WR_Admin_Notices extends WR_Abstract_Module
{
    /**
     * Initialize the hooks of this module.
     *
     * @return void
     */
    public function init_hooks()
    {
        if ( ! $this->plugin()->is_woocommerce_active() ) {
            // $this->add_action('admin_notices', 'show_notice_woocommerce');
        }

        if ( ! $this->plugin()->is_germanized_active() ) {
            // $this->add_action('admin_notices', 'show_notice_germanized');
        }

        if ( ! $this->settings()->has_shop_credentials() ) {
            $this->add_action('admin_notices', 'show_notice_credentials');
        }

        $this->add_action('admin_notices', 'show_notice_config');
    }

    /**
     * Return a HTML string that is to be shown as an admin notice.
     *
     * @param  string $message
     * @param  string $type
     * @param  bool   $is_dismissible
     * @return string
     */
    private static function notice($message, $type, $is_dismissible = true)
    {
        $partials = array();
        $partials[] = "<div class='notice {$type}".($is_dismissible ? " is-dismissible" : '')."'>";
        $partials[] = "<p><strong>{$message}</strong></p>";
        if ($is_dismissible) {
            $partials[] = "<button type='button' class='notice-dismiss'>";
            $partials[] = "<span class='screen-reader-text'>Dismiss this notice.</span>";
            $partials[] = "</button>";
        }
        $partials[] = "</div>";
        $result = join('', $partials);
        return $result;
    }

    /**
     * Return a HTML string that is to be shown as a success message.
     *
     * @param  string $message
     * @param  bool   $is_dismissible
     * @return string
     */
    private static function notice_success($message, $is_dismissible = true)
    {
        return self::notice($message, 'notice-success', $is_dismissible);
    }

    /**
     * Return a HTML string that is to be shown as an error message.
     *
     * @param  string $message
     * @param  bool   $is_dismissible
     * @return string
     */
    private static function notice_error($message, $is_dismissible = true)
    {
        return self::notice($message, 'notice-error', $is_dismissible);
    }

    /**
     * Return a HTML string that is to be shown as a warning.
     *
     * @param  string $message
     * @param  bool   $is_dismissible
     * @return string
     */
    private static function notice_warning($message, $is_dismissible = true)
    {
        return self::notice($message, 'notice-warning', $is_dismissible);
    }

    /**
     * Return a HTML string that is to be shown as a piece of information.
     *
     * @param  string $message
     * @param  bool   $is_dismissible
     * @return string
     */
    private static function notice_information($message, $is_dismissible = true)
    {
        return self::notice($message, 'notice-info', $is_dismissible);
    }

    /**
     * Show an error message to signify that WooCommerce is inactive.
     *
     * @return void
     */
    public function show_notice_woocommerce()
    {
        $message = 'WooRechnung: bitte aktiviere WooCommerce, um WooRechnung zu nutzen.';
        $notice = self::notice_error($message);
        echo $notice;
    }

    /**
     * Show an informational notice recommending Germanized.
     *
     * @return void
     */
    public function show_notice_germanized()
    {
        $href = "https://de.wordpress.org/plugins/woocommerce-germanized/";
        $target = "<a href='{$href}' target='_BLANK'>WooCommerce Germanized</a>";
        $message = "WooRechnung: wir empfehlen die Verwendung von {$target}!";
        $notice = self::notice_information($message);
        echo $notice;
    }

    /**
     * Show an error message to signify that the credentials are missing.
     *
     * @return void
     */
    public function show_notice_credentials()
    {
        $href = admin_url('/admin.php?page=wc-settings&tab=woorechnung');
        $target = "<a href='{$href}'>(hier konfigurieren)</a>";
        $message = "WooRechnung: Zugangsdaten noch nicht eingegeben {$target}.";
        $notice = self::notice_error($message);
        echo $notice;
    }

    /**
     * Show an warning message if specific config settings are made.
     * 
     * @return void
     */
    public function show_notice_config()
    {
        $is_enabled = [
            'debugging' => $this->plugin()->is_debugging_enabled(),
            'logging' => $this->plugin()->is_logging_enabled(),
            'verbose' => $this->plugin()->is_logging_verbose()
        ];
        if (in_array(true, $is_enabled)) {
            $message = $this->plugin()->get_name().' '.implode(', ', array_keys($is_enabled, true)).' ist aktiv.';
            $notice = self::notice_warning($message, false);
            echo $notice;
        }
    }

}

endif;
