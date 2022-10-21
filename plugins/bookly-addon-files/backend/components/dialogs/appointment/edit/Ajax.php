<?php
namespace BooklyFiles\Backend\Components\Dialogs\Appointment\Edit;

use Bookly\Lib as BooklyLib;
use BooklyFiles\Lib\Entities;

/**
 * Class Ajax
 * @package BooklyFiles\Backend\Components\Dialogs\Appointment\Edit
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'supervisor' );
    }

    /**
     * Disconnect file from Customer appointment.
     */
    public static function deleteCustomField()
    {
        $slug = self::parameter( 'slug' );
        $ca_id = self::parameter( 'ca_id' );
        $file = new Entities\Files();
        if ( $file->loadBy( compact( 'slug' ) ) ) {
            $file->deleteSafely( $ca_id );
            if( $ca_id ) {
                $slug = $file->getSlug();
                $ca   = BooklyLib\Entities\CustomerAppointment::find( $ca_id );
                $custom_fields = array_filter( json_decode( $ca->getCustomFields(), true ), function ( $custom_field ) use ( $slug ) {
                    return ! ( isset( $custom_field['value'] ) && $custom_field['value'] === $slug );
                } );
                $ca->setCustomFields( json_encode( $custom_fields ) )->save();
            }
        }
        wp_send_json_success();
    }
}