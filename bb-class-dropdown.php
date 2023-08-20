<?php
/**
 * Beaver Builder Class Dropdown
 *
 * @package     BBClassDropdown
 * @author      PYLE/DIGITAL
 * @license     GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name: Beaver Builder Class Dropdown
 * Plugin URI:  https://github.com/zackpyle/BBClassDropdown
 * Description: BB Class Dropdown adds user defined CSS classes to dropdown below the Beaver Builder class input in the Advanced tab
 * Version:     1.0.1
 * Author:      PYLE/DIGITAL
 * Text Domain: BBClassDropdown
 * License:     GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 */


define( 'BBCLASSDROPDOWN_VERSION', '1.0.1' );
define( 'BBCLASSDROPDOWN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BBCLASSDROPDOWN_FILE', __FILE__ );
define( 'BBCLASSDROPDOWN_URL', plugins_url( '/', __FILE__ ) );

// Include Updater files
require_once(BBCLASSDROPDOWN_DIR . 'includes/updater/Autoloader.php');
require_once(BBCLASSDROPDOWN_DIR . 'includes/updater/GithubUpdater.php');
require_once(BBCLASSDROPDOWN_DIR . 'includes/updater/Init.php');

// Include plugin files
require_once( BBCLASSDROPDOWN_DIR . 'includes/bb-class-dropdown-functions.php');
require_once( BBCLASSDROPDOWN_DIR . 'includes/bb-class-dropdown-admin.php');

register_activation_hook( BBCLASSDROPDOWN_FILE, 'beaver_builder_class_dropdown_plugin_activate' );

// Initialize updater
new BBClassDropdown\Includes\Updater\Init();

add_action( 'init', 'maybe_load_scripts' );
add_action( 'init', 'clear_bb_class_options' );

/**
 * maybe_load_scripts
 *
 * @return void
 */
function maybe_load_scripts() {

    // no need to load this if BB isn't even available
    if ( !class_exists( 'FLBuilderModel' ) ) return;

        add_action( 'admin_enqueue_scripts',                    'beaver_builder_class_dropdown_enqueue_scripts' );
        add_action( 'wp_enqueue_scripts',                       'bb_class_frontend_scripts' );
        add_action( 'wp_enqueue_scripts',                       'bb_class_frontend_select2' );
		
	// Enqueue jQuery UI for admin area
	add_action('admin_enqueue_scripts', 'enqueue_jquery_ui_sortable');

        // Add admin settings tab to Beaver Builder
        add_action('fl_builder_admin_settings_nav_items',       'bb_class_dropdown_menu_item');
        add_action('fl_builder_admin_settings_render_forms',    'bb_class_dropdown_add_settings_form');
        add_action('fl_builder_admin_settings_save',            'bb_class_dropdown_admin_settings_save');
}


/**
 * beaver_builder_class_dropdown_enqueue_scripts
 * 
 * Enqueue scripts for dashboard
 *
 * @return void
 */
function beaver_builder_class_dropdown_enqueue_scripts() {

    // no need to load this if BB isn't available
    if ( !class_exists( 'FLBuilderModel' ) ) return;

    wp_enqueue_script( 
        'bb-class-dropdown-scripts', 
        BBCLASSDROPDOWN_URL . 'includes/js/bb-class-dropdown-admin-scripts.js', 
        array( 'jquery' ), 
        BBCLASSDROPDOWN_VERSION, 
        true 
    );
}

/**
 * bb_class_frontend_scripts
 * 
 * Enqueue scripts when editing BB layout/page
 *
 * @return void
 */
function bb_class_frontend_scripts() {

    // no need to load this if BB isn't available or not in builder
    if ( !class_exists( 'FLBuilderModel' ) || !\FLBuilderModel::is_builder_active() ) return;

    // Custom JS
    wp_enqueue_script( 
        'bb-class-dropdown-frontend-script', 
        BBCLASSDROPDOWN_URL . 'includes/js/bb-class-dropdown-frontend-script.js', 
        array( 'jquery' ), BBCLASSDROPDOWN_VERSION, true 
    );
    // get options for localization
    $options = get_option( 'beaver_builder_class_dropdown_options' , [] );
    wp_localize_script( 
        'bb-class-dropdown-frontend-script', 
        'BBClassOptions' , 
        array( "options" => $options ) 
    );
}

/**
 * bb_class_frontend_select2
 *
 * @return void
 */
function bb_class_frontend_select2() {

    // no need to load this if BB isn't available or not in builder
    if ( !class_exists( 'FLBuilderModel' ) || !\FLBuilderModel::is_builder_active() ) return;
    
    // return early if select2 isn't loaded
    if ( !apply_filters( 'fl_select2_enabled' , true ) ) return;

    // Custom JS
    wp_enqueue_script( 
        'bb-class-dropdown-select2', 
        BBCLASSDROPDOWN_URL . 'includes/js/bb-class-dropdown-select2.js', 
        array( 'jquery', 'select2' ), 
        BBCLASSDROPDOWN_VERSION, 
        true 
    );
}

function enqueue_jquery_ui_sortable() {
    // Enqueue jQuery UI core
    wp_enqueue_script('jquery-ui-core');
    
    // Enqueue jQuery UI sortable module
    wp_enqueue_script('jquery-ui-sortable');
}

/**
 * beaver_builder_class_dropdown_activate
 *
 * @return void
 */
function beaver_builder_class_dropdown_plugin_activate() {
    // Check if Beaver Builder is active
    if ( ! class_exists( 'FLBuilder' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( esc_html__( 'Sorry, but this plugin requires Beaver Builder.', 'BBClassDropdown' ) );
    }

    // Flush rewrite rules
    //flush_rewrite_rules();
}


/**
 * clear_bb_class_options
 *
 * Clear all existing classes by navigating to /?clear_bb_class_options=1
 * 
 * @return void
 */
function clear_bb_class_options() {
    if (isset($_GET['clear_bb_class_options'])) {
        delete_option('beaver_builder_class_dropdown_options');
        add_action('admin_notices', 'bb_class_options_reset_notice');
    }
}
function bb_class_options_reset_notice() {
    echo '<div class="notice notice-success is-dismissible updated"><p>' . esc_html__('Classes reset!', 'BBClassDropdown') . '</p></div>';
}

/**
 * bb_class_dropdown_menu_item
 *
 * Add new tab in the bb settings menu
 * 
 * @param  mixed $navitems
 * @return void
 */
function bb_class_dropdown_menu_item($navitems)
{
    $navitems['class-dropdown'] = array(
        'title'=> 'Predefined Classes',
        'show'  => true,
        'priority'  => 695
    );
    return $navitems;
}

/**
 * bb_class_dropdown_add_settings_form
 * 
 * output for our BB settings tab
 *
 * @return void
 */
function bb_class_dropdown_add_settings_form(){
    // function found in includes/bb-class-dropdown-functions.php
    beaver_builder_class_dropdown_settings_page_html();
}
