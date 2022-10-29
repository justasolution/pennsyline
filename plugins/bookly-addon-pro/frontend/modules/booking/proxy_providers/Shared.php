<?php
namespace BooklyPro\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Booking\Proxy;
use Bookly\Lib\Config;
use BooklyPro\Lib;

/**
 * Class Shared
 * @package BooklyPro\Frontend\Modules\Booking\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function enqueueBookingScripts( array $depends )
    {
        if ( Lib\Config::showFacebookLoginButton() && ! get_current_user_id() ) {
            wp_enqueue_script( 'bookly-sdk.js', sprintf( 'https://connect.facebook.net/%s/sdk.js', BooklyLib\Config::getLocale() ) );

            $depends[] = 'bookly-sdk.js';
        }

        return $depends;
    }

    /**
     * @inheritDoc
     */
    public static function booklyFormOptions( array $bookly_options )
    {
        $bookly_options['facebook'] = array(
            'enabled' => (int) ( Lib\Config::showFacebookLoginButton() && ! get_current_user_id() ),
            'appId'   => Lib\Config::getFacebookAppId(),
        );

        return $bookly_options;
    }

    /**
     * @inheritDoc
     */
    public static function prepareCartItemInfoText( $data, BooklyLib\CartItem $cart_item, $userData )
    {
        if ( $cart_item->getAppointmentId() ) {
            $data['online_meeting_url'][] = BooklyLib\Proxy\Shared::buildOnlineMeetingUrl( '', $cart_item->getAppointment(), $userData->getCustomer() );
            $data['online_meeting_password'][] = BooklyLib\Proxy\Shared::buildOnlineMeetingPassword( '', $cart_item->getAppointment() );
            $data['online_meeting_join_url'][] = BooklyLib\Proxy\Shared::buildOnlineMeetingJoinUrl( '', $cart_item->getAppointment(), $userData->getCustomer() );
        }

        $staff_category = $data['staff'] && $data['staff']->getCategoryId() ? Lib\Entities\StaffCategory::find( $data['staff']->getCategoryId() ) : null;
        $data['staff_category_name'][] = $staff_category ? $staff_category->getTranslatedName() : '';
        $data['staff_category_info'][] = $staff_category ? $staff_category->getTranslatedInfo() : '';
        $data['staff_category_image'][] = ( $staff_category && ( $url = $staff_category->getImageUrl() ) ) ? '<img src="' . $url . '"/>' : '';

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function prepareChainItemInfoText( $data, BooklyLib\ChainItem $chain_item )
    {
        $staff_category = $data['staff'] && $data['staff']->getCategoryId() ? Lib\Entities\StaffCategory::find( $data['staff']->getCategoryId() ) : null;
        $data['staff_category_name'][] = $staff_category ? $staff_category->getTranslatedName() : '';
        $data['staff_category_info'][] = $staff_category ? $staff_category->getTranslatedInfo() : '';
        $data['staff_category_image'][] = ( $staff_category && ( $url = $staff_category->getImageUrl() ) ) ? '<img src="' . $url . '"/>' : '';

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function prepareInfoTextCodes( array $codes, array $data )
    {
        $keys = array( 'online_meeting_url', 'online_meeting_password', 'online_meeting_join_url', 'staff_category_image', 'staff_category_info', 'staff_category_name' );
        foreach ( $keys as $key ) {
            $codes[ $key ] = isset( $data[ $key ] ) ? implode( ', ', $data[ $key ] ) : '';
        }

        return $codes;
    }

    /**
     * @inheritDoc
     */
    public static function preparePaymentOptions( $options, $form_id, $show_price, BooklyLib\CartInfo $cart_info, $userData )
    {
        if ( Config::paypalEnabled() ) {
            $gateway = BooklyLib\Entities\Payment::TYPE_PAYPAL;
            if ( Proxy\CustomerGroups::allowedGateway( $gateway, $userData ) !== false ) {
                $cart_info->setGateway( $gateway );
                $payment_status = $userData->extractPaymentStatus( $cart_info->getGateway() );

                $options[ $gateway ] = array(
                    'html' => self::renderTemplate(
                        'paypal_payment_option',
                        compact( 'form_id', 'show_price', 'cart_info', 'payment_status' ),
                        false
                    ),
                    'pay' => $cart_info->getPayNow(),
                );
            }
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    public static function renderPaymentForms( $form_id, $page_url )
    {
        if ( Config::paypalEnabled() ) {
            switch ( get_option( 'bookly_paypal_enabled' ) ) {
                case Lib\Payment\PayPal::TYPE_EXPRESS_CHECKOUT:
                    \Bookly\Frontend\Components\Payment\Gateway::renderForm( $form_id, 'paypal', $page_url, 'paypal-express-checkout' );
                    break;
                case Lib\Payment\PayPal::TYPE_PAYMENTS_STANDARD:
                    Proxy\PaypalPaymentsStandard::renderPaymentForm( $form_id, $page_url );
                    break;
                case Lib\Payment\PayPal::TYPE_CHECKOUT:
                    \Bookly\Frontend\Components\Payment\Gateway::renderForm( $form_id, 'paypal', $page_url );
                    break;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function stepOptions( array $options, $step, $userData )
    {
        if ( $step == 'service' ) {
            $options['l10n']['duration_label'] = BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_service_duration' );
        } elseif ( $step == 'details' ) {
            if ( BooklyLib\Config::wooCommerceEnabled() ) {
                $options['woocommerce']['enabled'] = 1;
                $options['woocommerce']['cart_url'] = wc_get_cart_url();
                if ( ! get_option( 'bookly_wc_create_order_at_zero_cost' ) ) {
                    $cart_info = $userData->cart->getInfo();
                    $options['woocommerce']['enabled'] = $cart_info->getTotal() > 0;
                }
            }
        } elseif ( $step == 'complete' ) {
            $final_page_url = '';
            $items = $userData->cart->getItems();
            if ( count( $items ) === 1 ) {
                $final_page_url = $items[0]->getService()->getFinalStepUrl();
            }

            $options['final_step_url'] = $final_page_url ?: get_option( 'bookly_url_final_step_url' );
        }

        return $options;
    }
}