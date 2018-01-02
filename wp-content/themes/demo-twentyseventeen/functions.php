<?php

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// Remove the default function
function remove_twentyseventeen_actions() {
    remove_action( 'wp_get_attachment_image_attributes', 'twentyseventeen_post_thumbnail_sizes_attr', 10, 3 );
}
add_action( 'init', 'remove_twentyseventeen_actions' );

function minnpost_demo_post_thumbnail_sizes_attr( $attr = array(), $attachment, $size = '' ) {
	if ( 'post-thumbnail' === $size ) {
		is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		! is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'minnpost_demo_post_thumbnail_sizes_attr', 10 , 3 );

function minnpost_has_post_thumbnail( $post = null ) {
	if ( isset( $post->id ) ) {
		$post = $post->id;
	}
	if ( get_post_meta( $post, '_thumbnail_id', 'by_url' ) === 'by_url' || ! has_post_thumbnail( $post ) ) {
		return FALSE;
	} else {
		return TRUE;
	}
}


add_filter( 'wp_get_attachment_image_src', 'gallery_change_src', 10, 4 );

function gallery_change_src( $image, $attachment_id, $size, $icon ) {
	$format = get_post_format();
	$image = image_downsize( $attachment_id, $size );
	if ( $format === 'gallery' && ! $image ) {
		$post = get_post( $attachment_id );
		if ( $post !== null ) {
			$src = $post->guid;
			$width = get_option( $size . '_size_w' );
			$height = get_option( $size . '_size_h' );
			if ( $src && $width && $height ) {
				$image = array( $src, $width, $height );
				return $image;
			}
		}
	} else {
		return $image;
	}
}

if ( ! function_exists( 'twentyseventeen_posted_on' ) ) :
/**
 * Integrate Co-Authors Plus with TwentyTen by replacing twentyseventeen_posted_on() with this function
 */
function twentyseventeen_posted_on() {
    if ( function_exists( 'coauthors_posts_links' ) ) :
        printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'twentyten' ),
            'meta-prep meta-prep-author',
            sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
                get_permalink(),
                esc_attr( get_the_time() ),
                get_the_date()
            ),
            coauthors_posts_links( null, null, null, null, false )
        );
    else:
        printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'twentyten' ),
            'meta-prep meta-prep-author',
            sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
                get_permalink(),
                esc_attr( get_the_time() ),
                get_the_date()
            ),
            sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
                get_author_posts_url( get_the_author_meta( 'ID' ) ),
                esc_attr( sprintf( __( 'View all posts by %s', 'twentyten' ), get_the_author() ) ),
                get_the_author()
            )
        );
    endif;
}
endif;

// Remove the default function
function remove_twentyseventeen_widgets_init() {
    // Unregister some of the TwentySeventeen sidebars
	unregister_sidebar( 'sidebar-1' );
	unregister_sidebar( 'sidebar-2' );
	unregister_sidebar( 'sidebar-3' );
}
add_action( 'widgets_init', 'remove_twentyseventeen_widgets_init', 11 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function minnpost_twentyseventeen_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar Right', 'twentyseventeen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your right sidebar.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Sidebar Middle', 'twentyseventeen' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your middle sidebar, which is more rarely used.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Top', 'twentyseventeen' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Add widgets here to appear in your footer.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Bottom', 'twentyseventeen' ),
		'id'            => 'sidebar-4',
		'description'   => __( 'Add widgets here to appear in your footer.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'minnpost_twentyseventeen_widgets_init', 20 );

add_action( 'cmb2_admin_init', 'minnpost_twentyseventeen_cmb2_settings' );
/**
 * Define the metabox and field configurations.
 */
function minnpost_twentyseventeen_cmb2_settings() {

	$image_settings = new_cmb2_box( array(
		'id'            => 'image_settings',
		'title'         => __( 'Image Settings', 'minnpost_twentyseventeen' ),
		'object_types'  => array( 'post', ), // Post type
		'context'       => 'side',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // Keep the metabox closed by default
	) );

	$image_settings->add_field( array(
		'name'             => 'Homepage Image Size',
		'desc'             => 'Size to use if this post appears on the homepage',
		'id'               => '_mp_image_settings_homepage_image_size',
		'type'             => 'select',
		'show_option_none' => true,
		'default'          => 'large',
		'options'          => array(
			'feature_middle' => __( 'Medium', 'minnpost_twentyseventeen' ),
			'none'   => __( 'Do not display image', 'minnpost_twentyseventeen' ),
			'feature_large'     => __( 'Large', 'minnpost_twentyseventeen' ),
		),
	) );

	$subtitle_settings = new_cmb2_box( array(
		'id'            => 'subtitle_settings',
		'title'         => __( 'Subtitle Settings', 'minnpost_twentyseventeen' ),
		'object_types'  => array( 'post', ), // Post type
		'context'       => 'subtitles',
		'priority'      => 'high',
		'show_names'    => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		 'closed'     => true, // Keep the metabox closed by default
	) );

	/*'type'       => 'text',
	'show_on_cb' => 'cmb2_hide_if_no_cats', // function should return a bool value
	// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
	// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
	// 'on_front'        => false, // Optionally designate a field to wp-admin only
	// 'repeatable'      => true,*/

	$subtitle_settings->add_field( array(
		'name'             => 'Deck',
		'desc'             => '',
		'id'               => '_mp_subtitle_settings_deck',
		'type'             => 'text',
	) );

	$subtitle_settings->add_field( array(
		'name'             => 'Byline',
		'desc'             => '',
		'id'               => '_mp_subtitle_settings_byline',
		'type'             => 'text',
	) );


	/*$category_settings = new_cmb2_box( array(
		'id'               => 'category_settings',
		'title'            => __( 'Category Settings', 'minnpost_twentyseventeen' ),
		'object_types'     => array( 'term', ), // Post type
		'taxonomies'       => array( 'category' ),
		'new_term_section' => true,
		'priority' => 'high'
	) );

	$category_settings->add_field( array(
		'name'             => 'Deck',
		'desc'             => '',
		'id'               => '_mp_category_settings_deck',
		'type'             => 'text',
	) );

	$category_settings->add_field( array(
		'name'             => 'Byline',
		'desc'             => '',
		'id'               => '_mp_category_settings_byline',
		'type'             => 'text',
	) );*/

}

/**
 * Remove default description column from category
 *
 */
function minnpost_twentyseventeen_remove_taxonomy_description($columns)
{
 // only edit the columns on the current taxonomy, replace category with your custom taxonomy (don't forget to change in the filter as well)
 if ( !isset($_GET['taxonomy']) || $_GET['taxonomy'] != 'category' )
 return $columns;

 // unset the description columns
 if ( $posts = $columns['description'] ){ unset($columns['description']); }
 return $columns;
}

add_filter('manage_edit-category_columns','minnpost_twentyseventeen_remove_taxonomy_description');

function minnpost_twentyseventeen_subtitles() {
        # Get the globals:
        global $post, $wp_meta_boxes;

        # Output the "advanced" meta boxes:
        do_meta_boxes( get_current_screen(), 'subtitles', $post );

        # Remove the initial "advanced" meta boxes:
        unset($wp_meta_boxes['post']['subtitles']);
    }

add_action('edit_form_after_title', 'minnpost_twentyseventeen_subtitles');

