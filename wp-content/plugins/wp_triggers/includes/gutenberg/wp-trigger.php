<?php
/**
 * Register new block category "WP Triggers".
*/
add_filter( 'block_categories', function( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'wp-triggers',
				'title' => 'WP Triggers',
			),
		)
	);
}, 10, 2 );

defined( 'ABSPATH' ) || exit;

/**
 * Load all translations for our plugin from the MO file.
 */
add_action( 'init', 'wptgg_triggers_load_textdomain' );

function wptgg_triggers_load_textdomain() {
	load_plugin_textdomain( 'wp-triggers', false, basename( __DIR__ ) . '/languages' );
}

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * Passes translations to JavaScript.
 */
function wptgg_triggers_register_block() {

	if ( ! function_exists( 'register_block_type' ) ) {
		// Gutenberg is not active.
		return;
	}

	register_block_type( 'wp-triggers/trigger-box', array(
		'editor_script' => 'wp-triggers-gutenberg-block',
		'render_callback' => 'wptgg_trigger_gutenberg_render'
	) );

	if ( function_exists( 'wp_set_script_translations' ) ) {
	/**
	 * May be extended to wp_set_script_translations( 'my-handle', 'my-domain',
	 * plugin_dir_path( MY_PLUGIN ) . 'languages' ) ). For details see
	 * https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
	*/
		wp_set_script_translations( 'wp-triggers-gutenberg-block', 'gutenberg-examples' );
	}

}
add_action( 'init', 'wptgg_triggers_register_block' );

function wptgg_trigger_gutenberg_render( $attributes ) {
	$id = $attributes['id'];

	$html = '';
	if($id !=='' && $id !==NULL) {
		$html .= do_shortcode('[wptrigger id='.$id.']');
	}

	return $html;
}

function wptgg_triggers_enqueue_assets() {
	wp_register_script(
		'wp-triggers-gutenberg-block',
		plugins_url( 'block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'block.js' )
	);

	$triggers = wptggBackEnd::get_trigger();

	// Localize the script with list of WP Triggers
	$wp_triggers = array();

	$wp_triggers[] = array('value' => '', 'label' => 'Select a Trigger Box');

	foreach($triggers as $trigger_item) {
		$wp_triggers[] = array('value' => $trigger_item->ID, 'label' => $trigger_item->box_name);
	}

	wp_localize_script( 'wp-triggers-gutenberg-block', 'wp_triggers_object', $wp_triggers );

	// Enqueued script with localized data.
	wp_enqueue_script( 'wp-triggers-gutenberg-block' );
}
add_action('enqueue_block_editor_assets', 'wptgg_triggers_enqueue_assets');