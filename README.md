# WordPress Post States Registration Library

A comprehensive PHP library for registering custom post states in the WordPress admin area. This library provides a robust solution for programmatically adding labels next to post titles in the admin list view.

## Features

- 🚀 Easy registration of custom post states
- 🔄 Dynamic option value retrieval
- ⚡ Performance optimized with singleton pattern
- 🛡️ Comprehensive error handling with WP_Error
- 🔒 Type safety with strict typing
- ✅ WordPress coding standards compliant

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher

## Installation

You can install the package via composer:

```bash
composer require arraypress/wp-register-post-states
```

## Basic Usage

Here's a simple example of how to register custom post states:

```php
use function ArrayPress\WP\Register\register_post_states;

// Define your post states
$states = [
    'landing_page'  => __('Landing Page', 'your-textdomain'),
    'featured_post' => __('Featured Post', 'your-textdomain')
];

// Register the post states
$result = register_post_states($states);

// Handle any errors
if (is_wp_error($result)) {
    error_log($result->get_error_message());
    return;
}
```

## Custom Option Getter

You can specify a custom function to retrieve option values:

```php
// Using a custom option getter
$result = register_post_states($states, 'get_theme_mod');

// Or using any callable
$result = register_post_states($states, [$your_class, 'get_option']);
```

## Advanced Usage

For more control, you can use the class directly:

```php
use ArrayPress\WP\Register\PostStates;

// Get the manager instance
$manager = PostStates::instance();

// Initialize with configuration
$result = $manager->init([
    'homepage' => __('Homepage', 'your-textdomain'),
    'contact'  => __('Contact Page', 'your-textdomain')
]);

if ( is_wp_error( $result ) ) {
    // Handle error
    error_log($result->get_error_message());
    return;
}
```

## Configuration Options

Each post state should be configured with:

| Key | Type | Description |
|-----|------|-------------|
| option_key | string | The option key that stores the post ID |
| label | string | The text to display next to the post title |

## Error Handling

The library uses WordPress's WP_Error for error handling:

```php
$result = register_post_states([/* invalid config */]);

if ( is_wp_error( $result ) ) {
    // Get the error message
    $message = $result->get_error_message();
    
    // Get the error code
    $code = $result->get_error_code();
    
    // Handle the error appropriately
    error_log( "Post states registration failed: $message ($code)" );
}
```

## Filter Usage

The library hooks into WordPress's `display_post_states` filter:

```php
// Example of how the library modifies post states
add_filter( 'display_post_states', function( $post_states, $post ) {
    // Your custom post states are automatically added here
    return $post_states;
}, 10, 2);
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is licensed under the GPL2+ License. See the LICENSE file for details.

## Support

For support, please use the [issue tracker](https://github.com/arraypress/wp-register-post-states/issues).