<?php
/**
 * The PostStates class manages the display of custom post states within the WordPress admin area.
 *
 * @package     ArrayPress\WP\Register
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\WP\Register;

use WP_Error;
use WP_Post;

/**
 * Class PostStates
 *
 * Manages the addition of custom post states in the WordPress admin area
 * for specific pages based on dynamic options.
 *
 * @since 1.0.0
 */
class PostStates {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * An associative array mapping option keys to labels for the post states.
	 *
	 * @since 1.0.0
	 * @var array|WP_Error
	 */
	private $options_map;

	/**
	 * The callable function used to retrieve option values.
	 *
	 * @since 1.0.0
	 * @var callable|WP_Error
	 */
	private $option_getter;

	/**
	 * Get instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return self Instance of this class.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * PostStates constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $options_map   Associative array mapping option keys to labels.
	 * @param string|null $option_getter Function to retrieve the option values, defaults to 'get_option'.
	 */
	private function __construct( array $options_map = [], string $option_getter = null ) {
		$this->options_map   = [];
		$this->option_getter = 'get_option';
	}

	/**
	 * Initialize the post states system.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $options_map   Associative array mapping option keys to labels.
	 * @param string|null $option_getter Function to retrieve the option values.
	 *
	 * @return WP_Error|true True on success, WP_Error on failure.
	 */
	public function init( array $options_map, ?string $option_getter = null ) {
		$result = $this->set_options_map( $options_map );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $option_getter ) {
			$result = $this->set_option_getter( $option_getter );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		add_filter( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );

		return true;
	}

	/**
	 * Sets the options map.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options_map Associative array mapping option keys to labels.
	 *
	 * @return WP_Error|true True on success, WP_Error on failure.
	 */
	public function set_options_map( array $options_map ) {
		$filtered_map = array_filter( $options_map, [ $this, 'validate_options_map' ], ARRAY_FILTER_USE_BOTH );

		if ( empty( $filtered_map ) ) {
			return new WP_Error(
				'invalid_options_map',
				__( 'The options map cannot be empty and must contain valid keys and labels.', 'arraypress' )
			);
		}

		$this->options_map = $filtered_map;
		return true;
	}

	/**
	 * Sets the option getter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option_getter The function to retrieve the option values.
	 *
	 * @return WP_Error|true True on success, WP_Error on failure.
	 */
	public function set_option_getter( string $option_getter ) {
		if ( ! is_callable( $option_getter ) ) {
			return new WP_Error(
				'invalid_option_getter',
				__( 'The option getter must be a callable function.', 'arraypress' )
			);
		}

		$this->option_getter = $option_getter;
		return true;
	}

	/**
	 * Validates each entry in the options map to ensure keys and labels are not empty.
	 *
	 * @since 1.0.0
	 *
	 * @param string $label      The label for the post state.
	 * @param string $option_key The key for the option.
	 *
	 * @return bool Returns true if both key and label are valid, false otherwise.
	 */
	private function validate_options_map( string $label, string $option_key ): bool {
		return ! empty( $option_key ) && ! empty( $label );
	}

	/**
	 * Adds custom page state displays to the WordPress Pages list.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $post_states Existing post states.
	 * @param WP_Post $post        The current post object.
	 *
	 * @return array The modified post states.
	 */
	public function display_post_states( array $post_states, WP_Post $post ): array {
		if ( empty( $this->options_map ) || is_wp_error( $this->options_map ) ) {
			return $post_states;
		}

		foreach ( $this->options_map as $option_key => $label ) {
			$option_value = call_user_func( $this->option_getter, $option_key );

			if ( intval( $option_value ) === $post->ID ) {
				$post_states[ $option_key ] = $label;
			}
		}

		return $post_states;
	}

	/**
	 * Register post states.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $options_map   Associative array mapping option keys to labels.
	 * @param string|null $option_getter Function to retrieve the option values.
	 *
	 * @return WP_Error|self WP_Error on failure, instance on success.
	 */
	public static function register( array $options_map, ?string $option_getter = null ) {
		$instance = self::instance();
		$result   = $instance->init( $options_map, $option_getter );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $instance;
	}

}