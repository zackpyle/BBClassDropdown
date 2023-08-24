<?php
namespace BBClassDropdown\Helpers;

class Activation {

    public function __construct() {

        register_activation_hook( BBCLASSDROPDOWN_FILE,     __CLASS__ . '::beaver_builder_class_dropdown_plugin_activate' );
    }

    /**
     * beaver_builder_class_dropdown_activate
     *
     * @return void
     */
    public static function beaver_builder_class_dropdown_plugin_activate() {
        // Check if Beaver Builder is active
        if ( ! class_exists( 'FLBuilder' ) ) {
            deactivate_plugins( BBCLASSDROPDOWN_BASE );
            wp_die( esc_html__( 'Sorry, but this plugin requires Beaver Builder.', 'BBClassDropdown' ) );
        }
    
        // Flush rewrite rules
        //flush_rewrite_rules();
    }
    
}