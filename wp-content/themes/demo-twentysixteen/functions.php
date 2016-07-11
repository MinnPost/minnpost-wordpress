<?php

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// Remove the default function
function remove_twentysixteen_actions() {
    remove_action( 'wp_get_attachment_image_attributes', 'twentysixteen_post_thumbnail_sizes_attr', 10, 3 );
}
add_action( 'init', 'remove_twentysixteen_actions' );

function minnpost_demo_post_thumbnail_sizes_attr( $attr = array(), $attachment, $size = '' ) {
	if ( 'post-thumbnail' === $size ) {
		is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		! is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'minnpost_demo_post_thumbnail_sizes_attr', 10 , 3 );