<?php
namespace BooklyFiles\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Shared
 * @package BooklyFiles\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{

    /**
     * Prepare appearance options.
     *
     * @param array $options_to_save
     * @param array $options
     * @return array
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        $options_to_save = array_merge( $options_to_save, array_intersect_key( $options, array_flip( array (
            'bookly_files_enabled',
            'bookly_l10n_browse',
        ) ) ) );

        return $options_to_save;
    }
}