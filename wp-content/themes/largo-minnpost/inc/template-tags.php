<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package MinnPost Largo
 */

if ( ! function_exists( 'minnpost_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function minnpost_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( '%s', 'post date', 'largo' ),
		$time_string
	);

	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'largo' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

}
endif;

if ( ! function_exists( 'minnpost_posted_by' ) ) :
	/**
	 * Integrate Co-Authors Plus
	 */
	function minnpost_posted_by() {

		//echo ! empty( esc_html( get_post_meta( get_the_ID(), '_mp_subtitle_settings_byline', true ) ) ) ? esc_html( get_post_meta( get_the_ID(), '_mp_subtitle_settings_byline', true ) ) : function_exists( 'coauthors_posts_links' ) ? coauthors_posts_links( ',', ',', null, null, false ) : the_author_posts_link();

		if ( ! empty ( esc_html( get_post_meta( get_the_ID(), '_mp_subtitle_settings_byline', true ) ) ) ) :
			printf( __( esc_html( get_post_meta( get_the_ID(), '_mp_subtitle_settings_byline', true ) ), 'minnpost' ) );
		else:
			if ( function_exists( 'coauthors_posts_links' ) ) :
				printf( __( coauthors_posts_links( ',', ',', null, null, false ) ) );
			else:
				the_author_posts_link();
			endif;
		endif;

	}
endif;

if ( ! function_exists( 'minnpost_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function minnpost_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'largo' ) );
		if ( $categories_list && minnpost_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'largo' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'largo' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'largo' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		/* translators: %s: post title */
		comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'largo' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
		echo '</span>';
	}

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'largo' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;


if ( ! function_exists( 'minnpost_edit_link' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function minnpost_edit_link() {
	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'largo' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function minnpost_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'minnpost_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'minnpost_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so minnpost_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so minnpost_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in minnpost_categorized_blog.
 */
function minnpost_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'minnpost_categories' );
}
add_action( 'edit_category', 'minnpost_category_transient_flusher' );
add_action( 'save_post',     'minnpost_category_transient_flusher' );
