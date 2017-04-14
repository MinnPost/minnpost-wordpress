<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Largo
 */

?>

	</div><!-- #content -->

	<nav id="footer-primary" class="footer-navigation" role="navigation">
		<button class="menu-toggle" aria-controls="footer-primary" aria-expanded="false"><?php esc_html_e( 'Primary Footer', 'largo' ); ?></button>
		<?php wp_nav_menu(
			array(
				'theme_location' => 'footer_primary',
				'menu_id' => 'footer-primary',
				'depth' => 1,
				'container' => false,
				'walker' => new Minnpost_Walker_Nav_Menu,
			)
		); ?>
	</nav><!-- #footer-primary -->

	<nav id="footer-secondary" class="footer-navigation" role="navigation">
		<button class="menu-toggle" aria-controls="footer-secondary" aria-expanded="false"><?php esc_html_e( 'Secondary Footer', 'largo' ); ?></button>
		<?php wp_nav_menu(
			array(
				'theme_location' => 'footer_secondary',
				'menu_id' => 'footer-secondary',
				'depth' => 1,
				'container' => false,
				'walker' => new Minnpost_Walker_Nav_Menu,
			)
		); ?>
	</nav><!-- #footer-secondary -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'largo' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'largo' ), 'WordPress' ); ?></a>
			<span class="sep"> | </span>
			<?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'largo' ), 'largo', '<a href="https://automattic.com/" rel="designer">inn_nerds</a>' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
