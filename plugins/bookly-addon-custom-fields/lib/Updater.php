<?php
namespace BooklyCustomFields\Lib;

use Bookly\Lib;

/**
 * Class Updates
 * @package BooklyCustomFields\Lib
 */
class Updater extends Lib\Base\Updater
{
    public function update_3_0()
    {
        global $sitepress;

        $custom_fields = json_decode( get_option( 'bookly_custom_fields_data', '[]' ), true );
        $names = array();
        foreach ( $custom_fields as $custom_field ) {
            switch ( $custom_field['type'] ) {
                case 'textarea':
                case 'text-content':
                case 'text-field':
                case 'captcha':
                case 'file':
                case 'checkboxes':
                case 'radio-buttons':
                case 'drop-down':
                    $name = 'custom_field_' . $custom_field['id'] . '_%s';
                    $title = sanitize_title( $custom_field['label'] );
                    $key = substr( sprintf( $name, $title ), 0, 160 );
                    $names[ $key ] = sprintf( $name, substr( $title, 0, 160 - strlen( $name ) ) );
                    $name = 'custom_field_' . $custom_field['id'] . '_%s_description';
                    $key = substr( sprintf( $name, $title ), 0, 160 );
                    $new_name = 'custom_field_' . $custom_field['id'] . '_%s_descr';
                    $names[ $key ] = sprintf( $new_name, substr( $title, 0, 160 + 1 - strlen( $new_name ) ) );
                case 'checkboxes':
                case 'radio-buttons':
                case 'drop-down':
                    $name = 'custom_field_' . $custom_field['id'] . '_%s=%s';
                    foreach ( $custom_field['items'] as $label ) {
                        $label = sanitize_title( $label );
                        $key = substr( sprintf( $name, $title, $label ), 0, 160 );
                        $names[ $key ] = sprintf( $name, substr( $title, 0, 32 ), substr( $label, 0, 160 + 2 - 32 - strlen( $name ) ) );
                    }
                    break;
            }
        }

        $this->renameL10nStrings( $names, false );
        if ( $sitepress instanceof \SitePress ) {
            add_option( 'bookly_show_wpml_resave_required_notice', '1' );
        }
    }

    public function update_2_8()
    {
        $custom_fields = json_decode( get_option( 'bookly_custom_fields_data', '[]' ), true );
        foreach ( $custom_fields as &$custom_field ) {
            if ( ! isset( $custom_field['description'] ) ) {
                $custom_field['description'] = '';
            }
        }
        update_option( 'bookly_custom_fields_data', json_encode( $custom_fields ) );

        $conditions = get_option( 'bookly_custom_fields_conditions', array() );
        foreach ( $conditions as &$condition ) {
            if ( isset( $condition['value'] ) && ! is_array( $condition['value'] ) ) {
                $condition['value'] = array( $condition['value'] );
            }
        }
        update_option( 'bookly_custom_fields_conditions', $conditions );
    }
}