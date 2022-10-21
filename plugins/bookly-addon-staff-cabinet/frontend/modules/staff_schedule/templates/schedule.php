<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
/** @var Bookly\Backend\Components\Schedule\Component $schedule */
?>
<form>
    <?php $schedule->render() ?>
    <?php foreach ( $ss_ids as $id => $index ) : ?>
        <input type="hidden" name="ssi[<?php echo $id ?>]" value="<?php echo $index ?>" />
    <?php endforeach ?>

    <div class="text-right">
        <hr/>
        <?php Buttons::renderSubmit( 'bookly-schedule-save' ) ?>
        <?php Buttons::renderReset( 'bookly-schedule-reset', null, null, array(
            'data-default-breaks' => json_encode( $schedule->getBreaksArray() ),
            'data-spinner-color' => 'rgb(62, 66, 74)',
        ) ) ?>
    </div>
</form>