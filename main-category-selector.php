<?php
/**
 * Plugin Name: Main Category Selector Plugin
 * Description: Allows to define a main category for Posts and Custom Post Types.
 * Version: 0.0.1
 * Author: Diego Giraldo
 * Author URI: https://dgiraldo.co
 * Developer: Diego Giraldo
 * Developer URI: http://dgiraldo.co/
 * Text Domain: main-cat-selector
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Definitions.
define( 'MAINCATSEL_VERSION', '0.0.1' );
define( 'MAINCATSEL_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'MAINCATSEL_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

if ( ! class_exists('MAINCATSEL') ) {
class MAINCATSEL {

    /**
     * The single instance of the class.
     *
     * @var self
     * @since  1.0.0
     */
    private static $instance = null;

    /**
     * Allows for accessing single instance of class. Class should only be constructed once per call.
     *
     * @since  1.0.0
     * @static
     * @return self Main instance.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
    }
}
}

/**
 * Main instance.
 *
 * Returns the main instance of Custom CSV Importer for WP Job Manager to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return CJM_Importer
 */
function MAINCATSELINST() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
    return MAINCATSEL::instance();
}

$GLOBALS['maincatesel'] = MAINCATSELINST();