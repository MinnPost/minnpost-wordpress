<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		if ( is_sticky() && is_home() ) :
			echo twentyseventeen_get_svg( array( 'icon' => 'thumb-tack' ) );
		endif;
	?>
	<header class="entry-header">
		<?php
			if ( is_single() ) {
				the_title( '<h1 class="entry-title">', '</h1>' );
			} else {
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			}
		?>

		<?php if ( 'post' === get_post_type() ) : ?>

		<div class="entry-meta">By <?php echo ! empty( esc_html( get_post_meta( get_the_ID(), '_mp_subtitle_settings_byline', true ) ) ) ? esc_html( get_post_meta( get_the_ID(), '_mp_subtitle_settings_byline', true ) ) : function_exists( 'coauthors_posts_links' ) ? coauthors_posts_links( ',', ',', null, null, false ) : the_author_posts_link(); ?> | <?php echo is_single() ? twentyseventeen_time_link() : twentyseventeen_time_link() ?> <?php twentyseventeen_edit_link(); ?></div>

		<?php endif; ?>

	</header><!-- .entry-header -->

	<?php if ( '' !== get_the_post_thumbnail() ) : ?>

		<?php if ( is_singular() ) : ?>

		<div class="post-thumbnail">
			<?php the_post_thumbnail( 'detail' ); ?>
			<p><?php echo get_media_credit_html( get_post_thumbnail_id() ); ?></p>
		</div><!-- .post-thumbnail -->

		<?php else : ?>

		<?php $size = esc_html( get_post_meta( get_the_ID(), '_mp_image_settings_homepage_image_size', true ) ); ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php if ( is_home() ) : ?>
			<?php the_post_thumbnail( $size, array( 'alt' => the_title_attribute( 'echo=0' ) )); ?>
		<?php else : ?>
			<?php the_post_thumbnail( 'feature', array( 'alt' => the_title_attribute( 'echo=0' ) )); ?>
		<?php endif; ?>
		</a>

		<?php endif; ?>

	<?php endif; ?>
	
	<div class="entry-content">

		<?php if ( is_singular() ) : ?>

			<?php
			the_content( sprintf(
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ),
				get_the_title()
			) );

			wp_link_pages( array(
				'before'      => '<div class="page-links">' . __( 'Pages:', 'twentyseventeen' ),
				'after'       => '</div>',
				'link_before' => '<span class="page-number">',
				'link_after'  => '</span>',
			) );
			?>

		<?php else : ?>
			<?php the_excerpt(); ?>
		<?php endif; ?>

		
	</div><!-- .entry-content -->

	<?php if ( is_single() ) : ?>
		<?php twentyseventeen_entry_footer(); ?>
	<?php endif; ?>

</article><!-- #post-## -->
