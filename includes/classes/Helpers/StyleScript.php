<?php
namespace BBClassDropDown\Helpers;

class StyleScript {

    public function __construct() {

        add_action( 'init', __CLASS__ . '::maybe_load_scripts' );
    }

    
    /**
     * maybe_load_scripts
     *
     * @return void
     */
    public static function maybe_load_scripts() {
    
        // no need to load this if BB isn't even available
        if ( !class_exists( 'FLBuilderModel' ) ) return;
    
            add_action( 'admin_enqueue_scripts',                    __CLASS__ . '::class_dropdown_enqueue_scripts' );
            add_action( 'wp_enqueue_scripts',                       __CLASS__ . '::bb_class_frontend_scripts' );
            add_action( 'wp_enqueue_scripts',                       __CLASS__ . '::bb_class_frontend_select2' );
            
            // Enqueue jQuery UI for admin area
            add_action('admin_enqueue_scripts',                     __CLASS__ . '::enqueue_jquery_ui_sortable');
    }

    /**
     * beaver_builder_class_dropdown_enqueue_scripts
     * 
     * Enqueue scripts for dashboard
     *
     * @return void
     */
    public static function class_dropdown_enqueue_scripts() {

        // no need to load this if BB isn't available
        if ( !class_exists( 'FLBuilderModel' ) ) return;

		// only load script when on fl-builder-settings pages
		if ( !isset( $_REQUEST['page'] ) || 'fl-builder-settings' !== $_REQUEST['page'] ) {
            return;
        }


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
    public static function bb_class_frontend_scripts() {

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
    public static function bb_class_frontend_select2() {

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

    public static function enqueue_jquery_ui_sortable() {
        // Enqueue jQuery UI core
        wp_enqueue_script('jquery-ui-core');
        
        // Enqueue jQuery UI sortable module
        wp_enqueue_script('jquery-ui-sortable');
    }

    
}