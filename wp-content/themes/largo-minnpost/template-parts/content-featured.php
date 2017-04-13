<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Largo
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail( get_the_ID() ) ) : ?>

		<?php $size = esc_html( get_post_meta( get_the_ID(), '_mp_image_settings_homepage_image_size', true ) ); ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php if ( is_home() ) : ?>
			<?php the_post_thumbnail( $size, array( 'alt' => the_title_attribute( 'echo=0' ) )); ?>
		<?php else : ?>
			<?php the_post_thumbnail( 'feature', array( 'alt' => the_title_attribute( 'echo=0' ) )); ?>
		<?php endif; ?>
		</a>

	<?php endif; ?>

	<header class="entry-header">
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>

		<div class="entry-meta">
			By <?php minnpost_posted_by(); ?> | <?php minnpost_posted_on(); ?> <?php minnpost_edit_link(); ?>
		</div>

		<?php endif; ?>

	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_excerpt();
		?>
	</div><!-- .entry-content -->


</article><!-- #post-## -->
