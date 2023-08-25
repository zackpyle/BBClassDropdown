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
 * Version:     1.0.3
 * Author:      PYLE/DIGITAL
 * Author URI: 	https://github.com/zackpyle
 * Text Domain: BBClassDropdown
 * License:     GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 */
 
use BBClassDropdown\Autoloader;
use BBClassDropdown\Init;
 
 if ( defined( 'ABSPATH' ) && ! defined( 'BBCLASSDROPDOWN_VERION' ) ) {
    register_activation_hook( __FILE__, 'BBCLASSDROPDOWN_check_php_version' );
 
    /**
     * Display notice for old PHP version.
     */
    function BBCLASSDROPDOWN_check_php_version() {
        if ( version_compare( phpversion(), '7.4', '<' ) ) {
            die( esc_html__( 'BB Class Dropdown requires PHP version 7.4+. Please contact your host to upgrade.', 'BBClassDropdown' ) );
        }
    }
 
   define( 'BBCLASSDROPDOWN_VERSION'   , '1.0.3' );
   define( 'BBCLASSDROPDOWN_DIR'     , plugin_dir_path( __FILE__ ) );
   define( 'BBCLASSDROPDOWN_BASE'    , plugin_basename( __FILE__ ) );
   define( 'BBCLASSDROPDOWN_FILE'    , __FILE__ );
   define( 'BBCLASSDROPDOWN_URL'     , plugins_url( '/', __FILE__ ) );
 
   define( 'CHECK_BBCLASSDROPDOWN_PLUGIN_FILE', __FILE__ );
 
 }
 
if ( ! class_exists( 'BBClassDropdown\Init' ) ) {
 
    /**
     * The file where the Autoloader class is defined.
     */
    require_once 'includes/classes/Autoloader.php';
    spl_autoload_register( array( new Autoloader(), 'autoload' ) );
 
    $plugin_var = new Init();
 
}
