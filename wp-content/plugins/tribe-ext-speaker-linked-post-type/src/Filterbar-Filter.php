<?php

/**
 * Class Tribe__Events__Filterbar__Filters__Speaker
 *
 * Based on Tribe__Events__Filterbar__Filters__Organizer
 */
class Tribe__Events__Filterbar__Filters__Speaker extends Tribe__Events__Filterbar__Filter {
	public $type = 'select';

	public function get_admin_form() {
		$title = $this->get_title_field();
		$type  = $this->get_multichoice_type_field();

		return $title . $type;
	}

	protected function get_values() {
		/** @var wpdb $wpdb */
		global $wpdb;
		// get speaker IDs associated with published events
		$speaker_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT m.meta_value FROM {$wpdb->postmeta} m INNER JOIN {$wpdb->posts} p ON p.ID=m.post_id WHERE p.post_type=%s AND p.post_status='publish' AND m.meta_key=%s AND m.meta_value > 0",
				Tribe__Events__Main::POSTTYPE,
				Tribe__Extension__Speaker_Linked_Post_Type::instance()->get_linked_post_type_custom_field_key()
			)
		);
		array_filter( $speaker_ids );
		if ( empty( $speaker_ids ) ) {
			return array();
		}

		/**
		 * Filter Total Speakers in Filter Bar
		 * Use this with caution, this will load speakers on the front-end, may be slow
		 * The base limit is 200 for safety reasons
		 *
		 *
		 * @parm int  200 posts per page limit
		 * @parm array $speaker_ids   ids of speakers attached to events
		 */
		$limit = apply_filters( Tribe__Extension__Speaker_Linked_Post_Type::POST_TYPE_KEY . '_filter_bar_limit', 200, $speaker_ids );

		$speakers = get_posts(
			array(
				'post_type'        => Tribe__Extension__Speaker_Linked_Post_Type::POST_TYPE_KEY,
				'posts_per_page'   => $limit,
				'suppress_filters' => false,
				'post__in'         => $speaker_ids,
				'post_status'      => 'publish',
				'orderby'          => 'title',
				'order'            => 'ASC',
			)
		);

		$speakers_array = array();
		foreach ( $speakers as $speaker ) {
			$speakers_array[] = array(
				'name'  => $speaker->post_title,
				'value' => $speaker->ID,
			);
		}

		return $speakers_array;
	}

	protected function setup_join_clause() {
		global $wpdb;
		$this->joinClause = $wpdb->prepare(
			"INNER JOIN {$wpdb->postmeta} AS speaker_filter ON ({$wpdb->posts}.ID = speaker_filter.post_id AND speaker_filter.meta_key=%s)",
			Tribe__Extension__Speaker_Linked_Post_Type::instance()->get_linked_post_type_custom_field_key()
		);
	}

	protected function setup_where_clause() {
		if ( is_array( $this->currentValue ) ) {
			$speaker_ids = implode( ',', array_map( 'intval', $this->currentValue ) );
		} else {
			$speaker_ids = esc_attr( $this->currentValue );
		}

		$this->whereClause = " AND speaker_filter.meta_value IN ($speaker_ids) ";
	}
}
