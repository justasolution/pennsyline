<?php
namespace BooklyFiles\Backend\Components\Dialogs\Appointment\Attachments;

use Bookly\Lib as BooklyLib;
use BooklyFiles\Lib\Entities;
Use BooklyFiles\Lib\ProxyProviders\Local;

class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => array( 'staff', 'supervisor' ) );
    }

    /**
     * json with list attachment.
     */
    public static function getAttachments()
    {
        $ca_id = self::parameter( 'ca_id' );
        $files = array();
        if ( $ca = BooklyLib\Entities\CustomerAppointment::find( $ca_id ) ) {
            $custom_fields = (array) json_decode( $ca->getCustomFields(), true );
            $names         = Local::getFileNamesForCustomFields( $custom_fields );
            if ( $names ) {
                $file_labels = array();
                foreach ( Local::getAll() as $field ) {
                    $file_labels[ $field->id ] = $field->label;
                }
                foreach ( $custom_fields as $custom_field ) {
                    $field_id = $custom_field['id'];
                    if ( isset( $names[ $field_id ] ) ) {
                        $files[] = array(
                            'label' => $file_labels[ $field_id ],
                            'name'  => $names[ $field_id ],
                            'slug'  => $custom_field['value'],
                        );
                    }
                }
            }
        }

        $download_url = add_query_arg( array( 'action' => 'bookly_files_download', 'csrf_token' => BooklyLib\Utils\Common::getCsrfToken() ), admin_url( 'admin-ajax.php' ) );
        $html = self::renderTemplate( '_dialog_body', compact( 'files', 'download_url' ), false );
        wp_send_json_success( compact( 'html' ) );
    }

    /**
     * Download file.
     */
    public static function download()
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();

        $slug = self::parameter( 'slug' );
        $file = new Entities\Files();
        if ( $file->loadBy( compact( 'slug' ) ) && $fs->is_readable( $file->getPath() ) ) {
            header( 'Content-Description: File Transfer' );
            header( 'Content-Type: application/octet-stream' );
            header( 'Content-Disposition: attachment; filename="' . $file->getName() .'"' );
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Connection: Keep-Alive' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Pragma: public' );
            header( 'Content-Length: ' . $fs->size( $file->getPath() ) );
            print $fs->get_contents( $file->getPath() );
        }
        exit;
    }
}