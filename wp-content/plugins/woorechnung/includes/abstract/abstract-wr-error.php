<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Abstract_Error' ) ):

/**
 * WooRechnung Abstract Error Class
 *
 * @class    WR_Abstract_Error
 * @version  1.0.0
 * @package  WooRechnung\Abstract
 * @author   Zweischneider
 */
abstract class WR_Abstract_Error extends Exception
{
    abstract public function render_error();
}

endif;
