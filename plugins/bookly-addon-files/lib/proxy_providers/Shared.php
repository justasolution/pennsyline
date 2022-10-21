<?php
namespace BooklyFiles\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyFiles\Lib\Entities;

/**
 * Class Shared
 * Provide shared methods to be used in Bookly.
 *
 * @package BooklyFiles\Lib\ProxyProviders
 */
abstract class Shared extends BooklyLib\Proxy\Shared
{
    /**
     * After deletion of the customer appointments the affiliated files will be safely removed.
     *
     * @param BooklyLib\Entities\CustomerAppointment $ca
     */
    public static function deleteCustomerAppointment( BooklyLib\Entities\CustomerAppointment $ca )
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();

        $custom_fields = (array) json_decode( $ca->getCustomFields(), true );
        $custom_fields_with_file = Local::getAllIds();

        foreach ( $custom_fields as $id => $slug ) {
            if ( in_array( $id, $custom_fields_with_file ) ) {
                $file  = new Entities\Files();
                if ( $file->loadBy( compact( 'slug' ) ) ) {
                    /** @var Entities\CustomerAppointmentFiles[] $caf_list */
                    $caf_list = Entities\CustomerAppointmentFiles::query()
                        ->where( 'file_id', $file->getId() )
                        ->find();
                    $delete_file = true;
                    foreach ( $caf_list as $caf ) {
                        if ( $caf->getCustomerAppointmentId() == $ca->getId() ) {
                            $caf->delete();
                        } else {
                            $delete_file = false;
                        }
                    }
                    if ( $delete_file ) {
                        $fs->delete( $file->getPath(), 'false', 'f' );
                        $file->delete();
                    }
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function prepareTableColumns( $columns, $table )
    {
        if ( $table == BooklyLib\Utils\Tables::APPOINTMENTS ) {
            $columns['attachments'] = __( 'Attachments', 'bookly' );
        }

        return $columns;
    }
}