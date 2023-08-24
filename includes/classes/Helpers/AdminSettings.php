<?php
namespace BBClassDropdown\Helpers;

class AdminSettings {

    public function __construct() {

        add_action( 'init',                                     __CLASS__ . '::bb_class_dropdown_admin_settings_save' );

        add_action( 'init',                                     __CLASS__ . '::clear_bb_class_options' );

        add_action('fl_builder_admin_settings_render_forms',    __CLASS__ . '::class_dropdown_settings_page_html');

        add_action('fl_builder_admin_settings_save',            __CLASS__ . '::bb_class_dropdown_admin_settings_save');


    }

    /**
     * clear_bb_class_options
     *
     * Clear all existing classes by navigating to /?clear_bb_class_options=1
     * 
     * @return void
     */
    public static function clear_bb_class_options() {
        // return early when not in admin area
        if (!is_admin(  )) return;

        if (isset($_GET['clear_bb_class_options'])) {

            delete_option('beaver_builder_class_dropdown_options');
            
            add_action('admin_notices',         __CLASS__ . '::bb_class_options_reset_notice');
        }
    }
        
    /**
     * bb_class_options_reset_notice
     *
     * @return void
     */
    public static function bb_class_options_reset_notice() {
        echo '<div class="notice notice-success is-dismissible updated"><p>' . esc_html__('Classes reset!', 'BBClassDropdown') . '</p></div>';
    }

    
    /**
     * beaver_builder_class_dropdown_settings_page_html
     *
     * Settings page HTML
     * 
     * @return void
     */
    public static function class_dropdown_settings_page_html() {
        // Get options
        $options = get_option( 'beaver_builder_class_dropdown_options', array() );
        // Render settings page
        ?>
        
        <div id="fl-class-dropdown-form" class="fl-settings-form">
            <h1>Predefined Classes</h1>
            <form action="" method="post" id="class-dropdown-form">
                <input type="hidden" name="bb-class-dd-nonce" value="<?php echo wp_create_nonce('bb-class-dd-nonce'); ?>">
                <input type="hidden" name="bb-class-action" value="update">
                <?php settings_fields( 'beaver_builder_class_dropdown_options_group' ); ?>
                <?php do_settings_sections( 'beaver_builder_class_dropdown_options_group' ); ?>
                <table class="beaver-builder-class-dropdown-groups">
                    <thead>
                        <tr>
                            <th id="handle-th"></th> <!-- Empty th for the handle -->
                            <th id="group-name-th" scope="row"><?php esc_html_e( 'Group', 'BBClassDropdown' ); ?></th>
                            <th id="class-th"><?php esc_html_e( 'Class / Label', 'BBClassDropdown' ); ?></th>
                            <th id="button-th"></th> <!-- Empty th for the buttons -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
        if (isset($options['groups']) && is_array($options['groups'])) {
            foreach ( $options['groups'] as $i => $group ) :
            if(is_array($group)) { // Add this line to check if $group is an array
                        ?>
                        <tr class="group">
                            <td class="group-handle"  valign="top">
                                <!-- Drag-and-drop handle -->
                                <div class="drag-handle"></div> 
                                <!-- Hidden input for ordering -->
                                        <input class="group-order" type="hidden" data-name="order" value="<?php echo $i; ?>" />
                            </td>
                            <td valign="top" class="group-name-col">
                                <input class="group-name" type="text" data-name="name" value="<?php echo array_key_exists('name', $group) ? esc_attr( $group['name'] ) : ""; ?>" />
                                <div class="group-options">
                                    <button type="button" class="button beaver-builder-class-dropdown-remove-group">
                                        <span class="sr-only">Delete Group</span><svg aria-hidden="true" width="13" height="13"><use xlink:href="#trash" /></svg>
                                    </button>
                                    <!-- Option to have group be only a single class at a time - useful for something like a background color -->
                                    <div class="single-select-wrapper">
                                        <label><input type="checkbox" data-name="singleton" value="1" <?php checked( isset($group['singleton']) ? $group['singleton'] : 0 ); ?> /> Single Class Group</label>
                                    </div>
                                </div>
                            </td>
                            <td valign="top" class="class-col">
                                <table class="beaver-builder-class-dropdown-classes">
                                    <tbody>
                                        <?php 
                            if (isset($group['classes']) && is_array($group['classes'])) {
                                foreach ( $group['classes'] as $j => $class ) :
                                if(is_array($class)) { // Add this line to check if $class is an array
                                        ?>
                                        <tr class="class-row" valign="top">
                                            <td class="class-handle" valign="top">
                                                <!-- Drag-and-drop handle -->
                                                <div class="drag-handle"></div>
                                                <!-- Hidden input for ordering -->
                                                <input class="class-order" type="hidden" data-name="order" value="<?php echo $j; ?>" />
                                            </td>
                                            <td class="class-value-col"><input type="text" data-name="id" placeholder="foo-bar" value="<?php echo array_key_exists('id', $class) ? esc_attr( $class['id'] ) : ""; ?>" /></td>
                                            <td class="class-label-col"><input type="text" data-name="name" placeholder="Foo Bar" value="<?php echo array_key_exists('name', $class) ? esc_attr( $class['name'] ) : ""; ?>" /></td>
                                            <td class="class-btn-col">
                                                <button type="button" class="button beaver-builder-class-dropdown-remove-class">
                                                <svg aria-hidden="true" width="13" height="13">
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
                                <button type="button" class="button beaver-builder-class-dropdown-add-class">
                                    <svg aria-hidden="true" width="12" height="12">
                                        <use xlink:href="#plus" />
                                    </svg>
                                </button>
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
                <div id="main-submit-wrapper">
                    <?php submit_button(); ?>
                </div>
            </form>
            <section id="bb-class-dd-settings">
                <div id="bb-class-import-export-settings">
                    <h2>Import/Export Settings</h2>
                    <button id="export-classes" class="button button-primary">Export Classes</button>
                    <button id="import-classes" class="button button-primary">Import Classes</button>
                    <dialog id="import-modal" onclick="event.target==this && this.close()">
                        <button id="close-import-modal" aria-label="Close Import Dialog">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="modal-content">
                            <h3>Select JSON import file:</h3>
                            <form action="" method="post" enctype="multipart/form-data" id="class-import-form">
                                <input type="hidden" name="bb-class-dd-nonce" value="<?php echo wp_create_nonce('bb-class-dd-nonce'); ?>">
                                <input type="hidden" name="bb-class-action" value="import">
                                <?php settings_fields( 'beaver_builder_class_dropdown_options_group' ); ?>
                                <?php do_settings_sections( 'beaver_builder_class_dropdown_options_group' ); ?>
                                <input type="file" name="fileToUpload" id="fileToUpload" accept="application/JSON">
                                <?php submit_button( 'Import Settings' ); ?>
                            </form>
                        </div>
                    </dialog>
                </div>
                <div id="bb-dropdown-reset-wrapper">
                    <h2>Reset Settings</h2>
                    <button id="reset-bb-dropdown-settings" class="button-link-delete button-secondary">Delete All Classes</button>
                </div>
                
            </section>
        </div>

        <svg xmlns="http://www.w3.org/2000/svg"  hidden id="icon-svg-container">
            <symbol id="trash" viewBox="0 0 448 512" >
                <path d="M170.5 51.6L151.5 80h145l-19-28.4c-1.5-2.2-4-3.6-6.7-3.6H177.1c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80H368h48 8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8V432c0 44.2-35.8 80-80 80H112c-44.2 0-80-35.8-80-80V128H24c-13.3 0-24-10.7-24-24S10.7 80 24 80h8H80 93.8l36.7-55.1C140.9 9.4 158.4 0 177.1 0h93.7c18.7 0 36.2 9.4 46.6 24.9zM80 128V432c0 17.7 14.3 32 32 32H336c17.7 0 32-14.3 32-32V128H80zm80 64V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0V400c0 8.8-7.2 16-16 16s-16-7.2-16-16V192c0-8.8 7.2-16 16-16s16 7.2 16 16z"/>
            </symbol>
            <symbol id="plus" viewBox="0 0 448 512" >
                <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/>
            </symbol>
        </svg>
        <style>
            .fl-settings-form{
                max-width: 700px;
            }
            table{
                width:100%;
            }
            input.group-name,
            .class-row input{
                width: 100%;
            }
            td.class-btn-col {
                min-width: 25px;
                padding-left: 5px !important;
            }
            #fl-class-dropdown-form input::placeholder {
                color: #b7b7b7;
            }
            .beaver-builder-class-dropdown-groups{
                margin-top: 20px;
                border-collapse: collapse;
            }
            .beaver-builder-class-dropdown-groups tr.group:not(:first-child) > td {
                padding-top: 20px;
            }
            .beaver-builder-class-dropdown-groups th{
                text-align: left;
                padding-bottom:5px !important;
            }
            .beaver-builder-class-dropdown-classes{
                margin-top: -2px;
                margin-left:10px;
            }
            tr.group {
                border-bottom: 1px solid #f2f2f2;
            }
            tr.group:last-child{
                border-bottom:none;
            }
            button.beaver-builder-class-dropdown-add-group{
                margin-left: 32px !important;
            }
            button.button.beaver-builder-class-dropdown-remove-class,
            button.button.beaver-builder-class-dropdown-remove-group{
                border-radius: 50%;
                height: 25px;
                width: 25px;
                display: grid;
                place-content: center;
                min-height: unset;
                margin-top: 5px;
                padding: 0;
            }
            button.button.beaver-builder-class-dropdown-remove-class svg,
            button.button.beaver-builder-class-dropdown-add-class svg,
            button.beaver-builder-class-dropdown-remove-group svg{
                fill: currentColor;
            }
            .beaver-builder-class-dropdown-groups button.beaver-builder-class-dropdown-add-class {
                margin: 5px 0 20px 50px;
                border-radius: 50%;
                height: 20px;
                width: 20px;
                display: grid;
                place-content: center;
                min-height: unset;
                padding: 0;
            }
            .beaver-builder-class-dropdown-groups .group:only-child .beaver-builder-class-dropdown-remove-group,
            .beaver-builder-class-dropdown-classes tr:only-child .beaver-builder-class-dropdown-remove-class {
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
            /* Drag Handle */
            .drag-handle {
                position: relative;
                width: 15px;
                cursor: grab;
                padding: 10px 3px 10px 10px;
            }
            .drag-handle::before,
            .drag-handle::after {
                content: '';
                display: block;
                width: 100%;
                height: 2px;
                background-color: #cecece;
                margin-bottom: 3px;
            }
            .drag-handle::after {
                margin-bottom: 0px;
            }
            th#class-th {
                padding-left: 45px;
            }
            .group-options {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-top: 5px;
            }
            .beaver-builder-class-dropdown-groups .group:first-child .group-options{
                margin-top:0;
            }
            .sr-only {
                border: 0 !important;
                clip: rect(1px, 1px, 1px, 1px) !important;
                -webkit-clip-path: inset(50%) !important;
                    clip-path: inset(50%) !important;
                height: 1px !important;
                margin: -1px !important;
                overflow: hidden !important;
                padding: 0 !important;
                position: absolute !important;
                width: 1px !important;
                white-space: nowrap !important;
            }
            #main-submit-wrapper .submit{
                text-align:right;
            }
            .wp-core-ui .button-link-delete.button-secondary {
                border-color: currentColor;
            }
            #bb-dropdown-reset-wrapper{
                margin-top:30px;
            }
            #bb-class-dd-settings h2{
                font-weight:500;
            }
            
            /* Import Export */		
            .modal-content{
                padding:35px;
            }
            #bb-class-dd-settings{
                margin-top:80px;
            }
            #import-modal{
                border: none;
                border-radius:5px;
                box-shadow: 0 5px 10px rgb(0 0 0 / .25);
                overflow:visible;
                padding:0;
            }
            #import-modal::backdrop {
            background: rgb(0 0 0 / 0.4);
            }
            button#close-import-modal {
                position: absolute;
                top: -15px;
                right: -15px;
                height: 30px;
                width: 30px;
                border-radius: 50%;
                border: 1px solid gray;
                display: grid;
                place-content: center;
                line-height: 1;
                cursor: pointer;
                font-size: 17px;
            }
            #import-modal p.submit{
                margin-top:0;
                padding-bottom:0;
            }
        </style>
        <?php
    }

    /**
     * bb_class_dropdown_admin_settings_save
     * 
     * 
     *
     * @return void
     */
    public static function bb_class_dropdown_admin_settings_save() {
        // return early when not in admin area
        if (!is_admin(  )) return;

        // return early when not set or nonce not matching

        if ( !isset($_POST['bb-class-dd-nonce']) || !wp_verify_nonce($_POST['bb-class-dd-nonce'], 'bb-class-dd-nonce') ) return;
        
        $action = filter_input( INPUT_POST , 'bb-class-action' );

        if ( 'update' == $action ) {

            $settings = isset($_POST['beaver_builder_class_dropdown_options']) ? $_POST['beaver_builder_class_dropdown_options'] : array();

            $group_order = 0;
            // Sanitize each group and class setting
            if (isset($settings['groups']) && is_array($settings['groups'])) {
        
                foreach ($settings['groups'] as $group) {
        
                    // add our new group
                    $__new_groups[ $group_order ] = [];
                    
                    // set a name for this new group
                    $__new_groups[ $group_order ]['name'] = isset( $group['name'] ) ? sanitize_text_field( $group['name'] ) : "";
        
                    if (isset($group['classes']) && is_array($group['classes'])) {
        
                        // clear out our list of classes for this group
                        $classes = [];
                        
                        // start class order at 0
                        $class_order = 0;
                        foreach ($group['classes'] as $class) {
        
                            $classes[] = array(
                                        'id' => isset($class['id']) ? sanitize_text_field($class['id']) : "",
                                        'name' => isset($class['name']) ? sanitize_text_field($class['name']) : "",
                                        'order' => $class_order,
                            );
                            // increment class_order
                            $class_order++;
                        }
        
                        // Replace the associative classes array with the sequential one
                        $__new_groups[ $group_order ]['classes'] = $classes;
                        $__new_groups[ $group_order ][ 'singleton' ] = isset( $group[ 'singleton' ] ) ? '1' : '0';
                    }
                    $__new_groups[ $group_order ][ 'order' ] = $group_order;
                    
                    // increment group_order
                    $group_order++;
                }
        
                $settings[ 'groups' ] = $__new_groups;

                // Save to db
                update_option('beaver_builder_class_dropdown_options', $settings);
                
            }
        
        } elseif ( 'import' == $action ) {

            if ( isset($_FILES["fileToUpload"] ) && $_FILES[ 'fileToUpload' ][ 'tmp_name' ] !== '' ) {
                $content = file_get_contents( $_FILES["fileToUpload"]["tmp_name"] );

                try {
                    $json = json_decode( $content , true );
                } catch ( \Exception $error) {
                    return false;
                }
                // Save to db
                update_option('beaver_builder_class_dropdown_options', $json);

            }

        }



        \FLBuilderModel::delete_asset_cache_for_all_posts();
        
    }

}