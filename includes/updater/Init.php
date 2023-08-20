<?php
namespace BBClassDropdown\Includes\Updater;
use BBClassDropdown\Includes\Updater\GithubUpdater;

class Init {

    public function __construct() {

        $updater = new GithubUpdater( BBCLASSDROPDOWN_FILE );
        $updater->set_username( 'zackpyle' );
        $updater->set_repository( 'test' );
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
