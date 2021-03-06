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

if ( ! class_exists('MAINCATSEL') ) :
class MAINCATSEL {

    /**
     * The single instance of the class.
     *
     * @var self
     * @since  0.0.1
     */
    protected static $instance = null;

    /**
     * Allows for accessing single instance of class. Class should only be constructed once per call.
     *
     * @since  0.0.1
     * @static
     * @return self Main instance.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Main Class empty Constructor.
     *
     * @since  0.0.1
     */
    public function __construct() {}

    /**
     * Plugin Hooks Setup.
     *
     * @since  0.0.1
     */
    public function plugin_setup() {
        // Hooks.
        add_action( 'save_post', array( $this, 'save_main_cat' ), 10, 1 );
        add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
        add_action( 'deleted_term_taxonomy', array( $this, 'main_cat_delete' ) );
    }

    /**
     * Save Main Category.
     *
     * Updates (creates) an postmeta entry to associate main category to post.
     *
     * @since  0.0.1
     */
    public function save_main_cat( $post_id ) {
        if ( empty( $_POST['_main_cat'] ) ) {
            return;
        }

        $main_cat_id = intval( sanitize_text_field( wp_unslash( $_POST['_main_cat'] ) ) );

        if ( empty( $main_cat_id ) || ! term_exists( $main_cat_id, 'category' ) ) {
            delete_post_meta( $post_id, '_main_cat' );
            return;           
        }

        update_post_meta( $post_id, '_main_cat', $main_cat_id );

        // Update post categories to include main cat if not selected before:
        wp_set_post_categories( $post_id, $main_cat_id, true );
    }

    /**
     * Metabox register.
     *
     * Register metabox to be rendered in posts and CPTs.
     *
     * @since  0.0.1
     */
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

    /**
     * Metabox content renderer.
     *
     * Echoes the categories dropdown inside the metabox.
     *
     * @since  0.0.1
     */
    public function main_cat_metabox() {
        $args = array(
            'show_option_none'  => __( 'Select main category', 'main-cat-selector' ),
            'option_none_value' => 0,
            'echo'              => 1,
            'name'              => '_main_cat',
            'hide_empty'        => 0,
            'selected'          => get_post_meta( get_the_ID(), '_main_cat', true ),
        );
        wp_dropdown_categories( $args );
    }

    /**
     * Handles cleanup of the main category after a category is deleted.
     *
     * @since 0.0.1
     */
    public function main_cat_delete( $tax_id ) {
        $args = array(
            'post_type'     => 'post',
            'post_status'   => 'publish',
            'meta_query'    => array(
                array(
                    'key'   => '_main_cat',
                    'value' => $tax_id
                )
            ),
            'fields'        => 'ids',
            'numberposts'   => -1
        );

        $posts = get_posts($args);
        foreach ( $posts as $post ) {
            delete_post_meta( $post, '_main_cat' );
        }
    }
}
endif;

// Plugin initialization.
add_action( 'plugins_loaded', array( MAINCATSEL::instance(), 'plugin_setup' ) );
