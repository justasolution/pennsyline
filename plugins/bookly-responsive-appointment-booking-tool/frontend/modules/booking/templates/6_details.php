<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib;
use Bookly\Lib\Utils\Common;
use Bookly\Frontend\Modules\Booking\Proxy;
use Bookly\Frontend\Components;

/** @var Lib\UserBookingData $userData */
echo Common::stripScripts( $progress_tracker );
?>

<div class="bookly-box"><?php echo Common::html( $info_text ) ?></div>
<?php if ( $info_text_guest ) : ?>
    <div class="bookly-box bookly-js-guest"><?php echo Common::html( $info_text_guest ) ?></div>
<?php endif ?>
<?php if ( ! get_current_user_id() && ! $userData->getFacebookId() && ( Lib\Config::showLoginButton() || Lib\Proxy\Pro::showFacebookLoginButton() ) ) : ?>
<div class="bookly-box bookly-guest bookly-js-guest">
    <?php if ( Lib\Config::showLoginButton() ) : ?>
        <button class="bookly-btn bookly-js-login-show ladda-button"><?php echo Common::getTranslatedOption( 'bookly_l10n_step_details_button_login' ) ?></button>
    <?php endif ?>
    <?php Proxy\Pro::renderFacebookButton() ?>
</div>
<?php endif ?>
<div class="bookly-box bookly-table" style="border-bottom: 2px solid black;padding-bottom: 20px; display: none;">
    <div class="bookly-form-group">
        <label><?php esc_html_e( 'Existing Customer LookUp' ) ?></label>
        <?php
            $customers_count = Lib\Entities\Customer::query( 'c' )->count();
            if ( $customers_count < Lib\Entities\Customer::REMOTE_LIMIT ) {
                foreach ( Lib\Entities\Customer::query()->sortBy( 'full_name' )->find() as $customer ) {
                    $name = $customer->getFullName();
                    if ( $customer->getEmail() != '' || $customer->getPhone() != '' ) {
                        $name .= ' (' . trim( $customer->getEmail() . ', ' . $customer->getPhone(), ', ' ) . ')';
                    }

                    $result['customers'][] = array(
                        'id'            => (int) $customer->getId(),
                        'name'          => $name,
                        'full_name'     => $customer->getFullName(),
                        'email'         => $customer->getEmail(),
                        'group_id'      => $customer->getGroupId(),
                        'timezone'      => Lib\Proxy\Pro::getLastCustomerTimezone( $customer->getId() ),
                    );
                }
            } else {
                $result['customers_loaded'] = false;
            }

             echo '<select class="form-control bookly-js-select" id="mady_users_list">';
             echo '<option value="">Select</option>';
             foreach ($result['customers'] as $user) {
                echo '<option value="'.$user['full_name'] .'">' . $user['name'].'</option>';
             }
            echo '</select>';
            //echo "<pre>".print_r(($result['customers']),true)."</pre>";
        ?>
    </div>
    <div class="bookly-form-group" style="padding-left: 20px">
        <label></label>
        <button class="form-control" style="padding: 5px" id="load_customer_profile" data-spinner-size="40" data-style="zoom-in">
            <span class="ladda-label"><?php esc_html_e( 'Load User' ) ?></span>
        </button>
    </div>
    <div class="bookly-form-group" style="padding-left: 20px">
        <label></label>
        <button class="form-control" style="padding: 5px" id="clear_user_form" data-spinner-size="40" data-style="zoom-in">
            <span class="ladda-label"><?php esc_html_e( 'Clear Form' ) ?></span>
        </button>
    </div>
</div>
<div class="bookly-details-step">
    <?php if ( Lib\Config::showFirstLastName() ) : ?>
    <div class="bookly-box bookly-table">
        <div class="bookly-form-group">
            <label><?php echo Common::getTranslatedOption( 'bookly_l10n_label_first_name' ) ?></label>
            <div>
                <input class="bookly-js-first-name" type="text" value="<?php echo esc_attr( $userData->getFirstName() ) ?>"/>
            </div>
            <div class="bookly-js-first-name-error bookly-label-error"></div>
        </div>
        <div class="bookly-form-group">
            <label><?php echo Common::getTranslatedOption( 'bookly_l10n_label_last_name' ) ?></label>
            <div>
                <input class="bookly-js-last-name" type="text" value="<?php echo esc_attr( $userData->getLastName() ) ?>"/>
            </div>
            <div class="bookly-js-last-name-error bookly-label-error"></div>
        </div>
    </div>

    <?php endif ?>
    <div class="bookly-box bookly-table">
        <?php if ( ! Lib\Config::showFirstLastName() ) : ?>
        <div class="bookly-form-group">
            <label><?php echo Common::getTranslatedOption( 'bookly_l10n_label_name' ) ?></label>
            <div>
                <input class="bookly-js-full-name" type="text" value="<?php echo esc_attr( $userData->getFullName() ) ?>"/>
            </div>
            <div class="bookly-js-full-name-error bookly-label-error"></div>
        </div>
        <?php endif ?>
        <div class="bookly-form-group">
            <label><?php echo Common::getTranslatedOption( 'bookly_l10n_label_phone' ) ?></label>
            <div>
                <input class="bookly-js-user-phone-input<?php if ( get_option( 'bookly_cst_phone_default_country' ) != 'disabled' ) : ?> bookly-user-phone<?php endif ?>" value="<?php echo esc_attr( $userData->getPhone() ) ?>" type="text" />
            </div>
            <div class="bookly-js-user-phone-error bookly-label-error"></div>
        </div>
        <?php if ( Lib\Config::showFirstLastName() || ( ! Lib\Config::showFirstLastName() && ! Lib\Config::showEmailConfirm() ) ) : ?>
            <?php $self::renderTemplate( '_details_email', compact('userData') ) ?>
        <?php endif ?>
        <?php if ( Lib\Config::showFirstLastName() && Lib\Config::showEmailConfirm() ) : ?>
            <?php $self::renderTemplate( '_details_email_confirm', compact('userData') ) ?>
        <?php endif ?>
    </div>
    <?php if ( ! Lib\Config::showFirstLastName() && Lib\Config::showEmailConfirm() ) : ?>
        <div class="bookly-box bookly-table">
            <?php $self::renderTemplate( '_details_email', compact( 'userData' ) ) ?>
            <?php $self::renderTemplate( '_details_email_confirm', compact( 'userData' ) ) ?>
        </div>
    <?php endif ?>

    <?php Proxy\Pro::renderDetailsAddress( $userData ) ?>
    <?php Proxy\Pro::renderDetailsBirthday( $userData ) ?>

    <?php Proxy\CustomerInformation::renderDetailsStep( $userData ) ?>
    <?php Proxy\Shared::renderCustomFieldsOnDetailsStep( $userData ) ?>
    <?php if ( Lib\Config::showNotes() ): ?>
        <div class="bookly-box">
            <div class="bookly-form-group">
                <label><?php echo Common::getTranslatedOption( 'bookly_l10n_label_notes' ) ?></label>
                <div>
                    <textarea class="bookly-js-user-notes" rows="3"><?php echo esc_html( $userData->getNotes() ) ?></textarea>
                </div>
            </div>
        </div>
    <?php endif ?>
    <?php if ( get_option( 'bookly_app_show_terms', false ) ): ?>
        <div class="bookly-box">
            <div class="bookly-checkbox-group" style="line-height: 28px;">
                <input type="checkbox" class="bookly-js-terms" id="bookly-terms-<?php echo $userData->getFormId() ?>">
                <label class="bookly-square bookly-checkbox" style="width:28px; float:left; margin-left: 0; margin-right: 5px;" for="bookly-terms-<?php echo $userData->getFormId() ?>">
                    <i class="bookly-icon-sm"></i>
                </label>
                <label for="bookly-terms-<?php echo $userData->getFormId() ?>">
                    <?php echo Common::getTranslatedOption( 'bookly_l10n_label_terms' ) ?>
                </label>
            </div>
            <div class="bookly-js-terms-error bookly-label-error"></div>
        </div>
    <?php endif ?>
</div>

<?php Proxy\RecurringAppointments::renderInfoMessage( $userData ) ?>

<div class="bookly-box bookly-nav-steps">
    <?php if ( $show_back_btn ) : ?>
    <button class="bookly-back-step bookly-js-back-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
        <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_back' ) ?></span>
    </button>
    <?php endif ?>
    <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
        <button class="bookly-next-step bookly-js-next-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
            <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_step_details_button_next' ) ?></span>
        </button>
    </div>
</div>