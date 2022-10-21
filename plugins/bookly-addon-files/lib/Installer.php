<?php
namespace BooklyFiles\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Installer
 * @package BooklyFiles\Lib
 */
class Installer extends Base\Installer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = array(
            'bookly_files_enabled'   => get_option( 'bookly_custom_fields_enabled' ) ? '1' : '0',
            'bookly_files_directory' => '',
            'bookly_l10n_browse'     => __( 'Browse', 'bookly' ),
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
            'CREATE TABLE IF NOT EXISTS `' . Entities\Files::getTableName() . '` (
                `id`   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` TEXT NOT NULL,
                `slug` VARCHAR(32) NOT NULL,
                `path` TEXT NOT NULL,
                `custom_field_id` INT NULL
             ) ENGINE = INNODB
             ' . $charset_collate
        );

        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . Entities\CustomerAppointmentFiles::getTableName() . '` (
                `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `customer_appointment_id` INT UNSIGNED NOT NULL,
                `file_id`  INT UNSIGNED NOT NULL,
                CONSTRAINT
                    FOREIGN KEY (customer_appointment_id)
                    REFERENCES ' . BooklyLib\Entities\CustomerAppointment::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT
                    FOREIGN KEY (file_id)
                    REFERENCES ' . Entities\Files::getTableName() . '(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE = INNODB
            ' . $charset_collate
        );
    }

}