<?php
/*
Plugin Name: MinnPost Spills Widget
Plugin URI: #
Description: A plugin containing various widgets created in a TutsPlus series on WordPress widgets
Version: 0.0.1
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: minnpost-spills-widget
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
 
 
class MinnpostSpills_Widget extends WP_Widget {

	/**
	* @var object
	*/
	private $salesforce;

 
	public function __construct() {
		parent::__construct(
			'MinnpostSpills_Widget', __( 'MinnPost Spills Widget', 'minnpost-spills-widget' ),
			array(
				'classname'   => 'MinnpostSpills_Widget',
				'description' => __( 'Posts from a group of categories.', 'minnpost-spills-widget' )
			)
		);

		// Register hooks
		add_action('admin_print_scripts', array( $this, 'add_script') );
		add_action('admin_head', array( $this, 'add_script_config') );

	}

	/**
	 * Add script to admin page
	 */
	function add_script() {
	    // Build in tag auto complete script
	    wp_enqueue_script( 'suggest' );
	}

	/**
	 * add script to admin page
	 */
	function add_script_config() {
	?>

	    <script>
	    // Function to add auto suggest
	    function setSuggest(id) {
	        jQuery('#' + id).suggest("<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag", {multiple:true, multipleSep: ","});
	    }
	    </script>
	<?php
	}

	/**  
	* Front-end display of widget.
	*
	* @see WP_Widget::widget()
	*
	* @param array $args     Widget arguments.
	* @param array $instance Saved values from database.
	*/
	public function widget( $args, $instance ) {
		error_log('run spill widget');

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$categories = $instance['widget_categories'];
		$terms = $instance['widget_terms'];
		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		//echo $message;
		echo $after_widget;

	}
 
  
	/**
	* Sanitize widget form values as they are saved.
	*
	* @see WP_Widget::update()
	*
	* @param array $new_instance Values just sent to be saved.
	* @param array $old_instance Previously saved values from database.
	*
	* @return array Updated safe values to be saved.
	*/
	public function update( $new_instance, $old_instance ) {        

		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['widget_categories'] = $new_instance['widget_categories'];
		$instance['widget_terms'] = $new_instance['widget_terms'];

		return $instance;

	}
  
	/**
	* Back-end widget form.
	*
	* @see WP_Widget::form()
	*
	* @param array $instance Previously saved values from database.
	*/
	public function form( $instance ) {    

		if ( isset( $instance['title'] ) ) {
			$title = sanitize_text_field( $instance['title'] );
		} else {
			$title = '';
		}

		if ( isset( $instance['widget_categories'] ) ) {
			$categories = $instance['widget_categories'];
		} else {
			$categories = false;
		}

		if ( isset( $instance['terms'] ) ) {
			$terms = $instance['terms'];
		} else {
			$terms = '';
		}

		// Instantiate the walker passing name and id as arguments to constructor
        $category_walker = new Walker_Category_Checklist_Widget(
            $this->get_field_name( 'widget_categories' ), 
            $this->get_field_id( 'widget_categories' )
        );
        $term_walker = new Walker_Category_Checklist_Widget(
            $this->get_field_name( 'widget_terms' ), 
            $this->get_field_id( 'widget_terms' )
        );

		?>

		<div>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</div>
		<div>
			<label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:'); ?></label> 
			<?php $checked_ontop = true; ?>
			<ul class="categorychecklist" style="height: 200px; overflow: auto; border: 1px solid #ddd; background: #fdfdfd; padding: 0 0.9em;">
				<?php wp_category_checklist( 0, 0, $categories, false, $category_walker, $checked_ontop ); ?>
			</ul>
		</div>

		<div>
			<label for="<?php echo $this->get_field_id('terms'); ?>"><?php _e('Terms:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('terms'); ?>" name="<?php echo $this->get_field_name('terms'); ?>" type="text" value="<?php echo $terms; ?>" />
		</div>
		<script>
		setSuggest(<?php echo $this->get_field_id('terms'); ?>);
		</script>

		<?php 
	}

	/**
	* Load the Salesforce object
	* Also make it available to this whole class
	*
	* @return $this->salesforce
	*
	*/
    private function salesforce() {
		// get the base class
		if ( ! function_exists( 'is_plugin_active' ) ) {
     		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
     	}
		if ( is_plugin_active('salesforce-rest-api/salesforce-rest-api.php') ) {
			require_once plugin_dir_path( __FILE__ ) . '../salesforce-rest-api/salesforce-rest-api.php';
			$salesforce = Salesforce_Rest_API::get_instance();
			$this->salesforce = $salesforce;
			return $this->salesforce;
		}
	}

	/**
	* Load the data for the campaign's progress
	*
	* @see WP_Widget::form()
	*
	* @param array $instance Previously saved values from database.
	*/
	private function donation_progress_data( $report_id, $campaign_id ) {
        if ( is_object( $this->salesforce ) ) {
            $salesforce_api = $this->salesforce->salesforce['sfapi'];
        } else {
            $salesforce = $this->salesforce();
            $salesforce_api = $salesforce->salesforce['sfapi'];
        }

        if ( is_object( $salesforce_api ) ) {
            // this is a report id
            $report_result = $salesforce_api->run_analytics_report( $report_id, TRUE );
            $campaign_result = $salesforce_api->object_read( 'Campaign', $campaign_id );

            if ( isset( $campaign_result['data']['ExpectedRevenue'] ) ) {
                $goal = $campaign_result['data']['ExpectedRevenue'];
            } else {
                $goal = '';
            }

            if ( $report_result['data']['attributes']['status'] === 'Success' ) {
                $factmap = $report_result['data']['factMap'];
                foreach ( $factmap as $array ) {
                    if ( isset( $array['aggregates'] ) ) {
                        $success = TRUE;
                        $value = $array['aggregates'][1]['value'];
                        break;
                    }
                }
            } elseif ( $report_result['data']['attributes']['status'] === 'Running' || $report_result['data']['attributes']['status'] === 'New' ) {
                $success = 'running';
                $value = 0;
            }
        } else {
            $success = FALSE;
            $value = 0;
        }

        $data = array(
            'success' => $success,
            'value_opportunities' => $value,
            'goal' => $goal,
            'percent_complete' => ( $value / 10000 ) * 100
        );

        return $data;

    }
     
}

// This is required to be sure Walker_Category_Checklist class is available
require_once ABSPATH . 'wp-admin/includes/template.php';
/**
 * Custom walker to print category checkboxes for widget forms
 */
class Walker_Category_Checklist_Widget extends Walker_Category_Checklist {

    private $name;
    private $id;

    function __construct( $name = '', $id = '' ) {
        $this->name = $name;
        $this->id = $id;
    }

    function start_el( &$output, $cat, $depth = 0, $args = array(), $id = 0 ) {
        extract( $args );
        if ( empty( $taxonomy ) ) $taxonomy = 'category';
        $class = in_array( $cat->term_id, $popular_cats ) ? ' class="popular-category"' : '';
        $id = $this->id . '-' . $cat->term_id;
        $checked = checked( in_array( $cat->term_id, $selected_cats ), true, false );
        $output .= "\n<li id='{$taxonomy}-{$cat->term_id}'$class>" 
            . '<label class="selectit"><input value="' 
            . $cat->term_id . '" type="checkbox" name="' . $this->name 
            . '[]" id="in-'. $id . '"' . $checked 
            . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' 
            . esc_html( apply_filters( 'the_category', $cat->name ) ) 
            . '</label>';
      }
}
 
/* Register the widget */
add_action( 'widgets_init', function() {
	register_widget( 'MinnpostSpills_Widget' );
});