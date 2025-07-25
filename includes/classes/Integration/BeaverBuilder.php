<?php
namespace BBClassDropdown\Integration;

class BeaverBuilder {

    public function __construct() {
     
        add_filter( 'plugin_action_links_' . BBCLASSDROPDOWN_BASE,          __CLASS__ . '::class_dropdown_settings_link' );

        // Add admin settings tab to Beaver Builder
        add_action('fl_builder_admin_settings_nav_items',                   __CLASS__ . '::class_dropdown_menu_item');

    }
    
    /**
     * beaver_builder_class_dropdown_settings_link
     *
     * Add plugin settings link to plugin listing
     * 
     * @param  mixed $links
     * @return void
     */
    public static function class_dropdown_settings_link( $links ) {
        $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=fl-builder-settings#class-dropdown' ) ) . '">' . esc_html__( 'Settings', 'BBClassDropdown' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }

    /**
     * bb_class_dropdown_menu_item
     *
     * Add new tab in the bb settings menu
     * 
     * @param  mixed $navitems
     * @return void
     */
    public static function class_dropdown_menu_item($navitems)
    {
        $navitems['class-dropdown'] = array(
            'title'=> 'Utility Classes',
            'show'  => true,
            'priority'  => 695
        );
        return $navitems;
    }

}
