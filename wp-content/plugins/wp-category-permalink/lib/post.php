<?php

abstract class MWCPPost
{
    /**
    * Update the post metadata
    *
    * @param   string   $new_status  [description]
    * @param   string   $old_status  [description]
    * @param   WP_Post  $post        [description]
    *
    * @return  void
    */
    public static function transition_post_status( $new_status, $old_status, $post )
    {
        if ( !isset( $_POST['permalinkTaxa'] ) || !is_array( $_POST['permalinkTaxa'] ) )
        {
            return;
        }

        $permalink_taxa = $_POST['permalinkTaxa'];

        $permalink_cat = MWCPPost::getPermalinkMeta( $post->ID );

        $permalink_taxa = array_merge( $permalink_cat, $permalink_taxa );

        MWCPPost::setPermalinkMeta( $post->ID, $permalink_taxa );
    }

    /**
    * Return the category set-up in '_category_permalink', otherwise return the default category
    *
    * @param   [type]  $cat   [description]
    * @param   [type]  $cats  [description]
    * @param   [type]  $post  [description]
    *
    * @return  stdClass
    */
    public static function post_link_category( $default, $cats, $post )
    {
        $permalink_cat = MWCPPost::getPermalinkMeta( $post->ID );

        if ( !isset( $permalink_cat['category'] ) )
        {
            return $default;
        }

        // Find the category object for this
        foreach ( $cats as $cat )
        {
            if ( $cat->term_id == $permalink_cat['category'] )
            {
                return $cat;
            }
        }

        return $default;
    }

    /**
    * [post_type_link description]
    *
    * @param   [type]  $url   [description]
    * @param   [type]  $post  [description]
    *
    * @return  [type]
    */
    public static function post_type_link( $url, $post )
    {
        // A list of the only post types we are concerned with.
        $post_types = MWCPPost::post_types();

        if ( !MWCPSettings::is_pro() || !isset( $post_types[$post->post_type] ) )
        {
            return $url;
        }

        // Find and replace these values in the permalink_structure
        $find = array( '%year%', '%monthnum%', '%day%', '%hour%', '%minute%', '%second%', '%post_id%', '%postname%', '%author%' );
        $replace = explode( '|', get_the_date( 'Y|m|d|H|i|s', $post->ID ) );
        $replace[] = $post->ID;
        $replace[] = $post->post_name;
        $replace[] = get_the_author_meta( 'user_nicename', $post->post_author );

        $permalink_taxa = MWCPPost::getPermalinkMeta( $post->ID );

        foreach ( MWCPPost::taxonomies( $post->post_type ) as $info )
        {
            $query_var = $info->query_var;
            $term_id = 0;

            if ( isset( $permalink_taxa[$query_var] ) )
            {
                $term_id = $permalink_taxa[$query_var];
            }
            else
            {
                $terms = get_the_terms( $post->ID, $query_var );
                if ( is_wp_error( $terms ) ) {
                  error_log( $terms->get_error_message() );
                  die( "An error occured in WP Category Permalink." );
                }
                if ( !empty( $terms ) )
                {
                    $term_id = $terms[0]->term_id;
                }
            }

            $find[] = '%' . $query_var . '%';
            $replace[] = self::get_taxonomy_parents( $term_id, $query_var, false, '/', true );
        }

        // Base the url on the post_type slug, not the $url.
        $slug = $post_types[$post->post_type]->rewrite['slug'];
        $slug = trailingslashit( site_url( trailingslashit( $slug ) . '%postname%' ) );

        return str_replace( $find, $replace, $slug );
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
    protected static function get_taxonomy_parents( $id, $taxonomy = 'category', $link = false, $separator = '/', $nicename = false, $visited = array() )
    {
        $chain = array();
        $parent = get_term( $id, $taxonomy );

        if ( is_wp_error( $parent ) )
        {
            return '-';
        }

        $name = $nicename ? $parent->slug : $parent->name;

        if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) )
        {
            $visited[] = $parent->parent;
            $chain[] = self::get_taxonomy_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
        }

        if ( $link )
        {
            $chain[] = '<a href="' . esc_url( get_term_link( $parent,$taxonomy ) ) .
            '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->name ) ) .
            '">' . $name . '</a>';
        }
        else
        {
            $chain[] = $name;
        }

        return implode($separator, $chain);
    }

    public static function post_types()
    {
        static $post_types;

        if ( !$post_types )
        {
            $post_types = get_post_types( array( '_builtin' => 0, 'public' => 1 ), 'objects' );

            if ( !is_array( $post_types ) )
            {
                $post_types = array();
            }
        }

        return $post_types;
    }

    public static function taxonomies( $post_type )
    {
        static $taxa;

        if ( !is_array( $taxa ) )
        {
            $taxa = get_taxonomies( array( 'public' => 1, 'hierarchical' => 1 ), 'objects' );
            $taxa = array_filter( $taxa, create_function( '$a',  'return !!$a->rewrite;' ) );
        }

        $return = array();

        foreach ( $taxa as $taxon )
        {
            if ( in_array( $post_type, $taxon->object_type ) )
            {
                $return[] = $taxon;
            }
        }

        return $return;
    }

    public static function getPermalinkMeta( $post_id )
    {
        $permalink_meta = get_post_meta( $post_id, '_category_permalink', true );

        if ( empty( $permalink_meta ) )
        {
            return array();
        }

        // Old version -> integer, new version -> array
        if ( !is_array( $permalink_meta ) )
        {
            $permalink_meta = array( 'category' => $permalink_meta );
        }

        return $permalink_meta;
    }

    public static function setPermalinkMeta( $post_id, $permalink_meta = null )
    {
        if ( is_array( $permalink_meta ) )
        {
            update_post_meta( $post_id, '_category_permalink', $permalink_meta );
        }
        else
        {
            delete_post_meta( $post_id, '_category_permalink' );
        }
    }
}
