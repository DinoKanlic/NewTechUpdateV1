<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Plugin' ) ):

/**
 * WooRechnung Plugin Class
 *
 * @version  2.0.0
 * @package  WooRechnung
 * @author   Zweischneider
 */
final class WR_Plugin extends WR_Abstract_Plugin
{
    /**
     * The name of this class since self::class is not supported.
     *
     * @var string
     */
    const PLUGIN_CLASS = 'WR_Plugin';

    /**
     * The name of the this plugin.
     *
     * @var string
     */
    const PLUGIN_NAME = 'WooRechnung';

    /**
     * The text domain of this plugin.
     *
     * @var string
     */
    const TEXT_DOMAIN = 'WooRechnung';

    /**
     * The settings prefix for this plugin.
     *
     * @var string
     */
    const SETTINGS_PREFIX = 'woorechnung_';

    /**
     * The user agent name for this plugin.
     *
     * @var string
     */
    const USER_AGENT = 'woorechnung-wc-plugin';

    /**
     * String representing development environment.
     *
     * @var string
     */
     const ENV_DEVELOPMENT = 'development';

    /**
     * String representing production environment;
     *
     * @var string
     */
    const ENV_PRODUCTION = 'production';

    /**
     * The path of the WooCommerce plugin.
     *
     * @var string
     */
    const PLUGIN_WOOCOMMERCE = 'woocommerce/woocommerce.php';

    /**
     * The path of the WooCommerce Germanized plugin.
     *
     * @var string
     */
    const PLUGIN_GERMANIZED = 'woocommerce-germanized/woocommerce-germanized.php';


    /**
     * The hook loader instance to register any WordPress hooks.
     *
     * @var WR_Loader
     */
    private $_loader;

    /**
     * The logger instance to log plugin events.
     *
     * @var WR_Logger
     */
    private $_logger;

    /**
     * The API client to use for making requests to the API.
     *
     * @var WR_Client
     */
    private $_client;

    /**
     * The invoice factory instance to create invoices.
     *
     * @var WR_Factory
     */
    private $_factory;

    /**
     * The mailer instance to send emails.
     *
     * @var WR_Mailer
     */
    private $_mailer;

    /**
     * The plugin settings instance.
     *
     * @var WR_Settings
     */
    private $_settings;

    /**
     * The plugin storage instance.
     *
     * @var WR_Storage
     */
    private $_storage;

    /**
     * The plugin PDF viewer instance.
     *
     * @var WR_Viewer
     */
    private $_viewer;

    /**
     * The plugin error handler instance.
     *
     * @var WR_Handler
     */
    private $_handler;

    /**
     * Create a new instance of the plugin.
     *
     * @param  string $plugin_file
     * @param  string $plugin_path
     * @param  string $plugin_url
     * @return void
     */
    public function __construct($plugin_file, $plugin_path, $plugin_url)
    {
        $this->set_file($plugin_file);
        $this->set_path($plugin_path);
        $this->set_url($plugin_url);
        $this->load_commons();
        $this->load_modules();
    }

    /**
     * Get the name of this plugin.
     *
     * @return string
     */
    public function get_name()
    {
        return self::PLUGIN_NAME;
    }

    /**
     * Get the current version of this plugin.
     *
     * @return string
     */
    public function get_version()
    {
        if (!function_exists('get_plugin_data')) {
            require_once(ABSPATH.'wp-admin/includes/plugin.php');
        }
        if ($plugin_data = get_plugin_data($this->get_file())) {
            return !empty($plugin_data['Version']) ? $plugin_data['Version'] : '';
        }
        return '';
    }

    /**
     * Get the settings prefox string of this plugin.
     *
     * @return string
     */
    public function get_settings_prefix()
    {
        return self::SETTINGS_PREFIX;
    }

    /**
     * Get the user agent for this plugin.
     *
     * @return string
     */
    public function get_user_agent()
    {
        $version = $this->get_version();
        return self::USER_AGENT.(!empty($version) ? '/'.$version : '');
    }

    /**
     * Get the text domain of this plugin.
     *
     * @return string
     */
    public function get_text_domain()
    {
        return self::TEXT_DOMAIN;
    }

    /**
     * Get the URI of the server application.
     *
     * @return string
     */
    public function get_server_uri()
    {
        return WOORECHNUNG_SERVER_URI;
    }

    /**
     * Get the email address of the support.
     *
     * @return string
     */
    public function get_support_email()
    {
        return WOORECHNUNG_SUPPORT_EMAIL;
    }

    /**
     * Get the current environment.
     *
     * Possible values are 'development' and 'production'.
     *
     * @return string
     */
    public function get_environment()
    {
        return WOORECHNUNG_ENVIRONMENT;
    }

    /**
     * Decide if a particular environment is present.
     *
     * @return bool
     */
    public function has_environment($environment)
    {
        return $this->get_environment() == $environment;
    }

    /**
     * Check if the plugin is run in a development environment.
     *
     * @return bool
     */
    public function is_env_development()
    {
        return $this->has_environment(self::ENV_DEVELOPMENT);
    }

    /**
     * Check if the plugin is run in a production environment.
     *
     * @return bool
     */
    public function is_env_production()
    {
        return $this->has_environment(self::ENV_PRODUCTION);
    }

    /**
     * Decide if debugging mode is enabled by configuration.
     *
     * @return bool
     */
    public function is_debugging_enabled()
    {
        return WOORECHNUNG_DEBUGGING_ENABLED;
    }

    /**
     * Decide if logging is enabled by configuration.
     *
     * @return bool
     */
    public function is_logging_enabled()
    {
        return WOORECHNUNG_LOGGING_ENABLED;
    }

    /**
     * Decide if verbose logging is enabled by configuration.
     *
     * @return bool
     */
    public function is_logging_verbose()
    {
        if (!defined('WOORECHNUNG_LOGGING_VERBOSE')) {
            return false;
        }
        return WOORECHNUNG_LOGGING_VERBOSE;
    }

    /**
     * Return the name of the log file from configuration.
     *
     * @return string
     */
    public function get_logging_filename()
    {
        return WOORECHNUNG_LOGGING_FILENAME;
    }

    /**
     * Return the variable names and descriptions for the invoice filename.
     *
     * @return array
     */
    public function get_invoice_filename_variables()
    {
        return array(
            'order_no' => 'Nummer der Bestellung',
            'invoice_no' => 'Rechnungsnummer',
            'invoice_date_day' => 'Tag des Rechnungsdatum',
            'invoice_date_month' => 'Monat des Rechnungsdatum',
            'invoice_date_year' => 'Jahr des Rechnungsdatum',
            'company' => 'Firmenname des Kunden',
            'first_name' => 'Vorname des Kunden',
            'last_name' => 'Nachname des Kunden'
        );
    }


    /**
     * Get the path for the directory for temporary files.
     *
     * @return string
     */
    public function get_temp_path( $append = '' )
    {
        return $this->get_path("temp/{$append}");
    }

    /**
     * Get the path for the invoice PDF files.
     *
     * @return string
     */
    public function get_invoices_path( $file = '' )
    {
        return $this->get_temp_path("invoices/{$file}");
    }

    /**
     * Get the path for the bulk export files.
     *
     * @return string
     */
    public function get_exports_path( $file = '' )
    {
        return $this->get_temp_path("exports/{$file}");
    }

    /**
     * Get the hook loader instance to register actions and filters.
     *
     * @return WR_Loader
     */
    public function get_loader()
    {
        return $this->_loader;
    }

    /**
     * Get the logger instance to log plugin events.
     *
     * @return WR_Logger
     */

    public function get_logger()
    {
        return $this->_logger;
    }

    /**
     * Get the HTTP client to make external requests.
     *
     * @return WR_Client
     */
     public function get_client()
     {
        return $this->_client;
     }

    /**
     * Get the invoice factory to create invoices.
     *
     * @return WR_Factory
     */
     public function get_factory()
     {
        return $this->_factory;
     }

    /**
     * Get the plugin mailer instance.
     *
     * @return WR_Mailer
     */
    public function get_mailer()
    {
        return $this->_mailer;
    }

    /**
     * Get the plugin settings instance.
     *
     * @return WR_Settings
     */
    public function get_settings()
    {
        return $this->_settings;
    }

    /**
     * Get the plugin options instance.
     *
     * @return WR_Options
     */
    public function get_options()
    {
        return $this->_options;
    }

    /**
     * Get the plugin storage instance.
     *
     * @return WR_Storage
     */
    public function get_storage()
    {
        return $this->_storage;
    }

    /**
     * Get the plugin viewer instance.
     *
     * @return WR_Viewer
     */
    public function get_viewer()
    {
        return $this->_viewer;
    }

    /**
     * Get the error handler instance.
     *
     * @return WR_Handler
     */
    public function get_handler()
    {
        return $this->_handler;
    }

    /**
     * Run the plugin loading all hooks registered within the loader.
     *
     * @return void
     */
    public function run_plugin()
    {
        $this->get_loader()->load();
    }

    /**
     * Check if the WooCommerce plugin is active.
     *
     * @return bool
     */
    public function is_woocommerce_active()
    {
        return $this->is_plugin_active( self::PLUGIN_WOOCOMMERCE );
    }

    /**
     * Check if the WooCommerce Germanized plugin is active.
     *
     * @return bool
     */
    public function is_germanized_active()
    {
        return $this->is_plugin_active( self::PLUGIN_GERMANIZED );
    }

    /**
     * Load all components that are shared by the mondules.
     *
     * @return void
     */
    private function load_commons()
    {
        $this->_loader = new WR_Loader($this);
        $this->_logger = new WR_Logger($this);
        $this->_client = new WR_Client($this);
        $this->_factory = new WR_Factory($this);
        $this->_settings = new WR_Settings($this);
        $this->_mailer = new WR_Mailer($this);
        $this->_storage = new WR_Storage($this);
        $this->_viewer = new WR_Viewer($this);
        $this->_handler = new WR_Handler($this);
    }

    /**
     * Load all modules that constitute this plugin.
     *
     * @return void
     */
    public function load_modules()
    {
        new WR_Plugin_Update($this);
        new WR_Admin_Assets($this);
        new WR_Admin_Notices($this);
        new WR_Admin_Settings($this);
        new WR_Bulk_Actions($this);
        new WR_Order_Action($this);
        new WR_Email_Handler($this);
        new WR_Order_Handler($this);
        new WR_Customer_Link($this);
    }
}

endif;
