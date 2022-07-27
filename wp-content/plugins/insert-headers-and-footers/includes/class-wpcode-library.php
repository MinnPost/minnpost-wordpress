<?php
/**
 * Load snippets from the wpcode.com snippet library.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Library.
 */
class WPCode_Library {

	/**
	 * The endpoint where everything starts.
	 *
	 * @var string
	 */
	public $library_url = 'https://cdn.wpcode.com/library/api/get';

	/**
	 * Key for storing snippets in the db.
	 *
	 * @var string
	 */
	private $cache_key = 'snippets';

	/**
	 * The key for storing individual snippets.
	 *
	 * @var string
	 */
	private $snippet_key = 'snippets/snippet';

	/**
	 * The base cache folder for this class.
	 *
	 * @var string
	 */
	private $cache_folder = 'library';

	/**
	 * The data.
	 *
	 * @var array
	 */
	private $data;

	/**
	 * The default time to live for libary items that are cached.
	 *
	 * @var int
	 */
	private $ttl = DAY_IN_SECONDS;

	/**
	 * Array of snippet ids that were already loaded from the library.
	 *
	 * @var array
	 */
	private $library_snippets;

	/**
	 * Grab all the available categories from the library.
	 *
	 * @return array
	 */
	public function get_data() {
		if ( ! isset( $this->data ) ) {
			$this->data = $this->load_data();
		}

		return $this->data;
	}


	/**
	 * Grab data from the cache.
	 *
	 * @param string $key The key used to grab from cache.
	 * @param int    $ttl The time to live for cached data, defaults to class ttl.
	 *
	 * @return array|false
	 */
	public function get_from_cache( $key, $ttl = 0 ) {
		if ( empty( $ttl ) ) {
			$ttl = $this->ttl;
		}

		$data = wpcode()->file_cache->get( $this->cache_folder . '/' . $key, $ttl );

		if ( isset( $data['error'] ) && isset( $data['time'] ) ) {
			if ( $data['time'] + 10 * MINUTE_IN_SECONDS < time() ) {
				return false;
			} else {
				return $this->get_empty_array();
			}
		}

		return $data;
	}

	/**
	 * Load the library data either from the server or from cache.
	 *
	 * @return array
	 */
	public function load_data() {
		$this->data = $this->get_from_cache( $this->cache_key );

		if ( false === $this->data ) {
			$this->data = $this->get_from_server();
		}

		return $this->data;
	}


	/**
	 * Get data from the server.
	 *
	 * @return array
	 */
	private function get_from_server() {
		$url = $this->library_url;

		if ( empty( $url ) ) {
			// Didn't know what to grab.
			return $this->get_empty_array();
		}

		$request = wp_remote_get( $url );

		if ( is_wp_error( $request ) ) {
			return $this->save_temporary_response_fail( $this->cache_key );
		}

		$response_code = wp_remote_retrieve_response_code( $request );

		$data = wp_remote_retrieve_body( $request );
		if ( $response_code > 299 ) {
			// Temporary error so cache for just 10 minutes and then try again.
			$data = '';
		}

		$data = $this->process_response( $data );

		if ( empty( $data['snippets'] ) ) {
			return $this->save_temporary_response_fail( $this->cache_key );
		}

		$this->save_to_cache( $this->cache_key, $data );

		return $data;
	}

	/**
	 * When we can't fetch from the server we save a temporary error => true file to avoid
	 * subsequent requests for a while. Returns a properly formatted array for frontend output.
	 *
	 * @param string $key The key used for storing the data in the cache.
	 *
	 * @return array
	 */
	public function save_temporary_response_fail( $key ) {
		$data = array(
			'error' => true,
			'time'  => time(),
		);
		$this->save_to_cache( $key, $data );

		return $this->get_empty_array();
	}

	/**
	 * Get an empty array for a consistent response.
	 *
	 * @return array[]
	 */
	public function get_empty_array() {
		return array(
			'categories' => array(),
			'snippets'   => array(),
		);
	}

	/**
	 * Save to cache.
	 *
	 * @param string      $key The key used to store the data in the cache.
	 * @param array|mixed $data The data that will be stored.
	 *
	 * @return void
	 */
	public function save_to_cache( $key, $data ) {
		wpcode()->file_cache->set( $this->cache_folder . '/' . $key, $data );
	}

	/**
	 * Generic handler for grabbing data by slug. Either all categories or the category slug.
	 *
	 * @param string $data Response body from server.
	 *
	 * @return array
	 */
	private function process_response( $data ) {
		$response = json_decode( $data, true );
		if ( ! isset( $response['status'] ) || 'success' !== $response['status'] ) {
			return $this->get_empty_array();
		}

		return $response['data'];
	}

	/**
	 * Get a cache key for a specific snippet id.
	 *
	 * @param int $id The snippet id.
	 *
	 * @return string
	 */
	public function get_snippet_cache_key( $id ) {
		return $this->snippet_key . '_' . $id;
	}

	/**
	 * Create a new snippet by the library id.
	 * This grabs the snippet by its id from the snippet library site and creates
	 * a new snippet on the current site using the response.
	 *
	 * @param int $library_id The id of the snippet on the library site.
	 *
	 * @return false|WPCode_Snippet
	 */
	public function create_new_snippet( $library_id ) {

		$snippet_data = $this->grab_snippet_from_api( $library_id );

		if ( ! $snippet_data ) {
			return false;
		}

		$snippet = new WPCode_Snippet( $snippet_data );

		$snippet->save();

		delete_transient( 'wpcode_used_library_snippets' );

		return $snippet;
	}

	/**
	 * Grab a snippet data from the API.
	 *
	 * @param int $library_id The id of the snippet in the Library api.
	 *
	 * @return array|array[]|false
	 */
	public function grab_snippet_from_api( $library_id ) {
		$data = $this->get_data();

		if ( empty( $data['links']['snippet'] ) ) {
			return false;
		}

		$url = add_query_arg(
			array(
				'site' => rawurlencode( site_url() ),
			),
			trailingslashit( esc_url( $data['links']['snippet'] ) ) . $library_id
		);

		$snippet_request = wp_remote_get( $url );

		$response_code = wp_remote_retrieve_response_code( $snippet_request );

		if ( $response_code > 299 ) {
			return false;
		}

		$snippet_data = $this->process_response( wp_remote_retrieve_body( $snippet_request ) );

		if ( empty( $snippet_data ) ) {
			return false;
		}

		return $snippet_data;
	}

	/**
	 * Get all the snippets that were created from the library, by library ID.
	 * Results are cached in a transient.
	 *
	 * @return array
	 */
	public function get_used_library_snippets() {

		if ( isset( $this->library_snippets ) ) {
			return $this->library_snippets;
		}

		$snippets_from_library = get_transient( 'wpcode_used_library_snippets' );

		if ( false === $snippets_from_library ) {
			$snippets_from_library = array();

			$args     = array(
				'post_type'   => 'wpcode',
				'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => '_wpcode_library_id',
						'compare' => 'EXISTS',
					),
				),
				'fields'      => 'ids',
				'post_status' => 'any',
				'nopaging'    => true,
			);
			$snippets = get_posts( $args );

			foreach ( $snippets as $snippet_id ) {
				$library_id                           = absint( get_post_meta( $snippet_id, '_wpcode_library_id', true ) );
				$snippets_from_library[ $library_id ] = $snippet_id;
			}

			set_transient( 'wpcode_used_library_snippets', $snippets_from_library );
		}

		$this->library_snippets = $snippets_from_library;

		return $this->library_snippets;

	}
}
