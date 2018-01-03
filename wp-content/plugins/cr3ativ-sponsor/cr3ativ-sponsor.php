<?php
/**
 * Plugin Name: Cr3ativ Sponsor Plugin
 * Plugin URI: http://cr3ativ.com/sponsor
 * Description: Custom written plugin for your sponsor needs on your WordPress site.
 * Author: Jonathan Atkinson
 * Author URI: http://cr3ativ.com/
 * Version: 1.2.2
 */

/* Place custom code below this line. */

/* Variables */
$ja_cr3ativ_sponsor_main_file = dirname(__FILE__).'/cr3ativ-sponsor.php';
$ja_cr3ativ_sponsor_directory = plugin_dir_url($ja_cr3ativ_sponsor_main_file);
$ja_cr3ativ_sponsor_path = dirname(__FILE__);

/* Add css file */
function creativ_sponsor_add_scripts() {
	global $ja_cr3ativ_sponsor_directory, $ja_cr3ativ_sponsor_path;
		wp_enqueue_style('creativ_sponsor', $ja_cr3ativ_sponsor_directory.'css/cr3ativsponsor.css');
}
		
add_action('wp_enqueue_scripts', 'creativ_sponsor_add_scripts');


////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////       WP Default Functionality       ////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
add_theme_support( 'post-thumbnails' );


////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////            Theme Options Metabox            /////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
include_once( 'includes/meta_box.php' );


////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////     Text Domain     /////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
load_plugin_textdomain('cr3at_sponsor', false, basename( dirname( __FILE__ ) ) . '/languages' );


////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////     Careers post type     ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
add_action('init', 'create_cr3ativsponsor');

function create_cr3ativsponsor() {

	$labels = array(
		'name'               => __( 'Sponsors', 'post type general name', 'cr3at_sponsor' ),
		'singular_name'      => __( 'Sponsor', 'post type singular name', 'cr3at_sponsor' ),
		'menu_name'          => __( 'Sponsor', 'admin menu', 'cr3at_sponsor' ),
		'add_new'            => __( 'Add New Sponsor', 'sponsor', 'cr3at_sponsor' ),
		'add_new_item'       => __( 'Add New Sponsor', 'cr3at_sponsor' ),
		'new_item'           => __( 'New Sponsor', 'cr3at_sponsor' ),
		'edit_item'          => __( 'Edit Sponsor', 'cr3at_sponsor' ),
		'view_item'          => __( 'View Sponsor', 'cr3at_sponsor' ),
		'all_items'          => __( 'All Sponsors', 'cr3at_sponsor' ),
		'search_items'       => __( 'Search Sponsors', 'cr3at_sponsor' ),
		'not_found'          => __( 'No sponsors found.', 'cr3at_sponsor' ),
		'not_found_in_trash' => __( 'No sponsors found in Trash.', 'cr3at_sponsor' )
	);
    	$cr3ativsponsor_args = array(
        	'labels' => $labels,
        	'public' => true,
            'menu_icon' => 'dashicons-businessman',
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
            'rewrite' => true, 
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail', 'page-attributes')
        );
       

        
register_post_type('cr3ativsponsor',$cr3ativsponsor_args);
	}


$cr3ativsponsor_fields = array(
	array(
            'label' => __('Company URL', 'cr3at_sponsor'),
            'desc' => __('When this field lists a url, the logo or company name will link to this url and open in a new window.', 'cr3at_sponsor'),
            'id' => 'cr3ativ_sponsorurl',
            'type' => 'text',
            'std' => ""
        )
);
 
$cr3ativsponsor_box = new cr3ativsponsor_add_meta_box( 'cr3ativsponsor_box', __('Sponsor Information', 'cr3at_sponsor'), $cr3ativsponsor_fields, 'cr3ativsponsor', true );


////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////     Custom taxonomies     ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////


add_action( 'init', 'cr3ativsponsor_level', 0 );
function cr3ativsponsor_level()	{
	register_taxonomy( 
		'cr3ativsponsor_level', 
		'cr3ativsponsor', 
			array( 
				'hierarchical' => true, 
				'label' => __('Sponsor Level', 'cr3at_sponsor'),
				'query_var' => true, 
				'rewrite' => true,
			) 
	);
 
}

add_filter( 'manage_edit-cr3ativsponsor_columns', 'my_edit_cr3ativsponsor_columns' ) ;

function my_edit_cr3ativsponsor_columns( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
        'sponsorimage' => __( 'Sponsor Logo' , 'cr3at_sponsor'),
		'title' => __( 'Sponsor Name', 'cr3at_sponsor' ),
        'sponsor_website' => __( 'Sponsor Website', 'cr3at_sponsor' ),
        'sponsor_level' => __( 'Sponsor Level' , 'cr3at_sponsor')
	);

	return $columns;
}

add_action( 'manage_cr3ativsponsor_posts_custom_column', 'my_manage_cr3ativsponsor_columns', 10, 2 );

function my_manage_cr3ativsponsor_columns( $column, $post_id ) {
	global $post;
            $cr3ativ_sponsorurl = get_post_meta($post->ID, 'cr3ativ_sponsorurl', $single = true); 
	switch( $column ) {
        
		case 'sponsorimage' :

			 the_post_thumbnail('thumbnail');
			break;
        
		case 'sponsor_website' :

             printf( $cr3ativ_sponsorurl ); 
			break;
        
		case 'level' :

			 if ( $cr3ativ_conflevels ) { 
				
	        	foreach ( $cr3ativ_conflevels as $cr3ativ_conflevel ) :
	        	
	        		$level = get_post($cr3ativ_conflevel);

	        		echo '<a href="'. admin_url() .'edit.php?post_type=cr3ativlevel">'. $level->post_title .'</a><br/>'; 
				
				endforeach; 
				
			}
			break;
        
		case 'sponsor_level' :

			$terms = get_the_terms( $post_id, 'cr3ativsponsor_level' );

			/* If terms were found. */
			if ( !empty( $terms ) ) {

				$out = array();

				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'cr3ativsponsor_level' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'cr3ativsponsor_level', 'display' ) )
					);
				}

				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
			}

			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}


////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////              sponsor widget                  /////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
include_once( 'includes/sponsor-widget.php' );



////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////       Shortcode Loop      ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////


// Taxonomy category shortcode
function sponsor_level_cat_func($atts, $content) {
    extract(shortcode_atts(array(
            'columns'  => '4',
            'image'    => 'yes',
            'title'    => 'yes',
            'link'     => 'yes',
            'bio'      => 'yes',
            'show'      => '',
            'orderby'      => '',
            'order'     =>  '',
            'category'      => ''
            ), $atts));

    global $post;

    if( $category == ('') ) { $category = 'all';} else { };
    if( $orderby == ('') ) { $orderby = 'rand';} else { };
    if( $order == ('') ) { $order = 'asc';} else { };
    if( $category != ('all') ) {
		$args = array(
		'post_type' => 'cr3ativsponsor',
        'posts_per_page' => $show,
        'order' => $order,
        'orderby' => $orderby,
        'tax_query' => array(
            array(
                'taxonomy' => 'cr3ativsponsor_level',
                'field' => 'slug',
                'terms' => array( $category)
            )
        ));
   } else {
		$args = array(
		'post_type' => 'cr3ativsponsor',
        'order' => $order,
        'orderby' => $orderby,
        'posts_per_page' => $show
		);
   }
   
    query_posts($args);
    
    $output = '';
    $temp_title = '';
    $temp_link = '';
    $temp_excerpt = '';
    $temp_image = '';
    
     $output .= '<div class="cr3_sponsorwrapper">';
    
    if (have_posts($args)) : while (have_posts()) : the_post();
    
        $temp_title = get_the_title($post->ID);
        $temp_sponsorurl = get_post_meta($post->ID, 'cr3ativ_sponsorurl', $single = true);
        $temp_excerpt = get_the_content($post->ID);
        $temp_image = get_the_post_thumbnail($post->ID, 'full');
    
        if( $columns == '1' ) {
        $output .= '<div class="ones-column">';
            ;} elseif ( $columns == '2' ) {
        $output .= '<div class="twos-column">';  
            ;} elseif ( $columns == '3' ) {    
        $output .= '<div class="threes-column">';     
            ;} else {    
        $output .= '<div class="fours-column">';  
        }
        
     if( $image == 'yes' ) { 
         if( $link == 'yes' ) { 
            $output .= '<a href="'.$temp_sponsorurl.'" target="_blank"><div class="cr3_sponsor_image">'.$temp_image.'</div></a>';
     ;} else {
             $output .= '<div class="cr3_sponsor_image">'.$temp_image.'</div>';
         ;}
         
     ;} 
     if( $title == 'yes' ) { 
         if( $link == 'yes' ) { 
            $output .= '<h2 class="cr3_sponsorname"><a href="'.$temp_sponsorurl.'" target="_blank">'.$temp_title.'</a></h2>';
     ;} else {
             $output .= '<h2 class="cr3_sponsorname">'.$temp_title.'</h2>';
         ;}
         
     ;} 

     if( $bio == 'yes' ) { 
         $output .= '<p>'.$temp_excerpt.'</p>';  
     ;}

        $output .= '</div>';
    
    endwhile; 

   endif;
    
    $output .= '</div>';
   
   wp_reset_query();
   return $output;
}
add_shortcode('sponsor_level', 'sponsor_level_cat_func');

?>