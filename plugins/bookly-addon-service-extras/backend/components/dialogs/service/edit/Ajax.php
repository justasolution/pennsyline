<?php
namespace BooklyServiceExtras\Backend\Components\Dialogs\Service\Edit;

use Bookly\Lib as BooklyLib;
use BooklyServiceExtras\Lib;
use BooklyServiceExtras\Lib\Entities;

/**
 * Class Ajax
 * @package BooklyServiceExtras\Backend\Components\Dialogs\Service\Edit
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * Update extras position.
     */
    public static function updateExtraPosition()
    {
        $extras_sort = (array) self::parameter( 'positions' );
        foreach ( $extras_sort as $position => $extra_id ) {
            if ( strpos( $extra_id, 'new' ) === false ) {
                $extra = new Entities\ServiceExtra();
                $extra->load( $extra_id );
                $extra->setPosition( $position );
                $extra->save();
            }
        }

        wp_send_json_success();
    }
}