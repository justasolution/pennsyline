<?php
namespace BooklyCustomStatuses\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;

/**
 * Class Shared
 * @package BooklyCustomStatuses\Lib\ProxyProviders
 */
class Shared extends BooklyLib\Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function prepareTableColumns( $columns, $table )
    {
        if ( $table == BooklyLib\Utils\Tables::CUSTOM_STATUSES ) {
            $columns = array_merge( $columns, array(
                'id'   => esc_html__( 'ID', 'bookly' ),
                'name' => esc_html__( 'Name', 'bookly' ),
                'busy' => esc_html__( 'Free/Busy', 'bookly' ),
            ) );
        }

        return $columns;
    }

    /**
     * @inheritDoc
     */
    public static function prepareTableDefaultSettings( $columns, $table )
    {
        if ( $table == BooklyLib\Utils\Tables::CUSTOM_STATUSES ) {
            $columns = array_merge( $columns, array(
                'id' => false,
            ) );
        }

        return $columns;
    }

    /**
     * @inheritDoc
     */
    public static function prepareColorsStatuses( array $statuses )
    {
        foreach ( Local::getAll() as $status ) {
            $statuses[ $status->getSlug() ] = $status->getColor();
        }

        return $statuses;
    }
}