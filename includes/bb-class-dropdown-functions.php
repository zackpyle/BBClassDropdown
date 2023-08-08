<?php
// Add custom classes to dropdown selector
function beaver_builder_class_dropdown_class_class_dropdown( $field, $field_key ) {

    // Get options
    $options = get_option( 'beaver_builder_class_dropdown_options' );

    // Process field
    if ( 'class' == $field_key && isset( $options['groups'] ) && is_array( $options['groups'] ) ) {
        $field['options'] = array(
            '' => esc_html__( '- Choose from predefined classes -', 'textdomain' ),
        );

        foreach ( $options['groups'] as $group ) {
            if (isset($group['name'])) {
                $optgroup = array(
                    'label' => esc_html( $group['name'] ),
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
add_filter( 'fl_builder_field_js_config', 'beaver_builder_class_dropdown_class_class_dropdown', 10, 2 );



// Add plugin settings link to plugin listing
function beaver_builder_class_dropdown_settings_link( $links ) {
    $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=beaver-builder-class-dropdown' ) ) . '">' . esc_html__( 'Settings', 'textdomain' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'beaver_builder_class_dropdown_settings_link' );