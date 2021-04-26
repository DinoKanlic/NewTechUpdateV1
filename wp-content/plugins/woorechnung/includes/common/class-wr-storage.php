<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WR_Storage' ) ):

/**
 * WooRechnung Storage class.
 *
 * This class provides an interface to a persistent data storage.
 * In order to send invoices as email attachments, the invoice PDF has
 * to be stored in the local filesystem. When to mail is actually sent,
 * the PDF file is being loaded from storage again.
 *
 * @class    WR_Storage
 * @version  1.0.0
 * @package  WooRechnung
 * @author   Zweischneider
 */
final class WR_Storage
{
    /**
     * The plugin instance via dependency injection.
     *
     * @var WR_Plugin
     */
    protected $_plugin;

    /**
     * Create a new instance of this storage class.
     *
     * @param  WR_Plugin $plugin
     * @return void
     */
    public function __construct(WR_Plugin $plugin)
    {
        $this->_plugin = $plugin;
    }

    /**
     * Store a file given the full path and data.
     *
     * @throws WR_Storage_Error
     * @param  string $path
     * @param  string $data
     * @return void
     */
    public function store_file($path, $data)
    {
        $result = @file_put_contents($path, $data);

        if ($result === false) {
            $code = WR_Storage_Error::WRITE_FILE_ERROR;
            throw new WR_Storage_Error($path, $code);
        }
    }

    /**
     * Load file data from a given path.
     *
     * @throws WR_Storage_Error
     * @param  string $path
     * @return binary
     */
    public function load_file($path)
    {
        $result = @file_get_contents($path);

        if ($result === false) {
            $code = WR_Storage_Error::READ_FILE_ERROR;
            throw new WR_Storage_Error($path, $code);
        }

        return $result;
    }

    /**
     * Create a new directory from a given path.
     *
     * @throws WR_Storage_Error
     * @param  string $permissions
     * @param  int $rights
     * @return void
     */
    public function create_directory($path, $permissions = 777)
    {
        $result = @mkdir($path, $permissions, true);

        if ($result === false) {
            $code = WR_Storage_Error::WRITE_DIR_ERROR;
            throw new WR_Storage_Error($path, $code);
        }
    }
}

endif;
