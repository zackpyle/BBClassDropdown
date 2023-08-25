<?php
namespace BBClassDropdown;

use BBClassDropdown\GithubUpdater;

use BBClassDropdown\Helpers\Activation;
use BBClassDropdown\Helpers\AdminSettings;
use BBClassDropdown\Helpers\StyleScript;
use BBClassDropdown\Integration\BeaverBuilder;

class Init {

    public function __construct() {

        self::init_updater();

        new Activation();
        new AdminSettings();
        new StyleScript();
        new BeaverBuilder();
        
    }
        
    /**
     * updater
     *
     * @return void
     */
    public static function init_updater() {
        $updater = new GithubUpdater( BBCLASSDROPDOWN_FILE );
        $updater->set_username( 'zackpyle' );
        $updater->set_repository( 'BBClassDropdown' );
        $updater->set_settings( array(
                    'requires'			=> '5.1',
                    'tested'			=> '6.3',
                    'rating'			=> '100.0',
                    'num_ratings'		=> '10',
                    'downloaded'		=> '10',
                    'added'				=> '2023-08-20',
                ) );
        $updater->initialize();

    }
}
