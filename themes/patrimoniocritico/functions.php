<?php
/**
 * Theme constants and setup functions
 *
 * @package PatrimonioCritico
 */

// Useful global constants.
define( 'PATRIMONIOCRITICO_THEME_VERSION', '1.0.0' );
define( 'PATRIMONIOCRITICO_THEME_TEMPLATE_URL', get_template_directory_uri() );
define( 'PATRIMONIOCRITICO_THEME_PATH', get_template_directory() . '/' );
define( 'PATRIMONIOCRITICO_THEME_DIST_PATH', PATRIMONIOCRITICO_THEME_PATH . 'dist/' );
define( 'PATRIMONIOCRITICO_THEME_DIST_URL', PATRIMONIOCRITICO_THEME_TEMPLATE_URL . '/dist/' );
define( 'PATRIMONIOCRITICO_THEME_INC', PATRIMONIOCRITICO_THEME_PATH . 'includes/' );
define( 'PATRIMONIOCRITICO_THEME_CLASSES', PATRIMONIOCRITICO_THEME_PATH . 'includes/classes/' );
define( 'PATRIMONIOCRITICO_THEME_BLOCK_DIST_DIR', PATRIMONIOCRITICO_THEME_DIST_PATH . '/blocks/' );

// Require Composer autoloader if it exists.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

$is_local_env = in_array( wp_get_environment_type(), [ 'local', 'development' ], true );
$is_local_url = strpos( home_url(), '.test' ) || strpos( home_url(), '.local' );
$is_local     = $is_local_env || $is_local_url;

if ( $is_local && file_exists( __DIR__ . '/dist/fast-refresh.php' ) ) {
	require_once __DIR__ . '/dist/fast-refresh.php';
	TenUpToolkit\set_dist_url_path( basename( __DIR__ ), PATRIMONIOCRITICO_THEME_DIST_URL, PATRIMONIOCRITICO_THEME_DIST_PATH );
}

require_once PATRIMONIOCRITICO_THEME_INC . 'core.php';
require_once PATRIMONIOCRITICO_THEME_INC . 'blocks.php';
require_once PATRIMONIOCRITICO_THEME_INC . 'overrides.php';
require_once PATRIMONIOCRITICO_THEME_INC . 'overrides.php';
require_once PATRIMONIOCRITICO_THEME_INC . 'utility.php';

// Run the setup functions.
PatrimonioCritico\Core\setup();
PatrimonioCritico\Blocks\setup();
PatrimonioCritico\Overrides\setup();
