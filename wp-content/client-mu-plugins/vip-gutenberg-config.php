<?php

/* VIP: Disable Gutenberg editor */
add_filter( 'use_block_editor_for_post', '__return_false' );

/**
 * Later: When to use Block Editor, based on post type
 */
if ( ! function_exists( 'minnpost_use_block_editor_for_post_type' ) ) :
	//add_filter( 'use_block_editor_for_post', 'minnpost_use_block_editor_for_post_type', 15, 2 );
	function minnpost_use_block_editor_for_post_type( $can_edit, $post ) {
		return false; // remove this line when we are ready to start using the block editor
		$use_for_post_types = array( 'newsletter' );
		if ( in_array( $post->post_type, $use_for_post_types, true ) ) {
			return true;
		}
		return false;
	}
endif;
