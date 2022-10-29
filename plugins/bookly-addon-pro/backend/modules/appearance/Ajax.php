<?php
namespace BooklyPro\Backend\Modules\Appearance;

use Bookly\Lib as BooklyLib;
use BooklyPro\Lib;

/**
 * Class Ajax
 *
 * @package BooklyPro\Backend\Modules\Appearance
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * Get list of form appearances
     */
    public static function getFormsList()
    {
        $forms = Lib\Entities\Form::query()
            ->where( 'type', self::parameter( 'form_type' ) )
            ->fetchArray();

        wp_send_json_success( compact( 'forms' ) );
    }

    public static function saveAppearance()
    {
        $id = self::parameter( 'id' );
        $type = self::parameter( 'type' );
        $settings = self::parameter( 'settings' );
        $name = self::parameter( 'name' );
        $custom_css = self::parameter( 'custom_css' );
        $token = self::parameter( 'token' );
        $form = new Lib\Entities\Form();
        if ( $id ) {
            $form->load( $id );
        }

        foreach ( $settings['l10n'] as $l10n ) {
            do_action( 'wpml_register_single_string', 'bookly', 'appearance_string_' . md5( $l10n ), $l10n );
        }
        // Convert js strings 'true','false' to boolean for checkboxes
        foreach ( array( 'show_book_more', 'show_qr_code', 'show_extras_price', 'show_extras_summary', 'show_terms', 'skip_services_step', 'skip_categories_step', 'show_reason' ) as $field ) {
            if ( isset( $settings[ $field ] ) ) {
                $settings[ $field ] = $settings[ $field ] === 'true';
            }
        }
        if ( $settings['categories_any'] ) {
            if ( $settings['categories_any'] === 'true' ) {
                $settings['categories_list'] = null;
            }
            unset( $settings['categories_any'] );
        }
        if ( $settings['services_any'] ) {
            if ( $settings['services_any'] === 'true' ) {
                $settings['services_list'] = null;
            }
            unset( $settings['services_any'] );
        }
        $form
            ->setToken( $token )
            ->setType( $type )
            ->setName( $name )
            ->setCustomCss( $custom_css )
            ->setSettings( json_encode( $settings ) );

        if ( $form->save() ) {
            wp_send_json_success( array( 'id' => $form->getId(), 'token' => $form->getToken() ) );
        } else {
            wp_send_json_error();
        }
    }

    public static function deleteAppearance()
    {
        $id = self::parameter( 'id' );
        $form = Lib\Entities\Form::find( $id );
        if ( $form ) {
            $form->delete();
        }

        wp_send_json_success();
    }

    public static function cloneAppearance()
    {
        $id = self::parameter( 'id' );
        $form = Lib\Entities\Form::find( $id );
        if ( $form ) {
            $new_name = $form->getName() . ' clone';
            $form
                ->setId( null )
                ->setName( $new_name )
                ->setToken( null )
                ->save();
        }

        wp_send_json_success();
    }

    public static function dismissAppearanceNotice()
    {
        update_user_meta( get_current_user_id(), Lib\Plugin::getPrefix() . 'dismiss_modern_appearance_notice', 1 );

        wp_send_json_success();
    }
}