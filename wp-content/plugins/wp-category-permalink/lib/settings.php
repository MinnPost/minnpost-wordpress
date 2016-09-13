<?php

abstract class MWCPSettings
{
    /**
    * Get an option value or default if not set.
    *
    * @param   string  $option   [description]
    * @param   string  $section  [description]
    * @param   mixed   $default  [description]
    *
    * @return  mixed
    */
    public static function get_option( $option, $section = 'wpcp_default', $default = '' )
    {
        $options = get_option( $section );

        if ( !isset( $options[$option] ) )
        {
            return $default;
        }

        if ( $options[$option] == 'off' )
        {
            return false;
        }

        if ( $options[$option] == 'on' )
        {
            return true;
        }

        return $options[$option];
    }

    /**
    * Whether or not this is the pro version of the plugin
    *
    * @return  boolean
    */
    public static function is_pro()
    {
        $validated = get_transient( 'wpcp_validated' );

        if ( $validated )
        {
            $serial = get_option( 'wpcp_pro_serial' );

            return !empty( $serial );
        }

        $subscr_id = get_option( 'wpcp_pro_serial', '' );

        if ( !empty( $subscr_id ) )
        {
            return self::validate_pro( self::get_option( 'subscr_id', 'wpcp_pro', array() ) );
        }

        return false;
    }

    /**
    * Get an instance of the Settings API
    *
    * @return  WeDevs_Settings_API
    */
    protected static function get_api()
    {
        static $api;

        if (!$api)
        {
            require_once 'wpcp_class.settings-api.php';

            $api = new WeDevs_Settings_API;
        }

        return $api;
    }

    /**
    * Add the Category Permalink settings to the menu.
    */
    public static function add_menu_item()
    {
        add_options_page( 'Category Permalink', 'Category Permalink', 'manage_options', 'wpcp_settings', array( 'MWCPSettings', 'render' ) );
    }

    /**
    * Render the settings page
    *
    * @return  void
    */
    public static function render()
    {
        require_once 'meow_footer.php';
        $api = self::get_api();
        $hide_ads = self::get_option( 'hide_ads', 'wpcp_basics', false );

        ?>
        <div class="wrap">
            <?php echo $hide_ads ? "" : jordy_meow_donation( true ); ?>
            <div id="icon-options-general" class="icon32"><br></div>
            <h2>WP Category Permalink<?php echo by_jordy_meow( $hide_ads ); ?></h2>
            <p>For this plugin to work, don't forget that you need to use a Permalink Structure that includes %category%. This %category% will be handled by the plugin. In the Pro version, the plugin will also handle it for your custom post types and taxonomies (such as in gallery plugins, WooCommerce, etc). More information about Permalinks and about the Pro version here: <a target="_blank" href="http://apps.meow.fr/wp-category-permalink/">WP Category Permalink</a>.</p>
            <?php
            $api->show_navigation();
            $api->show_forms();
            ?>
        </div>
        <?php

        echo jordy_meow_footer();
    }

    public static function init()
    {
        if ( isset( $_POST ) && is_array( $_POST ) )
        {
            self::handle_input( $_POST );
        }

        $sections = array(
            array(
                'id' => 'wpcp_basics',
                'title' => __( 'Settings', 'wp-category-permalink' )
            ),
            array(
                'id' => 'wpcp_default',
                'title' => __( 'Permalinks', 'wp-category-permalink' )
            ),
            array(
                'id' => 'wpcp_pro',
                'title' => __( 'Serial Key (Pro)', 'wp-category-permalink' )
            ),
        );

        $fields = array(
            'wpcp_basics' => self::get_fields_basics(),
            'wpcp_default' => self::get_fields_permalinks(),
            'wpcp_pro' => self::get_fields_pro(),
        );

        MWCPSettings::get_api()
        ->set_sections($sections)
        ->set_fields($fields)
        ->admin_init();
    }

    protected static function get_fields_basics()
    {
        $fields = array(
            array(
                'name' => 'hide_permalink',
                'label' => __( 'Hide Permalinks', 'wp-category-permalink' ),
                'desc' => __( 'Enable<br /><small>In the listing of posts (or any other post type), don\'t display the permalink below the title.</small>', 'wp-category-permalink' ),
                'type' => 'checkbox',
                'default' => 'false'
            ),
            array(
                'name' => 'hide_ads',
                'label' => __( 'Hide Ads', 'wp-category-permalink' ),
                'desc' => __( 'Enable<br /><small>Hide the ad and the Flattr button.</small>', 'wp-category-permalink' ),
                'type' => 'checkbox',
                'default' => 'false'
            ),
        );

        return $fields;
    }

    protected static function get_fields_pro()
    {
        $pro_status = get_option( 'wpcp_pro_status', 'Not Pro.' );

        $fields = array(
            array(
                'name' => 'pro',
                'label' => '',
                'desc' => __( sprintf( 'Status: %s', $pro_status ), 'wp-category-permalink' ),
                'type' => 'html',
            ),
            array(
                'name' => 'subscr_id',
                'label' => __( 'Serial', 'wp-category-permalink' ),
                'desc' => __( '<br />Enter your serial or subscription ID here. If you don\'t have one yet, get one <a target="_blank" href="http://apps.meow.fr/wp-category-permalink/">right here</a>.', 'wp-category-permalink' ),
                'type' => 'text',
                'default' => '',
            ),
        );

        return $fields;
    }

    protected static function get_fields_permalinks()
    {
        $fields = array();
        $post_types = MWCPPost::post_types();
        $map = create_function( '$a', 'return $a->query_var;' );

        $fields[] = array(
            'name' => 'permalinks_desc',
            'label' => __( "" ),
            'type' => 'html',
            'desc' => "The permalinks listed below are the ones based on custom post type and taxonomy created by your theme on another plugins. They are handled in the Pro version.",
        );

        foreach ( $post_types as $type => $post_info )
        {
            $taxa = MWCPPost::taxonomies( $type );

            if ( empty( $taxa ) )
            {
                continue;
            }

            $query_vars = array_map( $map, $taxa );

            $desc = __( 'Permalink: <b>' ) . $post_info->rewrite['slug'] . '/</b> + <i>' . $post_info->query_var  . '</i><br><small>' .
              __( 'Post Type: ' ) . $type . '<br />' .
              __( 'Taxonomies: ' ) . implode( ' ', $query_vars ) . '</small>';

            $fields[] = array(
                'name' => 'permalink.' . $type,
                'label' => __( $post_info->label ),
                'type' => 'html',
                'desc' => $desc,
            );
        }

        return $fields;
    }

    protected static function handle_input( array $post = array() )
    {
        if ( isset( $post['wpcp_pro'] ) )
        {
            self::validate_pro( $post['wpcp_pro']['subscr_id'] );
        }
    }

    protected static function validate_pro( $subscr_id )
    {
        if ( empty( $subscr_id ) )
        {
            delete_option( 'wpcp_pro_serial', '' );
            delete_option( 'wpcp_pro_status', '' );
            set_transient( 'wpcp_validated', false, 0 );

            return false;
        }

        require_once ABSPATH . WPINC . '/class-IXR.php';
        require_once ABSPATH . WPINC . '/class-wp-http-ixr-client.php';

        $client = new WP_HTTP_IXR_Client( 'http://apps.meow.fr/xmlrpc.php' );
        $client->useragent = 'MeowApps';

        if ( !$client->query( 'meow_sales.auth', $subscr_id, 'category-permalink', get_site_url() ) )
        {
            update_option( 'wpcp_pro_serial', '' );
            update_option( 'wpcp_pro_status', 'A network error: ' . $client->getErrorMessage() );
            set_transient( 'wpcp_validated', false, 0 );

            return false;
        }

        $post = $client->getResponse();

        if ( !$post['success'] )
        {
            if ( $post['message_code'] == 'NO_SUBSCRIPTION' )
            {
                $status = __( 'Your serial does not seem right.' );
            }
            else if ( $post['message_code'] == 'NOT_ACTIVE' ) {
                $status = __( 'Your subscription is not active.' );
            }
            else if ( $post['message_code'] == 'TOO_MANY_URLS' ) {
                $status = __( 'Too many URLs are linked to your subscription.' );
            }
            else {
                $status = 'There is a problem with your subscription.';
            }

            update_option( 'wpcp_pro_serial', '' );
            update_option( 'wpcp_pro_status', $status );
            set_transient( 'wpcp_validated', false, 0 );

            return false;
        }

        set_transient( 'wpcp_validated', $subscr_id, 3600 * 24 * 100 );
        update_option( 'wpcp_pro_serial', $subscr_id );
        update_option( 'wpcp_pro_status', __( 'Your subscription is enabled.' ) );

        return true;
    }
}
