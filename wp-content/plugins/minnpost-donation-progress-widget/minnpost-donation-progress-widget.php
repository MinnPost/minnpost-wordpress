<?php
/*
Plugin Name: MinnPost Donation Progress Widget
Plugin URI: #
Description: Create a sidebar widget that displays donation progress, based on a Salesforce Report and a Salesforce Campaign. Requires the Salesforce REST API plugin.
Version: 0.0.1
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: minnpost-donation-progress-widget
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
 
 
class MinnpostDonationProgress_Widget extends WP_Widget {

	/**
	* @var object
	*/
	private $salesforce;

 
	public function __construct() {
		parent::__construct(
			'MinnpostDonationProgress_Widget',
			__( 'MinnPost Donation Progress Widget', 'minnpost-donation-progress-widget' ),
			array(
				'classname'   => 'MinnpostDonationProgress_Widget',
				'description' => __( 'Track donation progress based on a Salesforce campaign report.', 'minnpost-donation-progress-widget' )
			)
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'css_and_js' ) );

	}

	public function css_and_js() {
		wp_enqueue_style( 'minnpost-nimbus', plugins_url( 'fonts/nimbus.css', __FILE__ ), array(), '0.1' );
        wp_enqueue_style( 'minnpost-donation-progress-widget', plugins_url( 'minnpost-donation-progress-widget.css', __FILE__ ), array( 'minnpost-nimbus' ), '0.1' );
        wp_enqueue_script( 'minnpost-donation-progress-widget-js', plugins_url( 'minnpost-donation-progress-widget.js', __FILE__ ), array( 'jquery-core' ), '0.1' );
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

		extract( $args );

		$data = $this->donation_progress_data( $instance['report_id'], $instance['campaign_id'] );

		if ( $data['success'] === TRUE ) {
            $percent = $data['percent_complete'];
            $value = '$' . number_format( $data['value_opportunities'] );
            $goal_int = $data['goal'];
            $goal = '$' . number_format( $goal_int );
            $one_third_int = $goal_int / 3;
            $one_third = '$' . number_format( round( $one_third_int ) );
            $two_thirds = '$' . number_format( round( $one_third_int * 2 ) );
        } else {
            $percent = '';
            $value = '';
            $goal = '';
            $one_third = '';
            $two_thirds = '';
        }

        $html = '';
        $html .= '
        <div class="donation-widget">
            <div class="donation-meter" data-report="00OF0000006ZU9e" data-campaign="701560000001aYpAAI">
              <h2 class="pane-title"><span class="logo">MinnPost</span> <span class="year">2016</span> <span class="drive-name">Year-End Member Drive</span></h2>
              <div class="meter-status">
                <div class="thermometer">';
                if ( $goal !== '' ) {
                  $html .= '<span class="point goal">' . $goal . '</span>';
                }
                  $html .= '<span class="point two-thirds">' . $two_thirds . '</span>
                  <span class="point one-third">' . $one_third . '</span>
                  <span class="glass">
                    <span class="amount"></span>
                  </span>
                  <div class="bulb">
                    <span class="red-circle"></span>
                    <span class="filler">
                      <span></span>
                    </span>
                  </div>
                </div>
                <strong class="total" data-percent="' . $percent . '">' . $value . '</strong>';
                if ( $goal !== '' ) {
                    $html .= '<strong class="drive-goal">Drive goal: <span class="goal">' . $goal . '</span></strong>';
                }
              $html .=' </div>
            </div>
        </div>
        ';

        echo $html;

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

		$instance['report_id'] = strip_tags( $new_instance['report_id'] );
		$instance['campaign_id'] = strip_tags( $new_instance['campaign_id'] );

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

		if ( isset( $instance['report_id'] ) ) {
			$report_id = esc_attr( $instance['report_id'] );
		} else {
			$report_id = '';
		}

		if ( isset( $instance['campaign_id'] ) ) {
			$campaign_id = esc_attr( $instance['campaign_id'] );
		} else {
			$campaign_id = '';
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_id('report_id'); ?>"><?php _e('Salesforce Report ID:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('report_id'); ?>" name="<?php echo $this->get_field_name('report_id'); ?>" type="text" value="<?php echo $report_id; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('campaign_id'); ?>"><?php _e('Salesforce Campaign ID:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('campaign_id'); ?>" name="<?php echo $this->get_field_name('campaign_id'); ?>" type="text" value="<?php echo $campaign_id; ?>" />
		</p>

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
 
/* Register the widget */
add_action( 'widgets_init', function() {
	register_widget( 'MinnpostDonationProgress_Widget' );
});