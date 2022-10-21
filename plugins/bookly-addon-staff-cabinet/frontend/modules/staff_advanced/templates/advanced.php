<?php if (!defined('ABSPATH')) exit;
use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use Bookly\Backend\Components\Controls\Buttons;
/**
 * @var \Bookly\Lib\Entities\Staff $staff
 */
?>
<form>
    <div>
        <?php echo Proxy\Pro::getAdvancedHtml( $staff, array(), false ) ?>
        <?php if ( $calendars ) Proxy\Pro::renderGoogleCalendarsList( $calendars, $selected_calendar_id ) ?>
    </div>
    <div class="text-right">
        <hr/>
        <span class="bookly-js-advanced-error text-danger"></span>
        <?php Buttons::renderSubmit( 'bookly-advanced-save' ) ?>
        <?php Buttons::renderReset() ?>
    </div>
</form>