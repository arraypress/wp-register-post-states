<?php
/**
 * Post States Registration Helper Functions
 *
 * @package     ArrayPress\WP\Register
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 */

declare( strict_types=1 );

use ArrayPress\WP\Register\PostStates;

if ( ! function_exists( 'register_post_states' ) ):
	/**
	 * Register multiple post states
	 *
	 * Example usage:
	 * ```php
	 * $states = [
	 *     'page_on_front'  => __( 'Front Page', 'my-plugin' ),
	 *     'page_for_posts' => __( 'Posts Page', 'my-plugin' )
	 * ];
	 *
	 * register_post_states( $states );
	 * ```
	 *
	 * @param array  $states        Array of post states (key => label)
	 * @param string $option_getter Optional custom option getter function name
	 *
	 * @return PostStates|WP_Error PostStates instance or WP_Error on failure
	 */
	function register_post_states( array $states, string $option_getter = 'get_option' ) {
		$manager = new PostStates();
		$result  = $manager->register( $states, $option_getter );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $manager;
	}
endif;

if ( ! function_exists( 'add_post_state' ) ):
	/**
	 * Add a single post state
	 *
	 * Example usage:
	 * ```php
	 * add_post_state(
	 *     'privacy_page',
	 *     __( 'Privacy Policy', 'my-plugin' )
	 * );
	 * ```
	 *
	 * @param string $key           Option key
	 * @param string $label         Display label
	 * @param string $option_getter Optional custom option getter function name
	 *
	 * @return PostStates|WP_Error PostStates instance or WP_Error on failure
	 */
	function add_post_state( string $key, string $label, string $option_getter = 'get_option' ) {
		$manager = new PostStates();
		$result  = $manager->add_state( $key, $label, $option_getter );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $manager;
	}
endif;