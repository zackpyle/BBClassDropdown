<?php
namespace BBClassDropdown\Integration;

class BeaverBuilder {

    public function __construct() {

        add_filter( 'fl_builder_field_js_config',                           __CLASS__ . '::add_class_dropdown_options', 10, 2 );
        
        add_filter( 'plugin_action_links_' . BBCLASSDROPDOWN_BASE,          __CLASS__ . '::class_dropdown_settings_link' );

        // Add admin settings tab to Beaver Builder
        add_action('fl_builder_admin_settings_nav_items',                   __CLASS__ . '::class_dropdown_menu_item');

    }

     
    /**
     * beaver_builder_class_dropdown_class_class_dropdown
     *
     * Add custom classes to dropdown selector
     * 
     * @param  mixed $field
     * @param  mixed $field_key
     * @return void
     */
    public static function add_class_dropdown_options( $field, $field_key ) {

        // Get options
        $options = get_option( 'beaver_builder_class_dropdown_options' );

        // Process field
        if ( 'class' == $field_key && isset( $options['groups'] ) && is_array( $options['groups'] ) ) {
            $field['options'] = array(
                '' => esc_html__( '- Choose from utility classes -', 'BBClassDropdown' ),
            );

            foreach ( $options['groups'] as $group ) {
                if (isset($group['name'])) {
                    $optgroup = array(
                        'label' => esc_html( $group['name']),
                        'options' => array(),
                    );
                    
                    // Check if the single selection checkbox is checked for this group - currently doesn't work
                    // if ( isset($group['single_selection']) && $group['single_selection'] ) {
                    //     // Add the data attribute for single selection
                    //     $optgroup['data-single-selection'] = 'true';
                    // }

                    if ( isset( $group['classes'] ) && is_array( $group['classes'] ) ) {
                        foreach ( $group['classes'] as $class ) {
                            if (isset($class['id']) && isset($class['name'])) {
                                $optgroup['options'][ $class['id'] ] = esc_html( $class['name'] );
                            }
                        }
                    }

                    if ( ! empty( $optgroup['options'] ) ) {
                        $field['options'][ 'optgroup-' . sanitize_title_with_dashes( $group['name'] ) ] = $optgroup;
                    }
                }
            }
        }

        // Return field
        return $field;
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
