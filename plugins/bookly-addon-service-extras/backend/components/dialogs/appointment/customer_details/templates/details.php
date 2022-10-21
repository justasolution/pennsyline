<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** @var BooklyServiceExtras\Lib\Entities\ServiceExtra[] $extras */
use Bookly\Lib\Utils\Price;
?>
<h5 class="text-muted"><?php esc_html_e( 'Extras', 'bookly' ) ?></h5>
<div id="bookly-extras">
    <?php foreach ( $extras as $extra ) : ?>
        <div class="row mb-2 service_<?php echo $extra->getServiceId() ?>">
            <div class="col-sm-3 pr-1" style="width:5em">
                <input class="bookly-js-extras-count form-control" data-id="<?php echo $extra->getId() ?>" type="number" min="0" name="extra[<?php echo $extra->getId() ?>]" value="0" />
            </div>
            <div class="col mt-2 pl-0">
                <span class="bookly-js-nop-wrap collapse">&nbsp;&times; <i class="far fa-fw fa-user mr-1"></i><span class="bookly-js-nop"></span></span>&nbsp;&times; <b><?php echo $extra->getTitle() ?></b> (<?php echo Price::format( $extra->getPrice() ) ?>)
            </div>
        </div>
    <?php endforeach ?>
</div>