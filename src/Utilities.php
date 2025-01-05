<?php
/**
 * Post States Registration Helper Functions
 *
 * @package     ArrayPress\WP\Register
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

use ArrayPress\WP\Register\PostStates;

if ( ! function_exists( __NAMESPACE__ . '\\register_post_states' ) ):
	/**
	 * Helper function to register post states.
	 *
	 * Example usage:
	 * ```php
	 * $options_map = [
	 *     'landing_page'  => __('Landing Page', 'text-domain'),
	 *     'featured_post' => __('Featured Post', 'text-domain'),
	 * ];
	 *
	 * $result = register_post_states($options_map);
	 * if (is_wp_error($result)) {
	 *     error_log($result->get_error_message());
	 * }
	 * ```
	 *
	 * @param array       $options_map   Associative array mapping option keys to labels.
	 * @param string|null $option_getter Function to retrieve option values, defaults to 'get_option'.
	 *
	 * @return PostStates|WP_Error PostStates instance on success, WP_Error on failure.
	 * @since 1.0.0
	 *
	 */
	function register_post_states( array $options_map, ?string $option_getter = null ) {
		try {
			return PostStates::register( $options_map, $option_getter );
		} catch ( Exception $e ) {
			return new WP_Error(
				'post_states_registration_failed',
				sprintf(
				/* translators: %s: Error message */
					__( 'Failed to register post states: %s', 'arraypress' ),
					$e->getMessage()
				)
			);
		}
	}
endif;

if ( ! function_exists( __NAMESPACE__ . '\\post_states' ) ):
	/**
	 * Helper function to get the PostStates instance.
	 *
	 * @return PostStates
	 * @since 1.0.0
	 *
	 */
	function post_states(): PostStates {
		return PostStates::instance();
	}
endif;