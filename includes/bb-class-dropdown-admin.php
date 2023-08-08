<?php

/**
 * Provide a admin area for the plugin
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Settings page HTML
 */
function beaver_builder_class_dropdown_settings_page_html() {
    // Get options
    $options = get_option( 'beaver_builder_class_dropdown_options', array() );

    // Render settings page
    ?>
    <div id="fl-class-dropdown-form" class="fl-settings-form">
        <h1>Predefined Classes</h1>
        <form action="" method="post">
			<input type="hidden" name="bb-class-dd-nonce" value="<?php echo wp_create_nonce('bb-class-dd-nonce'); ?>">
            <?php settings_fields( 'beaver_builder_class_dropdown_options_group' ); ?>
            <?php do_settings_sections( 'beaver_builder_class_dropdown_options_group' ); ?>
            <?php settings_fields( 'beaver_builder_class_dropdown_options_group' ); ?>
            <?php do_settings_sections( 'beaver_builder_class_dropdown_options_group' ); ?>
			<table class="beaver-builder-class-dropdown-groups">
				<thead>
					<tr>
						<th scope="row"><?php esc_html_e( 'Group', 'textdomain' ); ?></th>
						<th><?php esc_html_e( 'Class / Label', 'textdomain' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php 
	if (isset($options['groups']) && is_array($options['groups'])) {
		foreach ( $options['groups'] as $i => $group ) :
		if(is_array($group)) { // Add this line to check if $group is an array
					?>
					<tr class="group">
						<td valign="top">
							<input type="text" name="beaver_builder_class_dropdown_options[groups][<?php echo $i; ?>][name]" value="<?php echo array_key_exists('name', $group) ? esc_attr( $group['name'] ) : ""; ?>" />
							<!-- Option to have group be only a single class at a time - useful for something like a background color -->
							<div class="single-select-wrapper">
								<input type="checkbox" name="beaver_builder_class_dropdown_options[groups][<?php echo $i; ?>][checkbox]" value="1" <?php checked( isset($group['checkbox']) ? $group['checkbox'] : 0 ); ?> />
								<label for="beaver_builder_class_dropdown_options[groups][<?php echo $i; ?>][checkbox]">Single Select Classes</label>
							<div>
						</td>
						<td>
							<table class="beaver-builder-class-dropdown-classes">
								<tbody>
									<?php 
			if (isset($group['classes']) && is_array($group['classes'])) {
				foreach ( $group['classes'] as $j => $class ) :
				if(is_array($class)) { // Add this line to check if $class is an array
									?>
									<tr>
										<td><input type="text" name="beaver_builder_class_dropdown_options[groups][<?php echo $i; ?>][classes][<?php echo $j; ?>][id]" placeholder="foo-bar" value="<?php echo array_key_exists('id', $class) ? esc_attr( $class['id'] ) : ""; ?>" /></td>
										<td><input type="text" name="beaver_builder_class_dropdown_options[groups][<?php echo $i; ?>][classes][<?php echo $j; ?>][name]" placeholder="Foo Bar" value="<?php echo array_key_exists('name', $class) ? esc_attr( $class['name'] ) : ""; ?>" /></td>
										<td>
											<button type="button" class="button beaver-builder-class-dropdown-remove-class">
											<svg aria-hidden="true" width="15" height="15">
												<use xlink:href="#trash" />
											</svg>
											</button>
										</td>
									</tr>
									
									<?php
				} // Closing bracket for if(is_array($class))
				endforeach;
			}
									?>
								</tbody>
							</table>
							<button type="button" class="button beaver-builder-class-dropdown-add-class">+</button>
						</td>
					</tr>
					<?php
		} // Closing bracket for if(is_array($group))
		endforeach;
	}
					?>
				</tbody>
			</table>
			<button type="button" class="button beaver-builder-class-dropdown-add-group">Add Group</button>
			<section id="select2-settings">
				<h2>Select2 Settings</h2>
				<p>
					<input id="select2_enabled" name="beaver_builder_class_dropdown_options[select2_enabled]" type="checkbox" <?php checked( isset($options['select2_enabled']) ? $options['select2_enabled'] : 0 ); ?> />
					<label for="select2_enabled">Enable Select2</label>
				</p>
			</section>
            <?php submit_button(); ?>
        </form>
    </div>

	<svg xmlns="http://www.w3.org/2000/svg"  hidden id="icon-svg-container">
		<symbol id="trash" viewBox="0 0 448 512" >
		<path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/>
	</symbol>
	</svg>
	<style>
		.beaver-builder-class-dropdown-groups{
			margin-top: 20px;
		}
		.beaver-builder-class-dropdown-add-group{
			margin-top: 30px !important;
		}
		.beaver-builder-class-dropdown-groups tr.group:not(:first-child) > td {
			padding-top: 20px;
		}
		.beaver-builder-class-dropdown-groups th{
			text-align: left;
		}
		.beaver-builder-class-dropdown-classes{
			margin-top: -2px;
		}
		.beaver-builder-class-dropdown-groups button.beaver-builder-class-dropdown-add-class{
			margin-left: 4px;
		}
		button.button.beaver-builder-class-dropdown-remove-class {
			display: grid;
			place-content: center;
		}
		button.button.beaver-builder-class-dropdown-remove-class svg {
			fill: currentColor;
		}
		.beaver-builder-class-dropdown-groups .group:first-child .beaver-builder-class-dropdown-remove-group,
		.beaver-builder-class-dropdown-classes tr:first-child .beaver-builder-class-dropdown-remove-class {
			display: none;
		}
		.fl-settings-form .beaver-builder-class-dropdown-groups th{
			padding: 0;
			font-weight: bold
		}
		.fl-settings-form .beaver-builder-class-dropdown-groups td {
			padding: 0;
		}
		#select2-settings{
			margin-top: 60px;
		}
		.single-select-wrapper{
			margin-top:10px;
		}
		#icon-svg-container{
			border: 0 !important;
			clip: rect(1px, 1px, 1px, 1px) !important; /* 1 */
			-webkit-clip-path: inset(50%) !important;
				clip-path: inset(50%) !important;  /* 2 */
			height: 1px !important;
			margin: -1px !important;
			overflow: hidden !important;
			padding: 0 !important;
			position: absolute !important;
			width: 1px !important;
			white-space: nowrap !important;
		}
	</style>
    <?php
}

function bb_class_dropdown_admin_settings_save() {
    if ( isset( $_POST['bb-class-dd-nonce'] ) && wp_verify_nonce( $_POST['bb-class-dd-nonce'], 'bb-class-dd-nonce' ) ) {
        $settings = isset($_POST['beaver_builder_class_dropdown_options']) ? $_POST['beaver_builder_class_dropdown_options'] : array();

        // Sanitize Select2 setting
        $settings['select2_enabled'] = isset($settings['select2_enabled']) ? 1 : 0;

        // Sanitize each group and class setting
        if (isset($settings['groups']) && is_array($settings['groups'])) {
            foreach ($settings['groups'] as $i => $group) {
                $settings['groups'][$i]['name'] = isset($group['name']) ? sanitize_text_field($group['name']) : "";

                if (isset($group['classes']) && is_array($group['classes'])) {
                    foreach ($group['classes'] as $j => $class) {
                        $settings['groups'][$i]['classes'][$j]['id'] = isset($class['id']) ? sanitize_text_field($class['id']) : "";
                        $settings['groups'][$i]['classes'][$j]['name'] = isset($class['name']) ? sanitize_text_field($class['name']) : "";
                    }
                }
            }
        }

        // Save to db
        update_option( 'beaver_builder_class_dropdown_options', $settings );

        FLBuilderModel::delete_asset_cache_for_all_posts();
    }
}


add_action( 'init', 'bb_class_dropdown_admin_settings_save' );
