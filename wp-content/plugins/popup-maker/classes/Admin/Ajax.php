<?php
/*******************************************************************************
 * Copyright (c) 2017, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class PUM_Admin_Ajax {

	public static function init() {
		add_action( 'wp_ajax_pum_object_search', array( __CLASS__, 'object_search' ) );
		add_action( 'wp_ajax_pum_process_batch_request', array( __CLASS__, 'process_batch_request' ) );
		// add_action( 'wp_ajax_pum_process_batch_import', array( __CLASS__, 'process_batch_import' ) );
	}

	public static function object_search() {
		$results = array(
			'items'       => array(),
			'total_count' => 0,
		);

		$object_type = sanitize_text_field( $_REQUEST['object_type'] );

		switch ( $object_type ) {
			case 'post_type':
				$post_type = ! empty( $_REQUEST['object_key'] ) ? sanitize_text_field( $_REQUEST['object_key'] ) : 'post';

				$include = ! empty( $_REQUEST['include'] ) ? wp_parse_id_list( $_REQUEST['include'] ) : null;
				$exclude = ! empty( $_REQUEST['exclude'] ) ? wp_parse_id_list( $_REQUEST['exclude'] ) : null;

				if ( ! empty( $include ) && ! empty( $exclude ) ) {
					$exclude = array_merge( $include, $exclude );
				}

				if ( $include ) {
					$include_query = PUM_Helpers::post_type_selectlist_query( $post_type, array(
						'post__in' => $include,
					), true );

					foreach ( $include_query['items'] as $id => $name ) {
						$results['items'][] = array(
							'id'   => $id,
							'text' => $name,
						);
					}

					$results['total_count'] += $include_query['total_count'];
				}

				$query = PUM_Helpers::post_type_selectlist_query( $post_type, array(
					's'              => ! empty( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : null,
					'paged'          => ! empty( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : null,
					'post__not_in'   => $exclude,
					'posts_per_page' => 10,
				), true );

				foreach ( $query['items'] as $id => $name ) {
					$results['items'][] = array(
						'id'   => $id,
						'text' => $name,
					);
				}

				$results['total_count'] += $query['total_count'];

				break;
			case 'taxonomy':
				$taxonomy = ! empty( $_REQUEST['object_key'] ) ? sanitize_text_field( $_REQUEST['object_key'] ) : 'category';

				$include = ! empty( $_REQUEST['include'] ) ? wp_parse_id_list( $_REQUEST['include'] ) : null;
				$exclude = ! empty( $_REQUEST['exclude'] ) ? wp_parse_id_list( $_REQUEST['exclude'] ) : null;

				if ( ! empty( $include ) && ! empty( $exclude ) ) {
					$exclude = array_merge( $include, $exclude );
				}

				if ( $include ) {
					$include_query = PUM_Helpers::taxonomy_selectlist_query( $taxonomy, array(
						'include' => $include,
					), true );

					foreach ( $include_query['items'] as $id => $name ) {
						$results['items'][] = array(
							'id'   => $id,
							'text' => $name,
						);
					}

					$results['total_count'] += $include_query['total_count'];
				}

				$query = PUM_Helpers::taxonomy_selectlist_query( $taxonomy, array(
					'search'  => ! empty( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : null,
					'paged'   => ! empty( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : null,
					'exclude' => $exclude,
					'number'  => 10,
				), true );

				foreach ( $query['items'] as $id => $name ) {
					$results['items'][] = array(
						'id'   => $id,
						'text' => $name,
					);
				}

				$results['total_count'] += $query['total_count'];
				break;
		}
		echo PUM_Utils_Array::safe_json_encode( $results );
		die();
	}


	/**
	 * Handles Ajax for processing a single batch request.
	 */
	public static function process_batch_request() {
		// Batch ID.
		$batch_id = isset( $_REQUEST['batch_id'] ) ? sanitize_key( $_REQUEST['batch_id'] ) : false;

		if ( ! $batch_id ) {
			wp_send_json_error( array(
				'error' => __( 'A batch process ID must be present to continue.', 'popup-maker' ),
			) );
		}

		// Nonce.
		if ( ! isset( $_REQUEST['nonce'] ) || ( isset( $_REQUEST['nonce'] ) && false === wp_verify_nonce( $_REQUEST['nonce'], "{$batch_id}_step_nonce" ) ) ) {
			wp_send_json_error( array(
				'error' => __( 'You do not have permission to initiate this request. Contact an administrator for more information.', 'popup-maker' ),
			) );
		}

		// Attempt to retrieve the batch attributes from memory.
		$batch = PUM_Batch_Process_Registry::instance()->get( $batch_id );

		if ( $batch === false ) {
			wp_send_json_error( array(
				'error' => sprintf( __( '%s is an invalid batch process ID.', 'popup-maker' ), esc_html( $_REQUEST['batch_id'] ) ),
			) );
		}

		$class      = isset( $batch['class'] ) ? sanitize_text_field( $batch['class'] ) : '';
		$class_file = isset( $batch['file'] ) ? $batch['file'] : '';

		if ( empty( $class_file ) || ! file_exists( $class_file ) ) {
			wp_send_json_error( array(
				'error' => sprintf( __( 'An invalid file path is registered for the %1$s batch process handler.', 'popup-maker' ), "<code>{$batch_id}</code>" ),
			) );
		} else {
			require_once $class_file;
		}

		if ( empty( $class ) || ! class_exists( $class ) ) {
			wp_send_json_error( array(
				'error' => sprintf( __( '%1$s is an invalid handler for the %2$s batch process. Please try again.', 'popup-maker' ), "<code>{$class}</code>", "<code>{$batch_id}</code>" ),
			) );
		}

		$step = sanitize_text_field( $_REQUEST['step'] );

		/**
		 * Instantiate the batch class.
		 *
		 * @var PUM_Interface_Batch_Exporter|PUM_Interface_Batch_Process|PUM_Interface_Batch_PrefetchProcess $process
		 */
		if ( isset( $_REQUEST['data']['upload']['file'] ) ) {

			// If this is an import, instantiate with the file and step.
			$file    = sanitize_text_field( $_REQUEST['data']['upload']['file'] );
			$process = new $class( $file, $step );

		} else {

			// Otherwise just the step.
			$process = new $class( $step );

		}

		// Garbage collect any old temporary data.
		// TODO Should this be here? Likely here to prevent case ajax passes step 1 without resetting process counts?
		if ( $step < 2 ) {
			$process->finish();
		}

		$using_prefetch = ( $process instanceof PUM_Interface_Batch_PrefetchProcess );

		// Handle pre-fetching data.
		if ( $using_prefetch ) {
			// Initialize any data needed to process a step.
			$data = isset( $_REQUEST['form'] ) ? $_REQUEST['form'] : array();

			$process->init( $data );
			$process->pre_fetch();
		}

		/** @var int|string|WP_Error $step */
		$step = $process->process_step();

		if ( is_wp_error( $step ) ) {
			wp_send_json_error( $step );
		} else {
			$response_data = array( 'step' => $step );

			// Map fields if this is an import.
			if ( isset( $process->field_mapping ) && ( $process instanceof PUM_Interface_CSV_Importer ) ) {
				$response_data['columns'] = $process->get_columns();
				$response_data['mapping'] = $process->field_mapping;
			}

			// Finish and set the status flag if done.
			if ( 'done' === $step ) {
				$response_data['done']    = true;
				$response_data['message'] = $process->get_message( 'done' );

				// If this is an export class and not an empty export, send the download URL.
				if ( method_exists( $process, 'can_export' ) ) {

					if ( ! $process->is_empty ) {
						$response_data['url'] = pum_admin_url( 'tools', array(
							'step'       => $step,
							'nonce'      => wp_create_nonce( 'pum-batch-export' ),
							'batch_id'   => $batch_id,
							'pum_action' => 'download_batch_export',
						) );
					}
				}

				// Once all calculations have finished, run cleanup.
				$process->finish();
			} else {
				$response_data['done']       = false;
				$response_data['percentage'] = $process->get_percentage_complete();
			}

			wp_send_json_success( $response_data );
		}

	}

}
