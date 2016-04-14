<?php

add_action( 'admin_init', 'wpcp_admin_init' );

/**
 *
 * SETTINGS PAGE
 *
 */
 
function wpcp_settings_page() {
    global $wpcp_settings_api;
	echo '<div class="wrap">';
    jordy_meow_donation(true);
    echo "<div id='icon-options-general' class='icon32'><br></div><h2>WP Category Permalink";
    by_jordy_meow();
    echo "</h2>";
    echo "<p>For more information about WP Category Permalink, please visit the official website here: <a target='_blank' href='http://apps.meow.fr/wp-category-permalink/'>WP Category Permalink</a> on Meow Apps.</p>";
    $wpcp_settings_api->show_navigation();
    $wpcp_settings_api->show_forms();
    echo '</div>';
	jordy_meow_footer();
}

function wpcp_admin_init() {
    if ( isset( $_POST ) && isset( $_POST['wpcp_pro'] ) )
        wpcp_validate_pro( $_POST['wpcp_pro']['subscr_id'] );
    $pro_status = get_option( 'wpcp_pro_status', "Not Pro." );
	require( 'wpcp_class.settings-api.php' );
	$sections = array(
        array(
            'id' => 'wpcp_basics',
            'title' => __( 'Basics', 'wp-category-permalink' )
        ),
        array(
            'id' => 'wpcp_pro',
            'title' => __( 'Pro', 'wp-category-permalink' )
        )
    );
	$fields = array(
        'wpcp_basics' => array(
            array(
                'name' => 'woocommerce',
                'label' => __( 'WooCommerce Support', 'wp-category-permalink' ),
                'desc' => __( 'Adds support for WooCommerce (Pro only).', 'wp-category-permalink' ),
                'type' => 'checkbox',
                'default' => ""
            ),
        ),
        'wpcp_pro' => array(
            array(
                'name' => 'pro',
                'label' => '',
                'desc' => __( sprintf( 'Status: %s', $pro_status ), 'wp-category-permalink' ),
                'type' => 'html'
            ),
            array(
                'name' => 'subscr_id',
                'label' => __( 'Serial', 'wp-category-permalink' ),
                'desc' => __( '<br />Enter your serial or subscription ID here. If you don\'t have one yet, get one <a target="_blank" href="http://apps.meow.fr/wp-category-permalink/">right here</a>.', 'wp-category-permalink' ),
                'type' => 'text',
                'default' => ""
            ),
        )
    );
    global $wpcp_settings_api;
	$wpcp_settings_api = new WeDevs_Settings_API;
    $wpcp_settings_api->set_sections( $sections );
    $wpcp_settings_api->set_fields( $fields );
    $wpcp_settings_api->admin_init();
}

?>
