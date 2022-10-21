<?php
namespace BooklyFiles\Backend\Modules\CustomFields\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Lib\Proxy;
use BooklyFiles\Lib\Entities;

/**
 * Class Local
 * @package BooklyFiles\Backend\Modules\CustomFields\ProxyProviders
 */
class Local extends Proxy\Files
{
    /**
     * @inheritDoc
     */
    public static function renderCustomFieldTemplate( $services_html, $description_html )
    {
        self::renderTemplate( 'file', compact( 'services_html', 'description_html' ) );
    }

    /**
     * @inheritDoc
     */
    public static function renderCustomFieldButton()
    {
        printf( '<button class="btn btn-default mb-2 mr-1" data-type="file"><i class="fas fa-fw fa-plus mr-1"></i>%s</button>', __( 'File', 'bookly' ) );
    }

    /**
     * @inheritDoc
     */
    public static function saveCustomFields( array $custom_fields )
    {
        $fs = BooklyLib\Utils\Common::getFilesystem();
        $custom_fields_with_file = Proxy\Files::getAllIds();
        foreach ( $custom_fields_with_file as $cf_id ) {
            $missing = true;
            foreach ( $custom_fields as $field ) {
                if ( $field->id == $cf_id ) {
                    $missing = false;
                    break;
                }
            }
            if ( $missing ) {
                /** @var Entities\Files[] $files */
                $files = Entities\Files::query()->where( 'custom_field_id', $cf_id )->find();
                foreach ( $files as $file ) {
                    $fs->delete( $file->getPath(), false, 'f' );
                    $file->delete();
                }
                break;
            }
        }
    }
}