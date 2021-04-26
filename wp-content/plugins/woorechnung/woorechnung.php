<?php

/**
 * Plugin Name: WooRechnung
 * Plugin URI: https://www.woorechnung.com/
 * Description: Erweiterung von WooCommerce um Funktionen zur Rechnungserstellung.
 * Version: 2.0.23
 * Author: Zweischneider GmbH & Co. KG
 * Author URI: https://www.zweischneider.de
 *
 * Text Domain: woorechnung
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 4.8.0
 *
 * @package WooRechnung
 * @category Core
 * @author ZWEISCHNEIDER
 */

// Exit if accessed directly
if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

if (!function_exists('is_function_disabled')) {
    function is_function_disabled($function)
    {
        $disabled = @ini_get('disable_functions');
        if (!empty($disabled) && is_string($disabled)) {
            $disabled = explode(',', $disabled);
            if (is_array($disabled)) {
                return in_array($function, $disabled);
            }
        }
        return false;
    }
}

if (!function_exists('user_home_path')) {
    function user_home_path($path)
    {
        $user = @get_current_user();
        if (is_function_disabled('posix_getpwnam') == false && function_exists('posix_getpwnam')) {
            $user_info = !empty($user) ? @posix_getpwnam($user) : null;
        } else {
            $user_info = null;
        }

        // Get home path
        $home = getenv('HOME');
        if (empty($home)) {
            if (!empty($_SERVER['HOME'])) {
                $home = $_SERVER['HOME'];
            } else if (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
                $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
            } else if (!empty($user_info) && !empty($user_info['dir'])) {
                $home = $user_info['dir'];
            } else if (!empty($_SERVER['DOCUMENT_ROOT'])) {
                $home = @realpath($_SERVER['DOCUMENT_ROOT'].'/../');
            }
        }

        if (empty($home)) {
            return '';
        }

        // build full path
        $full_path = rtrim($home, '\\/').'/'.ltrim($path, '\\/');

        // Check for open_basedir restrictions
        $open_basedir = @ini_get('open_basedir');
        if (!empty($open_basedir)) {
            $open_basedirs = explode(PATH_SEPARATOR, $open_basedir);
            foreach ($open_basedirs as $dir) {
                if (stripos(rtrim($home, '\\/'), rtrim($dir, '\\/')) === 0) {
                    return $full_path;
                }
            }
        } else {
            return $full_path;
        }

        return '';
    }
}

// Get the name of this file in filsystem
// Get the root path of this plugin in filesystem
// Get the base URL of this plugin on the server
$plugin_file = __FILE__;
$plugin_path = plugin_dir_path( $plugin_file );
$plugin_url = plugins_url( '', $plugin_file );

$home_config = user_home_path('woorechnung/config/config.php');
$plugin_config = $plugin_path . 'config/config.php';

// Require all classes the plugin consists of
// Include the configuration file for the plugin
require_once ( !empty($home_config) && file_exists($home_config) ? $home_config : $plugin_config );
require_once ( $plugin_path . 'includes.php' );

// Create a new instance of the plugin class
// Run the plugin and register all hooks within WordPress
$plugin = new WR_Plugin( $plugin_file, $plugin_path, $plugin_url );
$plugin->run_plugin();

// Make the plugin instance global
$GLOBALS['woorechnung'] = $plugin;

function woorechnung() {
    return $GLOBALS['woorechnung'];
}
