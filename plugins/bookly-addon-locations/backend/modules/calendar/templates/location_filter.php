<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * @var array $locations
 */
?>
<div class="col-sm-auto mb-2">
    <ul id="bookly-js-locations-filter"
        data-align="right"
        data-icon-class="far fa-flag"
        data-txt-select-all="<?php esc_attr_e( 'All locations', 'bookly' ) ?>"
        data-txt-all-selected="<?php esc_attr_e( 'All locations', 'bookly' ) ?>"
        data-txt-nothing-selected="<?php esc_attr_e( 'No locations selected', 'bookly' ) ?>"
    >
        <li data-value="no">
            <?php esc_html_e( 'W/o location', 'bookly' ) ?>
        </li>
        <?php foreach ( $locations as $location ): ?>
            <li data-value="<?php echo $location['id'] ?>">
                <?php echo esc_html( $location['name'] ) ?>
            </li>
        <?php endforeach ?>
    </ul>
</div>