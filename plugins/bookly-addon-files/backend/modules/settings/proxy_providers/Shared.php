<?php
namespace BooklyFiles\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Backend\Components\Settings\Menu;

/**
 * Class Shared
 * @package BooklyFiles\Backend\Modules\Settings\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function renderMenuItem()
    {
        Menu::renderItem( __( 'Files', 'bookly' ), 'files' );
    }

    /**
     * @inheritDoc
     */
    public static function renderTab()
    {
        self::renderTemplate( 'settings_tab' );
    }

    /**
     * @inheritDoc
     */
    public static function saveSettings( array $alert, $tab, array $params )
    {
        if ( $tab === 'files' ) {
            $options = array( 'bookly_files_directory', 'bookly_files_extensions' );
            foreach ( $options as $option_name ) {
                if ( array_key_exists( $option_name, $params ) ) {
                    update_option( $option_name, $params[ $option_name ] );
                }
            }
            $alert['success'][] = __( 'Settings saved.', 'bookly' );
        }

        return $alert;
    }
}