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
        add_action( 'save_post', array( $this, 'save_main_cat' ), 10, 1 );
        add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
    }

    public function save_main_cat( $post_id ) {
        $main_cat_id = intval( sanitize_text_field( wp_unslash( $_POST['_main_cat'] ) ) );

        if ( ! empty( $main_cat_id ) ) {
            update_post_meta( $post_id, '_main_cat', $main_cat_id );

            // Update post categories to include main cat if not selected before:
            wp_set_post_categories( $post_id, $main_cat_id, true );
        }
    }

    public function add_metabox() {
        add_meta_box(
            'maincatsel_metabox',
            __( 'Main Category', 'main-cat-selector' ),
            array( $this, 'main_cat_metabox' ),
            null,
            'side',
            'core'
        );
    }

    public function main_cat_metabox() {
        $args = array(
            'echo'       => 1,
            'name'       => '_main_cat',
            'hide_empty' => 0,
            'selected'   => get_post_meta( get_the_ID(), '_main_cat', true ),
        );
        wp_dropdown_categories( $args );
    }
}
}

/**
 * Main instance.
 *
 * Returns the main instance of the class.
 *
 * @since  1.0.0
 * @return CJM_Importer
 */
function MAINCATSELINST() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
    return MAINCATSEL::instance();
}

$GLOBALS['maincatesel'] = MAINCATSELINST();