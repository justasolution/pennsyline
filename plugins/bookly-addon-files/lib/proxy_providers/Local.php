<?php
namespace BooklyFiles\Lib\ProxyProviders;

use BooklyFiles\Lib;
use Bookly\Lib as BooklyLib;

/**
 * Class Local
 * Provide local methods to be used in Bookly and other add-ons.
 *
 * @package BooklyFiles\Lib\ProxyProviders
 */
abstract class Local extends BooklyLib\Proxy\Files
{
    /******************************************************************************************************************
     * FRONTEND                                                                                                       *
     ******************************************************************************************************************/

    /**
     * @inheritDoc
     */
    public static function getAllIds()
    {
        $custom_fields = (array) BooklyLib\Proxy\CustomFields::getAll( array( 'text-field', 'textarea', 'text-content', 'checkboxes', 'radio-buttons', 'drop-down', 'captcha' ) );

        $ids = array();
        foreach ( $custom_fields as $custom_field ) {
            if ( $custom_field->type == 'file' ) {
                $ids[] = $custom_field->id;
            }
        }

        return $ids;
    }

    /**
     * @inheritDoc
     */
    public static function attachFiles( array $custom_fields, BooklyLib\Entities\CustomerAppointment $ca )
    {
        $custom_fields_with_file = self::getAllIds();

        $customer_appointment_id = $ca->getId();
        foreach ( $custom_fields as $custom_field ) {
            if ( in_array( $custom_field['id'], $custom_fields_with_file ) ) {
                $file = new Lib\Entities\Files();
                if ( $custom_field['value'] && $file->loadBy( array( 'slug' => $custom_field['value'] ) ) ) {
                    $ca_file = new Lib\Entities\CustomerAppointmentFiles();
                    $ca_file->loadBy(
                        array(
                            'customer_appointment_id' => $customer_appointment_id,
                            'file_id'                 => $file->getId(),
                        ) );
                    if ( ! $ca_file->isLoaded() ) {
                        $ca_file
                            ->setCustomerAppointmentId( $customer_appointment_id )
                            ->setFileId( $file->getId() )
                            ->save();
                        self::renameFile( $file, $ca );
                    }
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function getFileNamesForCustomFields( array $custom_fields )
    {
        $names = array();
        $custom_fields_with_file = self::getAllIds();
        foreach ( $custom_fields as $field ) {
            if ( in_array( $field['id'], $custom_fields_with_file ) && isset( $field['value'] ) ) {
                $file = Lib\Entities\Files::query()
                    ->select( 'name' )
                    ->where( 'slug', $field['value'] )
                    ->fetchRow();
                $names[ $field['id'] ] = $file ? $file['name'] : $field['value'];
            }
        }

        return $names;
    }

    /******************************************************************************************************************
     * BACKEND                                                                                                        *
     ******************************************************************************************************************/

    /**
     * @inheritDoc
     */
    public static function setFileNamesForCustomFields( $data, array $custom_fields )
    {
        foreach ( $data as &$customer_custom_field ) {
            if ( array_key_exists( $customer_custom_field['id'], $custom_fields ) ) {
                $field = $custom_fields[ $customer_custom_field['id'] ];
                if ( $field->type == 'file' ) {
                    $file = Lib\Entities\Files::query( 'f' )
                        ->select( '`f`.`name`' )
                        ->where('slug', $customer_custom_field['value'] )
                        ->fetchRow();
                    if ( $file ) {
                        $customer_custom_field['value'] = $file['name'];
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function getAll()
    {
        $custom_fields = (array) BooklyLib\Proxy\CustomFields::getAll( array( 'text-field', 'textarea', 'text-content', 'checkboxes', 'radio-buttons', 'drop-down', 'captcha' ) );

        return array_filter( $custom_fields, function ( $field ) {
            return $field->type == 'file';
        } );

    }

    /**
     * Rename file to human friendly name
     *
     * @param Lib\Entities\Files $file
     * @param BooklyLib\Entities\CustomerAppointment $ca
     * @return Lib\Entities\Files
     */
    protected static function renameFile( Lib\Entities\Files $file, BooklyLib\Entities\CustomerAppointment $ca )
    {
        $mask = '{a_id}-{ca_id}-{cf_id}{random}{extension}';
        $fs = BooklyLib\Utils\Common::getFilesystem();
        $path = $file->getPath();
        $parts = explode( '.', basename( $path ) );
        $dir = dirname( $path );
        $extension = '';
        if ( count( $parts ) > 1 ) {
            $extension .= '.' . end( $parts );
        }
        $random = '';
        do {
            $target = $dir . DIRECTORY_SEPARATOR . strtr( $mask, array(
                    '{a_id}' => sprintf( '%04d', $ca->getAppointmentId() ),
                    '{ca_id}' => sprintf( '%04d', $ca->getId() ),
                    '{cf_id}' => sprintf( '%05d', $file->getCustomFieldId() ),
                    '{random}' => $random,
                    '{extension}' => $extension,
                ) );

            $random = '-' . wp_generate_password( 4, false );
        } while ( $fs->exists( $target ) );

        $fs->move( $file->getPath(), $target );
        $file->setPath( $target )->save();

        return $file;
    }

    /**
     * @return array
     */
    public static function getAllowedExtensions()
    {
        $extensions = strtolower( get_option( 'bookly_files_extensions', '' ) );
        $extensions = preg_replace( '/[^a-z\d,]/', '', $extensions );

        return $extensions === '' ? array() : explode( ',', $extensions );
    }
}