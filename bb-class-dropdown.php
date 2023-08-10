<?php
/*
Plugin Name: Beaver Builder Class Dropdown
Description: Adds user defined CSS classes to dropdown below the Beaver Builder class input in the Advanced tab
Version:     1.3.3
Author:      PYLE/DIGITAL
*/

// Include plugin files
require_once( plugin_dir_path( __FILE__ ) . 'includes/bb-class-dropdown-functions.php');
require_once( plugin_dir_path( __FILE__ ) . 'includes/bb-class-dropdown-admin.php');

// Enqueue scripts
function beaver_builder_class_dropdown_enqueue_scripts() {

    // no need to load this if BB isn't available
    if ( !class_exists( 'FLBuilderModel' ) ) return;

    wp_enqueue_script( 'bb-class-dropdown-scripts', plugin_dir_url( __FILE__ ) . 'includes/js/bb-class-dropdown-admin-scripts.js', array( 'jquery' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'beaver_builder_class_dropdown_enqueue_scripts' );

function bb_class_frontend_scripts() {

    // no need to load this if BB isn't available or not in builder
    if ( !class_exists( 'FLBuilderModel' ) || !\FLBuilderModel::is_builder_active() ) return;

    // Only load scripts if user is logged in
    if ( is_user_logged_in() ) {
		// Custom JS
		wp_enqueue_script( 'bb-class-dropdown-frontend-script', plugin_dir_url( __FILE__ ) . 'includes/js/bb-class-dropdown-frontend-script.js', array( 'jquery' ), '1.0', true );

        $options = get_option( 'beaver_builder_class_dropdown_options' , [] );

        wp_localize_script( 'bb-class-dropdown-frontend-script' , 'BBClassOptions' , array( "options" => $options ) );
    }
}
add_action( 'wp_enqueue_scripts', 'bb_class_frontend_scripts' );



function bb_class_frontend_select2() {

    // no need to load this if BB isn't available or not in builder
    if ( !class_exists( 'FLBuilderModel' ) || !\FLBuilderModel::is_builder_active() ) return;

	// Get options
	$options = get_option( 'beaver_builder_class_dropdown_options', array() );
	$select2_enabled = isset($options['select2_enabled']) ? $options['select2_enabled'] : 0;
	
    // Only load scripts if user is logged in
    if ( is_user_logged_in() ) {
		
		if ($select2_enabled) {
            // Select2
            if ( ! wp_script_is( 'select2', 'enqueued' ) ) {
                // Register and enqueue the script.
                wp_register_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.0.13', true );
                wp_enqueue_script( 'select2' );
            }
        
            if ( ! wp_style_is( 'select2', 'enqueued' ) ) {
                // Register and enqueue the style.
                wp_register_style( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.0.13' );
                wp_enqueue_style( 'select2' );
            }

            // Custom JS
            wp_enqueue_script( 'bb-class-dropdown-select2', plugin_dir_url( __FILE__ ) . 'includes/js/bb-class-dropdown-select2.js', array( 'jquery', 'select2' ), '1.0', true );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'bb_class_frontend_select2' );


function beaver_builder_class_dropdown_activate() {
    // Check if Beaver Builder is active
    if ( ! class_exists( 'FLBuilder' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( esc_html__( 'Sorry, but this plugin requires Beaver Builder.', 'textdomain' ) );
    }

    // Default options
    $default_options = array(
        'groups' => array(
            array(
                'name' => 'My Group',
                'classes' => array(
                    array(
                        'id' => 'my-class',
                        'name' => 'My Class',
                    ),
                ),
            ),
        ),
    );

    // Add default options
    add_option( 'beaver_builder_class_dropdown_options', $default_options );

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'beaver_builder_class_dropdown_activate' );

// Clear all existing classes by navigating to /?clear_bb_options=1
add_action('init', 'clear_bb_options');
function clear_bb_options() {
    if (isset($_GET['clear_bb_options'])) {
        delete_option('beaver_builder_class_dropdown_options');
        echo 'Options cleared!';
        die();
    }
}

// Add admin settings tab to Beaver Builder
add_action('fl_builder_admin_settings_nav_items','bb_class_dropdown_menu_item');
add_action('fl_builder_admin_settings_render_forms','bb_class_dropdown_add_settings_form');
add_action('fl_builder_admin_settings_save', 'bb_class_dropdown_admin_settings_save');

// Add new tab in the bb settings menu
function bb_class_dropdown_menu_item($navitems)
{
    $navitems['class-dropdown'] = array(
        'title'=> 'Predefined Classes',
        'show'  => true,
        'priority'  => 695
    );
    return $navitems;
}

function bb_class_dropdown_add_settings_form(){
    beaver_builder_class_dropdown_settings_page_html();
}
