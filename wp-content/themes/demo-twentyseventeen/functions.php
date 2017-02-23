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


function twentyseventeen_post_thumbnail() {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
	?>

	<div class="post-thumbnail">
		<?php the_post_thumbnail( 'detail' ); ?>
	</div><!-- .post-thumbnail -->

	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
	<?php if ( is_home() ) : ?>
		<?php the_post_thumbnail( 'thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ) )); ?>
	<?php else : ?>
		<?php the_post_thumbnail( 'feature', array( 'alt' => the_title_attribute( 'echo=0' ) )); ?>
	<?php endif; ?>
	</a>

	<?php endif; // End is_singular()
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