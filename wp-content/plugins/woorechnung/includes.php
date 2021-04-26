<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once ( $plugin_path . 'includes/abstract/abstract-wr-error.php' );
require_once ( $plugin_path . 'includes/abstract/abstract-wr-module.php' );
require_once ( $plugin_path . 'includes/abstract/abstract-wr-plugin.php' );
require_once ( $plugin_path . 'includes/abstract/abstract-wr-logger.php' );
require_once ( $plugin_path . 'includes/abstract/abstract-wr-client.php' );
require_once ( $plugin_path . 'includes/abstract/abstract-wr-settings.php' );
require_once ( $plugin_path . 'includes/class-wr-plugin.php' );
require_once ( $plugin_path . 'includes/common/class-wr-loader.php' );
require_once ( $plugin_path . 'includes/common/class-wr-logger.php' );
require_once ( $plugin_path . 'includes/common/class-wr-client.php' );
require_once ( $plugin_path . 'includes/common/class-wr-factory.php' );
require_once ( $plugin_path . 'includes/common/class-wr-settings.php' );
require_once ( $plugin_path . 'includes/common/class-wr-mailer.php' );
require_once ( $plugin_path . 'includes/common/class-wr-storage.php' );
require_once ( $plugin_path . 'includes/common/class-wr-handler.php' );
require_once ( $plugin_path . 'includes/common/class-wr-verbosity.php' );
require_once ( $plugin_path . 'includes/common/class-wr-viewer.php' );
require_once ( $plugin_path . 'includes/errors/class-wr-client-error.php' );
require_once ( $plugin_path . 'includes/errors/class-wr-export-error.php' );
require_once ( $plugin_path . 'includes/errors/class-wr-server-error.php' );
require_once ( $plugin_path . 'includes/errors/class-wr-storage-error.php' );
require_once ( $plugin_path . 'includes/admin/class-wr-plugin-update.php' );
require_once ( $plugin_path . 'includes/admin/class-wr-admin-assets.php' );
require_once ( $plugin_path . 'includes/admin/class-wr-admin-settings.php' );
require_once ( $plugin_path . 'includes/admin/class-wr-admin-notices.php' );
require_once ( $plugin_path . 'includes/admin/class-wr-bulk-actions.php' );
require_once ( $plugin_path . 'includes/admin/class-wr-order-action.php' );
require_once ( $plugin_path . 'includes/class-wr-customer-link.php' );
require_once ( $plugin_path . 'includes/class-wr-order-handler.php' );
require_once ( $plugin_path . 'includes/class-wr-email-handler.php' );
require_once ( $plugin_path . 'includes/support/class-wr-order-adapter.php' );
