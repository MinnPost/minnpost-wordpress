<?php

// child theme must use get_stylesheet_directory in place of get_template_directory

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.min.css' );
}

if ( ! function_exists( 'minnpost_posted_at' ) ) :
	function minnpost_posted_at() {
		//echo is_single() ? twentyseventeen_time_link() : twentyseventeen_time_link();
		//twentyseventeen_edit_link();
		if ( get_the_date( 'Y-m-d'  ) === date( 'Y-m-d', time() ) ) :
			printf( __( get_the_time(), 'minnpost' ) );
		else:
			printf( __( get_the_date(), 'minnpost' ) );	
		endif;
	}
endif;


/**
 * Custom template tags for this theme.
 */
require get_stylesheet_directory() . '/inc/template-tags.php';