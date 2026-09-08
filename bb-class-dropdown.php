<?php
/**
 * Beaver Builder Class Dropdown
 *
 * @package     BBClassDropdown
 * @author      SnippetNest
 * @license     GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name: Beaver Builder Class Dropdown
 * Plugin URI:  https://snippetnest.com/plugin/beaver-builder-class-dropdown/
 * Description: BB Class Dropdown adds user defined CSS classes to dropdown below the Beaver Builder class input in the Advanced tab
 * Version:     1.0.10
 * Author:      SnippetNest
 * Author URI: 	https://snippetnest.com/
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
 
   define( 'BBCLASSDROPDOWN_VERSION'   , '1.0.10' );
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
    require_once BBCLASSDROPDOWN_DIR . 'includes/classes/Autoloader.php';
    spl_autoload_register( array( new Autoloader(), 'autoload' ) );
 
    $plugin_var = new Init();
 
}

// SN Analytics
if ( ! defined( 'SN_ANALYTICS_ENDPOINT' ) ) {
	define( 'SN_ANALYTICS_ENDPOINT', 'https://snippetnest.com/wp-json/sn-analytics/v1/ping' );
}
if ( ! defined( 'SN_ANALYTICS_SECRET' ) ) {
	define( 'SN_ANALYTICS_SECRET', 'b64:O84AMMGANsFvXQFWgVW5OrbzghcxWZ7ygb3vUhWGZiA=' );
}
require_once __DIR__ . '/includes/class-sn-analytics.php';
if ( class_exists( 'SN_Analytics' ) ) {
	$sn_analytics = new SN_Analytics( __FILE__, 'bb-class-dropdown' );
	register_activation_hook( __FILE__, function() use ( $sn_analytics ) {
		$sn_analytics->activate();
	} );
	register_deactivation_hook( __FILE__, function() use ( $sn_analytics ) {
		$sn_analytics->deactivate();
	} );
	$sn_analytics->initialize();
}
function sn_bbclassdropdown_analytics_uninstall() {
	require_once __DIR__ . '/includes/class-sn-analytics.php';
	if ( class_exists( 'SN_Analytics' ) ) {
		SN_Analytics::uninstall( 'bb-class-dropdown' );
	}
}
register_uninstall_hook( __FILE__, 'sn_bbclassdropdown_analytics_uninstall' );
