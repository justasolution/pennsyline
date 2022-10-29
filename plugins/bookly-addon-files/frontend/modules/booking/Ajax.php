<?php
namespace BooklyFiles\Frontend\Modules\Booking;

use Bookly\Lib as BooklyLib;
use BooklyFiles\Lib\Entities;
use BooklyFiles\Lib\ProxyProviders\Local;

/**
 * Class Ajax
 * @package BooklyCustomFields\Frontend\Modules\Booking
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    /**
     * Upload file.
     */
    public static function upload()
    {
        $file = new Entities\Files();
        $name = $_FILES['files']['name'][0];
        $parts = explode( '.', $name );
        $allowed_extensions = Local::getAllowedExtensions();
        $extension = end( $parts );
        if ( $allowed_extensions && ! in_array( strtolower( $extension ), $allowed_extensions, false ) ) {
            BooklyLib\Utils\Common::getFilesystem()->delete( $_FILES['files']['tmp_name'][0], false, 'f' );
            wp_send_json_error( array( 'error' => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_incorrect_file_type' ) ) );
        }
        $slug = BooklyLib\Utils\Common::generateToken( get_class( $file ), 'slug' );
        $path = realpath( get_option( 'bookly_files_directory' ) ) . DIRECTORY_SEPARATOR . $slug;
        if ( count( $parts ) > 1 ) {
            $path .= '.' . $extension;
        }
        $file
            ->setName( $name )
            ->setPath( $path )
            ->setSlug( $slug )
            ->setCustomFieldId( self::parameter( 'custom_field_id' ) );
        if ( move_uploaded_file( $_FILES['files']['tmp_name'][0], $path ) ) {
            $file->save();
            wp_send_json_success( compact( 'name', 'slug' ) );
        } else {
            wp_send_json_error();
        }
    }

    /**
     * Remove (reset) file custom field and safely remove file
     * Frontend only.
     */
    public static function delete()
    {
        $userData = new BooklyLib\UserBookingData( self::parameter( 'form_id' ) );

        if ( $userData->load() ) {
            $slug = self::parameter( 'slug' );

            // Remove file.
            $file = new Entities\Files();
            if ( $file->loadBy( compact( 'slug' ) ) ) {
                $file->deleteSafely();
            }

            // Reset file value to empty string.
            $custom_fields_with_files = Local::getAllIds();
            foreach ( $userData->cart->getItems() as $item ) {
                $custom_fields = $item->getCustomFields();
                foreach ( $custom_fields as $key => $field ) {
                    if ( $field['value']
                        && in_array( $field['id'], $custom_fields_with_files )
                        && $field['value'] == $slug )
                    {
                        $custom_fields[ $key ]['value'] = '';
                    }
                }
                $item->setCustomFields( $custom_fields );
            }
        }
        $userData->sessionSave();

        wp_send_json_success();
    }
}