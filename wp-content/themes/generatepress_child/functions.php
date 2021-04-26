<?php
/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file. 
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */

function generatepress_child_enqueue_scripts() {
	if (is_rtl()) {
		wp_enqueue_style('generatepress-rtl', trailingslashit(get_template_directory_uri()) . 'rtl.css');
	}
}

add_action('wp_enqueue_scripts', 'generatepress_child_enqueue_scripts', 100);

// add active state on Shop :: navigation under categories, product and shop mainpage
function special_nav_class ($classes, $item) {
	if( strtolower($item->title) == "shop" ){
        if (is_page('shop') || is_woocommerce() || is_product()) {
            $classes[] = 'current-menu-item active';
        }
    }
	return $classes;
}
add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 4);
add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {
return array(
'width' => 650,
'height' => 650,
'crop' => 0,
);
}
);
/*
* Creating a function to create our CPT
*/
 
function custom_post_type() {
 
// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Ansprechpersonen', 'Post Type General Name', 'twentytwenty' ),
        'singular_name'       => _x( 'Ansprechperson', 'Post Type Singular Name', 'twentytwenty' ),
        'menu_name'           => __( 'Ansprechpersonen', 'twentytwenty' ),
        'parent_item_colon'   => __( 'Parent Ansprechperson', 'twentytwenty' ),
        'all_items'           => __( 'All Ansprechpersonen', 'twentytwenty' ),
        'view_item'           => __( 'View Ansprechperson', 'twentytwenty' ),
        'add_new_item'        => __( 'Add New Ansprechperson', 'twentytwenty' ),
        'add_new'             => __( 'Add New', 'twentytwenty' ),
        'edit_item'           => __( 'Edit Ansprechperson', 'twentytwenty' ),
        'update_item'         => __( 'Update Ansprechperson', 'twentytwenty' ),
        'search_items'        => __( 'Search Ansprechperson', 'twentytwenty' ),
        'not_found'           => __( 'Not Found', 'twentytwenty' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwenty' ),
    );
     
// Set other options for Custom Post Type
     
    $args = array(
        'label'               => __( 'Ansprechpersonen', 'twentytwenty' ),
        'description'         => __( 'Ansprechperson news and reviews', 'twentytwenty' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'Vendoren' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */ 
        'hierarchical'        => false,
        'public'              => true,
        'menu_icon'           => 'dashicons-universal-access',
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true,
 
    );
     
    // Registering your Custom Post Type
    register_post_type( 'Ansprechpersonen', $args );
 
}
 
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
 
add_action( 'init', 'custom_post_type', 0 );
// shove YOAST settings panel in editor to bottom 
add_filter( 'wpseo_metabox_prio', function() { return 'low'; } );


// Method 1: Filter.
function my_acf_google_map_api( $api ){
    $api['key'] = 'AIzaSyAV4GQzwX_IcQ54woSCVVDTgoQ8p0siS0g';
    return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');