<?php
/**
 * PostStates Registration Manager
 *
 * @package     ArrayPress\WP\Register
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 */

declare( strict_types=1 );

namespace ArrayPress\WP\Register;

use WP_Error;
use WP_Post;

/**
 * Class PostStates
 *
 * Manages the registration and display of custom post states in WordPress.
 *
 * @since 1.0.0
 */
class PostStates {

	/**
	 * Collection of registered post states
	 *
	 * @var array
	 */
	private array $states = [];

	/**
	 * Debug mode status
	 *
	 * @var bool
	 */
	private bool $debug = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->debug = defined( 'WP_DEBUG' ) && WP_DEBUG;
		add_filter( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );
	}

	/**
	 * Register post states
	 *
	 * @param array  $states        Array of post states
	 * @param string $option_getter Optional custom option getter function name
	 *
	 * @return WP_Error|true
	 */
	public function register( array $states, string $option_getter = 'get_option' ) {
		if ( empty( $states ) ) {
			return new WP_Error(
				'invalid_states',
				__( 'Post states configuration cannot be empty.', 'arraypress' )
			);
		}

		if ( ! is_callable( $option_getter ) ) {
			return new WP_Error(
				'invalid_option_getter',
				__( 'The option getter must be a callable function.', 'arraypress' )
			);
		}

		$filtered_states = array_filter( $states, function ( $label, $key ) {
			return ! empty( $key ) && ! empty( $label );
		}, ARRAY_FILTER_USE_BOTH );

		if ( empty( $filtered_states ) ) {
			return new WP_Error(
				'invalid_states_config',
				__( 'Post states must contain valid keys and labels.', 'arraypress' )
			);
		}

		foreach ( $filtered_states as $key => $label ) {
			$this->states[ $key ] = [
				'label'         => $label,
				'option_getter' => $option_getter
			];
		}

		$this->log( sprintf( 'Registered %d post states', count( $filtered_states ) ) );

		return true;
	}

	/**
	 * Add a single post state
	 *
	 * @param string $key           Option key
	 * @param string $label         Display label
	 * @param string $option_getter Optional custom option getter function name
	 *
	 * @return WP_Error|true
	 */
	public function add_state( string $key, string $label, string $option_getter = 'get_option' ) {
		if ( empty( $key ) || empty( $label ) ) {
			return new WP_Error(
				'invalid_state',
				__( 'Post state key and label cannot be empty.', 'arraypress' )
			);
		}

		if ( ! is_callable( $option_getter ) ) {
			return new WP_Error(
				'invalid_option_getter',
				__( 'The option getter must be a callable function.', 'arraypress' )
			);
		}

		$this->states[ $key ] = [
			'label'         => $label,
			'option_getter' => $option_getter
		];

		$this->log( sprintf( 'Added post state: %s', $key ) );

		return true;
	}

	/**
	 * Display post states in the admin
	 *
	 * @param array   $post_states Existing post states
	 * @param WP_Post $post        Current post object
	 *
	 * @return array
	 */
	public function display_post_states( array $post_states, WP_Post $post ): array {
		foreach ( $this->states as $key => $config ) {
			$option_value = call_user_func( $config['option_getter'], $key );

			if ( intval( $option_value ) === $post->ID ) {
				$post_states[ $key ] = $config['label'];
				$this->log( sprintf( 'Displayed state %s for post %d', $key, $post->ID ) );
			}
		}

		return $post_states;
	}

	/**
	 * Log debug message
	 *
	 * @param string $message Message to log
	 * @param array  $context Optional context
	 */
	protected function log( string $message, array $context = [] ): void {
		if ( $this->debug ) {
			error_log( sprintf(
				'[Post States] %s %s',
				$message,
				! empty( $context ) ? json_encode( $context ) : ''
			) );
		}
	}
}