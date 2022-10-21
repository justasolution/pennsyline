<?php
namespace BooklyServiceExtras\Backend\Components\Dialogs\Service\Edit\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyServiceExtras\Lib;
use Bookly\Backend\Components\Dialogs\Service\Edit\Proxy;
use BooklyServiceExtras\Backend\Components\Dialogs\Service\Edit\Forms;
use BooklyServiceExtras\Lib\Entities\ServiceExtra;

/**
 * Class Shared
 * @package BooklyServiceExtras\Backend\Components\Dialogs\Service\Edit\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function enqueueAssetsForServices()
    {
        $list = Lib\Utils\Common::getExtrasList();

        self::enqueueStyles( array(
            'bookly' => array(
                'backend/resources/css/typeahead.css' => array( 'bookly-backend-globals' ),
            ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/extras.js' => array( 'jquery' ), ),
            'bookly' => array(
                'backend/resources/js/typeahead.bundle.min.js' => array( 'jquery' ),
            ),
        ) );

        wp_localize_script( 'bookly-extras.js', 'BooklyExtrasL10n', array(
            'list' => $list,
            'quantity_error' => __( 'Min quantity should not be greater than max quantity', 'bookly' ),
        ) );
    }

    /**
     * @inheritDoc
     */
    public static function prepareAfterServiceList( $html )
    {
        return $html . self::renderTemplate( 'extras_blank', array(), false  );
    }

    /**
     * @inheritDoc
     */
    public static function prepareUpdateServiceResponse( array $response, BooklyLib\Entities\Service $service, array $_post )
    {
        $response['new_extras_list'] = Lib\Utils\Common::getExtrasList();

        return $response;
    }

    /**
     * @inheritDoc
     */
    public static function updateService( array $alert, BooklyLib\Entities\Service $service, array $_post )
    {
        if ( isset( $_post['extras'] ) ) {
            $extras         = $_post['extras'];
            $current_ids    = array_map( function ( ServiceExtra $se ) { return $se->getId(); }, ServiceExtra::query()->where( 'service_id', $service->getId() )->find() );
            $ids_to_delete  = array_diff( $current_ids, array_keys( $extras ) );
            if ( ! empty ( $ids_to_delete ) ) {
                // Remove redundant extras.
                ServiceExtra::query()->delete()->whereIn( 'id', $ids_to_delete )->execute();
            }
            foreach ( $extras as $id => $data ) {
                $form               = new Forms\ServiceExtra();
                $data['service_id'] = $service->getId();
                $form->bind( $data );
                $form->save();
            }
        } else {
            ServiceExtra::query()->delete()->where( 'service_id', $service->getId() )->execute();
        }

        return $alert;
    }
}