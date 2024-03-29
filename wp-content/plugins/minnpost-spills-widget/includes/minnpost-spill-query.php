<?php

/**
* Generate the appropriate WP_Query arguments
*
* @param array $categories
* @param array $terms
* @param bool $use_elasticsearch
* @param string $number_of_posts
* @return array $args
*
*/
function minnpost_spill_get_query_args( $categories = '', $terms = '', $use_elasticsearch = true, $number_of_posts = '' ) {

	$perspectives       = get_category_by_slug( 'perspectives' );
	$featured_columns   = get_term_meta( $perspectives->term_id, '_mp_category_featured_columns', true );
	$fonm               = get_category_by_slug( 'other-nonprofit-media' );
	$featured_columns[] = $perspectives->term_id;
	$featured_columns[] = $fonm->term_id;

	if ( ! empty( $categories ) && is_array( $categories ) ) {
		$category_ids = array();
		foreach ( $categories as $category ) {
			if ( is_numeric( $category ) ) {
				$category_ids[] = $category;
			} else {
				$category_object = get_category_by_slug( $category );
				$category_ids[]  = $category_object->term_id;
			}
		}
	}

	if ( ! empty( $terms ) ) {
		$term_ids = array();
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$term_object = get_term_by( 'name', $term, 'post_tag' );
				$term_ids[]  = $term_object->term_id;
			}
		} else {
			$terms_array = explode( ',', $terms );
			foreach ( $terms_array as $term ) {
				if ( ! empty( $term ) ) {
					$term_object = get_term_by( 'name', $term, 'post_tag' );
					$term_ids[]  = $term_object->term_id;
				}
			}
		}
	}

	$args = array();
	if ( isset( $term_ids ) && isset( $category_ids ) ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => array_values( $featured_columns ),
				'operator' => 'NOT IN',
			),
			array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $category_ids,
					'operator' => 'IN',
				),
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'term_id',
					'terms'    => $term_ids,
					'operator' => 'IN',
				),
			),
		);
	} elseif ( isset( $term_ids ) ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => array_values( $featured_columns ),
				'operator' => 'NOT IN',
			),
			array(
				'taxonomy' => 'post_tag',
				'field'    => 'term_id',
				'terms'    => $term_ids,
				'operator' => 'IN',
			),
		);
	} elseif ( isset( $category_ids ) ) {
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => array_values( $featured_columns ),
				'operator' => 'NOT IN',
			),
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $category_ids,
				'operator' => 'IN',
			),
		);
	}

	if ( '' !== $number_of_posts ) {
		$number_of_posts = (int) $number_of_posts;
	} else {
		$number_of_posts = 5;
	}

	if ( isset( $args ) ) {
		$default_args = array(
			'post_type'      => 'post',
			'posts_per_page' => $number_of_posts,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$args = array_merge( $default_args, $args );
		if ( class_exists( 'ES_WP_Query' ) && ( 'production' === VIP_GO_ENV || ( defined( 'VIP_ENABLE_VIP_SEARCH_QUERY_INTEGRATION' ) && true === VIP_ENABLE_VIP_SEARCH_QUERY_INTEGRATION ) ) && true === $use_elasticsearch ) {
			$args['es'] = true; // elasticsearch on production only
		}

		$args = apply_filters( 'minnpost_spills_query', $args );

		return $args;
	}
}
