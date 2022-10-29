<?php
namespace BooklyFiles\Lib;

use Bookly\Lib;

/**
 * Class Updates
 * @package BooklyFiles\Lib
 */
class Updater extends Lib\Base\Updater
{
    public function update_3_1()
    {
        add_option( 'bookly_files_extensions', '' );
        $this->addL10nOptions( array(
            'bookly_l10n_incorrect_file_type' => __( 'File\'s extension is not allowed', 'bookly' ),
        ) );
    }

    public function update_1_8()
    {
        $this->upgradeCharsetCollate( array(
            'bookly_customer_appointment_files',
            'bookly_files',
        ) );
    }

    public function update_1_3()
    {
        /** @global \wpdb $wpdb */
        global $wpdb;

        // Rename tables.
        $tables = array(
            'customer_appointment_files',
            'files',
        );
        $query = 'RENAME TABLE ';
        foreach ( $tables as $table ) {
            $query .= sprintf( '`%s` TO `%s`, ', $this->getTableName( 'ab_' . $table ), $this->getTableName( 'bookly_' . $table ) );
        }
        $query = substr( $query, 0, -2 );
        $wpdb->query( $query );
    }

    public function update_1_1()
    {
        $this->alterTables( array(
            'ab_files' => array(
                'ALTER TABLE `%s` ADD COLUMN `custom_field_id` INT NULL',
            ),
        ) );
    }
}