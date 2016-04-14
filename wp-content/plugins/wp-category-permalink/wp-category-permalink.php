<?php
/*
Plugin Name: WP Category Permalink
Plugin URI: http://apps.meow.fr
Description: Allows manual selection of a 'main' category for each post for better permalinks and SEO. Pro version WILL add support for custom taxonomies.
Version: 2.2.8
Author: Jordy Meow
Author URI: http://www.meow.fr
Remarks: This plugin was inspired by the Hikari Category Permalink. The way it works on the client-side is similar, and the JS file is actually the same one with a bit more code added to it.

Originally developed for two of my websites:
- Totoro Times (http://www.totorotimes.com)
- Haikyo (http://www.haikyo.org)
*/

if ( is_admin() ) {
	require( 'jordy_meow_footer.php' );
	require( 'wpcp_settings.php' );
}

/**
 *
 * Posts list
 *
 */

add_filter( 'manage_posts_columns' , 'mwcp_manage_posts_columns' );
function mwcp_manage_posts_columns( $columns ) {
	global $post_type;

	if (wpcp_is_woocommerce_product( $post_type ) && !wpcp_woocommerce_support() ) {
		return $columns;
	}

	$hidden_columns = get_user_option( "manageedit-postcolumnshidden" );
	if ( !in_array( 'scategory_permalink', (array) $hidden_columns) ) {
		$hidden_columns[] = 'scategory_permalink';
		$user = wp_get_current_user();
		update_user_option( $user->ID, "manageedit-postcolumnshidden", $hidden_columns );
	}
	return array_merge( $columns, array( 'scategory_permalink' => __( 'Permalink Category', 'wp-category-permalink' ) ) );
}

add_action( 'manage_posts_custom_column' , 'mwcp_custom_columns', 10, 2 );
function mwcp_custom_columns( $column, $post_id ) {
	global $post_type;

	if ( wpcp_is_woocommerce_product( $post_type ) && !wpcp_woocommerce_support() ) {
		return $column;
	}

	if ( $column == 'scategory_permalink' ) {
		$cat_id = get_post_meta( $post_id , '_category_permalink', true );
		echo "<span class='scategory_permalink_name'>";
		if ( $cat_id != null ) {
			$cat = get_category( $cat_id );
			if ( ! isset( $cat ) ) {
				$terms = get_the_terms( $post_id, 'product_cat' );
				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					return $column;
				}

				foreach ($terms as $term) {
					if ($cat_id == $term->term_id) {
						echo $term->name;
					}
				}
			} else {
				echo $cat->name;
			}
		} else {
			$cat = get_the_category( $post_id );
			if ( empty( $cat ) || is_wp_error( $cat ) ) {
				$terms = get_the_terms( $post_id, 'product_cat' );
				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					return $column;
				}
				$cat = array_values( $terms );
			}
			if ( count( $cat ) > 1 ) {
				echo '<span style="color: red;">' . $cat[0]->name . '</span>';
			}
			else if ( count( $cat ) == 1 ) {
				echo $cat[0]->name;
			}
		}
		echo "</span>";
	}
}



/**
 *
 * Post Edit CSS/JS + Update
 *
 */

add_action( 'admin_enqueue_scripts', 'mwcp_admin_enqueue_scripts' );

function mwcp_admin_enqueue_scripts () {
	global $post_type;

	// If it's a WooCommerce product and wpcp_woocommerce_support() or wpcp_is_pro() are false then exit.
	if ( wpcp_is_woocommerce_product( $post_type ) && ( !wpcp_woocommerce_support() || ! wpcp_is_pro() ) ) {
		return;
	}

	wp_enqueue_script( 'wp-category-permalink.js', plugins_url('/wp-category-permalink.js', __FILE__), array( 'jquery' ), '1.6', false );
}

/**
 *
 * Post Edit CSS/JS + Update
 *
 */

add_action( 'admin_print_styles-post.php', 'mwcp_post_css' );
add_action( 'admin_print_styles-post-new.php','mwcp_post_css' );
add_action( 'admin_footer-post.php', 'mwcp_post_js' );
add_action( 'admin_footer-post-new.php', 'mwcp_post_js' );
add_action( 'transition_post_status', 'mwcp_transition_post_status', 0, 3 );

// Inject the CSS into the post edit UI
function mwcp_post_css() {
	echo "<style type=\"text/css\">.scategory_link{vertical-align:middle;display:none;cursor:pointer;cursor:hand}</style>\n";
}

// Inject the javascript into the post edit UI
function mwcp_post_js() {
	global $post;

	$categoryID = '';
	if ( $post->ID ) {
		$categoryID = get_post_meta( $post->ID, '_category_permalink', true );
	}
	echo "<script type=\"text/javascript\">jQuery('#categorydiv, #taxonomy-product_cat').sCategoryPermalink({current: '$categoryID'});</script>\n";
}

// Update the post meta
function mwcp_transition_post_status( $new_status, $old_status, $post ) {
	if ( !isset( $_POST['scategory_permalink'] ) )
		return;
	$scategory_permalink = $_POST['scategory_permalink'];
	if ( isset( $scategory_permalink ) ) {
		$cats = wp_get_post_categories( $post->ID );

		if ( empty( $cats ) || is_wp_error( $cats ) ) {
			update_post_meta( $post->ID, '_category_permalink', $scategory_permalink );
			return;
		}

		foreach( $cats as $cat ){
			if( $cat == $scategory_permalink ) {
				if ( !update_post_meta( $post->ID, '_category_permalink', $scategory_permalink ) ) {
					add_post_meta( $post->ID, '_category_permalink',  $scategory_permalink, true );
					return;
				}
			}
		}
	}
}

/**
 *
 * Update the category on-the-fly (reading-mode)
 *
 */

add_filter( 'post_link_category', 'mwcp_post_link_category', 10, 3 );

// Return the category set-up in '_category_permalink', otherwise return the default category
function mwcp_post_link_category( $cat, $cats, $post ) {

	$catmeta = get_post_meta($post->ID, '_category_permalink', true);
	//	$cat = get_category( $catmeta );
	foreach ( $cats as $cat ) {
		if ( $cat->term_id == $catmeta ) {
			return $cat;
		}
	}
	return $cat;
}

add_filter( 'post_type_link', 'wpcp_update_permalink', 10, 2 );

function wpcp_update_permalink( $url, $post ) {
	if ( wpcp_woocommerce_support() ) {
		// Check if product permalink base contains %product_cat%.
		$arr = get_option( 'woocommerce_permalinks' );
		$permalink_structure = $arr['product_base'];
		if (false === strpos( $permalink_structure, '%product_cat%' ) ) {
			return $url;
		}

		// Check if _category_permalink is set.
		$category_permalink = get_post_meta( $post->ID, '_category_permalink', true );
		if ( empty( $category_permalink ) || is_wp_error( $category_permalink ) ) {
			return $url;
		} else {
			$category_permalink = (int) $category_permalink;
		}

		// Check if product belongs to any product category.
		$terms = get_the_terms( $post->ID, 'product_cat' );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return $url;
		}

		foreach ( $terms as $term ) {
			$cats[] = array(
			'id' => $term->term_id,
			'link' => str_replace( '%product_cat%', rtrim( wpcp_get_taxonomy_parents( $term->term_id, 'product_cat', false, '/', true ), '/' ), $permalink_structure ),
			);
		}

		foreach ( $cats as $cat ) {
			if ( $cat['id'] == $category_permalink ) {
				return trailingslashit( site_url( trailingslashit( str_replace( array( '%year%', '%monthnum%', '%day%', '%hour%', '%minute%', '%second%', '%post_id%', '%postname%', '%author%' ), array( get_the_date( 'Y', $post->ID ), get_the_date( 'm', $post->ID ), get_the_date( 'd', $post->ID ), get_the_date( 'H', $post->ID ), get_the_date( 'i', $post->ID ), get_the_date( 's', $post->ID ), $post->ID, $post->post_name, get_the_author_meta( 'user_nicename', $post->post_author ) ), $cat['link']) ) . $post->post_name ) );
			}
		}
	}

	return $url;
}

/**
 *
 * MENU ITEM (SETTINGS)
 *
 */

add_action( 'admin_menu', 'wpcp_admin_menu' );

function wpcp_admin_menu() {
	add_options_page( 'Category Permalink', 'Category Permalink', 'manage_options', 'wpcp_settings', 'wpcp_settings_page' );
}

function wpcp_getoption( $option, $section, $default = '' ) {
	$options = get_option( $section );
	if ( isset( $options[$option] ) ) {
		if ( $options[$option] == "off" ) {
			return false;
		}
		if ( $options[$option] == "on" ) {
			return true;
		}
		return $options[$option];
  }
	return $default;
}

function wpcp_is_woocommerce_product( $post_type ) {
	return $post_type == 'product' && class_exists( 'woocommerce' );
}

/**
 *
 * PRO
 *
 */

function wpcp_woocommerce_support() {
	return wpcp_getoption( "woocommerce", "wpcp_basics", false );
}

function wpcp_is_pro() {
	$validated = get_transient( 'wpcp_validated' );
	if ( $validated ) {
		$serial = get_option( 'wpcp_pro_serial');
		return !empty( $serial );
	}
	$subscr_id = get_option( 'wpcp_pro_serial', "" );
	if ( !empty( $subscr_id ) )
		return wpcp_validate_pro( wpcp_getoption( "subscr_id", "wpcp_pro", array() ) );
	return false;
}

function wpcp_validate_pro( $subscr_id ) {
	if ( empty( $subscr_id ) ) {
		delete_option( 'wpcp_pro_serial', "" );
		delete_option( 'wpcp_pro_status', "" );
		set_transient( 'wpcp_validated', false, 0 );
		return false;
	}
	require_once ABSPATH . WPINC . '/class-IXR.php';
	require_once ABSPATH . WPINC . '/class-wp-http-ixr-client.php';
	$client = new WP_HTTP_IXR_Client( 'http://apps.meow.fr/xmlrpc.php' );
	$client->useragent = 'MeowApps';
	if ( !$client->query( 'meow_sales.auth', $subscr_id, 'category-permalink', get_site_url() ) ) {
		update_option( 'wpcp_pro_serial', "" );
		update_option( 'wpcp_pro_status', "A network error: " . $client->getErrorMessage() );
		set_transient( 'wpcp_validated', false, 0 );
		return false;
	}
	$post = $client->getResponse();
	if ( !$post['success'] ) {
		if ( $post['message_code'] == "NO_SUBSCRIPTION" ) {
			$status = __( "Your serial does not seem right." );
		}
		else if ( $post['message_code'] == "NOT_ACTIVE" ) {
			$status = __( "Your subscription is not active." );
		}
		else if ( $post['message_code'] == "TOO_MANY_URLS" ) {
			$status = __( "Too many URLs are linked to your subscription." );
		}
		else {
			$status = "There is a problem with your subscription.";
		}
		update_option( 'wpcp_pro_serial', "" );
		update_option( 'wpcp_pro_status', $status );
		set_transient( 'wpcp_validated', false, 0 );
		return false;
	}
	set_transient( 'wpcp_validated', $subscr_id, 3600 * 24 * 100 );
	update_option( 'wpcp_pro_serial', $subscr_id );
	update_option( 'wpcp_pro_status', __( "Your subscription is enabled." ) );
	return true;
}

/**
 * Retrieve category parents with separator for general taxonomies.
 * Modified version of get_category_parents()
 *
 * @param int $id Category ID.
 * @param string $taxonomy Optional, default is 'category'.
 * @param bool $link Optional, default is false. Whether to format with link.
 * @param string $separator Optional, default is '/'. How to separate categories.
 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
 * @param array $visited Optional. Already linked to categories to prevent duplicates.
 * @return string
 */
function wpcp_get_taxonomy_parents( $id, $taxonomy = 'category', $link = false, $separator = '/', $nicename = false, $visited = array() ) {
	$chain = '';
	$parent = get_term( $id, $taxonomy );
	if ( is_wp_error( $parent ) )
		return $parent;
	if ( $nicename )
		$name = $parent->slug;
	else
		$name = $parent->name;
	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= wpcp_get_taxonomy_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
	}
	if ( $link )
		$chain .= '<a href="' . esc_url( get_term_link( $parent,$taxonomy ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->name ) ) . '">'.$name.'</a>' . $separator;
	else
		$chain .= $name.$separator;
	return $chain;
}
