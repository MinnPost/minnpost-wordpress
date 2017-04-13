<?php

// child theme must use get_stylesheet_directory in place of get_template_directory

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.min.css' );
}

if ( ! function_exists( 'minnpost_menus' ) ) :
	function minnpost_menus() {
		// Add Your Menu Locations
		register_nav_menus(
			array(
				'footer_primary' => __( 'Footer Primary' ), 
				'footer_secondary' => __( 'Footer Secondary' ),
				'minnpost_network' => __( 'Network Menu' ),
				'support_minnpost' => __( 'Support Menu' ),
				'top_menu' => __( 'Top Menu' ),
				'secondary_links' => __( 'Secondary' ),
				'primary_links' => __( 'Primary' ),
			)
		);
		unregister_nav_menu( 'menu-1' );
	}

	add_action( 'init', 'minnpost_menus' );

endif;


/**
 * Custom template tags for this theme.
 */
require get_stylesheet_directory() . '/inc/template-tags.php';