<?php
namespace BooklyCustomerGroups\Lib;

use Bookly\Lib;

/**
 * Class Updates
 * @package BooklyCustomerGroups\Lib
 */
class Updater extends Lib\Base\Updater
{
    public function update_2_8()
    {
        $this->alterTables( array(
            'bookly_customer_groups' => array(
                'ALTER TABLE `%s` ADD COLUMN `gateways` VARCHAR(255) DEFAULT NULL',
            ),
        ) );

        add_option( 'bookly_customer_groups_general_settings', array(
            'status' => get_option( 'bookly_appointment_default_status', 'approved' ),
            'skip_payment' => '0',
            'gateways' => null,
            'discount' => '0',
        ) );
    }

    public function update_2_6()
    {
        $this->alterTables( array(
            'bookly_customer_groups' => array(
                'ALTER TABLE `%s` ADD COLUMN `skip_payment` TINYINT(1) NOT NULL DEFAULT 0 AFTER `discount`',
            ),
        ) );

        $options = array(
            'bookly_l10n_info_complete_step_group_skip_payment' => __( 'Thank you! Your booking is complete. An email with details of your booking has been sent to you.', 'bookly' ),
        );
        $this->addL10nOptions( $options );
    }

    public function update_1_7()
    {
        $this->upgradeCharsetCollate( array(
            'bookly_customer_groups',
            'bookly_customer_groups_services',
        ) );
    }

    public function update_1_4()
    {
        global $wpdb;

        // Rename tables.
        $tables = array(
            'customer_groups',
            'customer_groups_services',
        );
        $query = 'RENAME TABLE ';
        foreach ( $tables as $table ) {
            $query .= sprintf( '`%s` TO `%s`, ', $this->getTableName( 'ab_' . $table ), $this->getTableName( 'bookly_' . $table ) );
        }
        $query = substr( $query, 0, -2 );
        $wpdb->query( $query );

        delete_option( 'bookly_customer_groups_enabled' );
    }
}