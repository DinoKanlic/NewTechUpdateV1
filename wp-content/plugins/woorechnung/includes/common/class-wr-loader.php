<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WR_Loader' ) ):

/**
 * WooRechnung Action and Filter Hook Loader Component.
 *
 * @version  1.0.0
 * @package  WooRechnung\Common
 * @author   Zweischneider
 */
final class WR_Loader
{
    /**
     * The plugin instance via dependency injection.
     *
     * @var WR_Plugin
     */
    private $_plugin;

    /**
     * The action hooks registered within this plugin.
     *
     * @var array
     */
    private $_actions;

    /**
     * The filter hooks registered within this plugin.
     *
     * @var array
     */
    private $_filters;

    /**
     * Create a new instance of the hook loader class.
     *
     * @param  WR_Plugin $plugin
     * @return void
     */
    public function __construct(WR_Plugin $plugin)
    {
        $this->_plugin = $plugin;
        $this->_actions = array();
        $this->_filters = array();
    }

    /*
     * Add an action hook to the corresponding array.
     *
     * @param string $name
     * @param callable $callback
     * @param int $priority
     * @param int $args
     * @return void
     */
    public function add_action($name, $callback, $priority = 10, $args = 1)
    {
        $this->_actions[] = compact('name', 'callback', 'priority', 'args');
    }

    /*
     * Add a filter hook to the corresponding array.
     *
     * @param string $name
     * @param callable $callback
     * @param int $priority
     * @param int $args
     * @return void
     */
    public function add_filter($name, $callback, $priority = 10, $args = 1)
    {
        $this->_filters[] = compact('name', 'callback', 'priority', 'args');
    }

    /**
     * Load all action and filter hooks that have been registered.
     *
     * @return void
     */
    public function load()
    {
        $this->load_actions();
        $this->load_filters();
    }

    /**
     * Load all action hooks that have been registered.
     *
     * @return void
     */
    private function load_actions()
    {
        foreach ( $this->_actions as $hook ) {
            add_action($hook['name'], $hook['callback'], $hook['priority'], $hook['args']);
        }
    }

    /**
     * Load all filter hooks that have been registered.
     *
     * @return void
     */
    private function load_filters()
    {
        foreach ( $this->_filters as $hook ) {
            add_filter($hook['name'], $hook['callback'], $hook['priority'], $hook['args']);
        }
    }
}

endif;
