<?php
namespace BooklyServiceExtras\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Installer
 * @package BooklyServiceExtras\Lib
 */
class Installer extends Base\Installer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = array(
            'bookly_service_extras_enabled'       => '1',
            'bookly_service_extras_multiply_nop'  => '1',
            'bookly_service_extras_show'          => array( 'title', 'price' ),
            'bookly_service_extras_show_in_cart'  => '1',
            'bookly_service_extras_after_step_time' => '0',
            'bookly_l10n_step_extras'             => __( 'Extras', 'bookly' ),
            'bookly_l10n_step_extras_button_next' => __( 'Next', 'bookly' ),
            'bookly_l10n_info_extras_step'        => __( 'Select the Extras you\'d like (Multiple Selection)', 'bookly' ),
        );
    }

    /**
     * Create tables in database.
     */
    public function createTables()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        $charset_collate = $wpdb->has_cap( 'collation' )
            ? $wpdb->get_charset_collate()
            : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\ServiceExtra::getTableName() . '` (
                `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `service_id`    INT UNSIGNED NOT NULL,
                `attachment_id` INT UNSIGNED DEFAULT NULL,
                `title`         VARCHAR(255) DEFAULT "",
                `duration`      INT NOT NULL DEFAULT 0,
                `price`         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `min_quantity`  INT NOT NULL DEFAULT 0,
                `max_quantity`  INT NOT NULL DEFAULT 1,
                `position`      INT NOT NULL DEFAULT 9999,
                CONSTRAINT
                    FOREIGN KEY (service_id)
                    REFERENCES ' . BooklyLib\Entities\Service::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            ' . $charset_collate
        );
    }

}