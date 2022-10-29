<?php
namespace BooklyStaffCabinet\Frontend\Modules\Calendar;

use Bookly\Lib as BooklyLib;

/**
 * Class Ajax
 * @package BooklyStaffCabinet\Frontend\Modules\Calendar
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array(
            'exportAppointments' => 'staff',
        );
    }

    /**
     * Export appointments
     */
    public static function exportAppointments()
    {
        $delimiter   = self::parameter( 'delimiter', ',' );
        $start_date  = self::parameter( 'start_date', current_time( 'Y-m-d 00:00:00' ) );
        $end_date    = self::parameter( 'end_date', current_time( 'Y-m-d 23:59:59' ) );
        $export_type = self::parameter( 'export_type', 'all' );

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=Appointments.csv' );

        $titles = array(
            'start_date'       => __( 'Booking Time', 'bookly' ),
            'customer_name'    => __( 'Customer Name', 'bookly' ),
            'customer_phone'   => __( 'Customer Phone', 'bookly' ),
            'customer_email'   => __( 'Customer Email', 'bookly' ),
            'service_title'    => __( 'Service', 'bookly' ),
            'service_duration' => __( 'Duration', 'bookly' ),
            'status'           => __( 'Status', 'bookly' ),
            'payment'          => __( 'Payment', 'bookly' ),
            'notes'            => BooklyLib\Utils\Common::getTranslatedOption( 'bookly_l10n_label_notes' ),
        );
        $header = array();
        $column = array();
        $cf_ids = array();

        foreach ( self::parameter( 'exp' ) as $key => $value ) {
            $header[] = $titles[ $key ];
            $column[] = $key;
        }

        $cf_titles = array();
        foreach ( (array) BooklyLib\Proxy\CustomFields::getTranslated() as $field ) {
            if ( ! in_array( $field->type, array( 'captcha', 'text-content' ) ) ) {
                $cf_titles[ $field->id ] = $field->label;
            }
        }

        foreach ( self::parameter( 'custom-field' ) as $id => $value ) {
            $header[] = $cf_titles[ $id ];
            $cf_ids[] = $id;
        }

        $query = BooklyLib\Entities\Appointment::query( 'a' )
            ->select( 'a.id,
                a.start_date,
                a.staff_any,
                ca.id        AS ca_id,
                ca.payment_id,
                ca.status,
                ca.notes,
                ca.extras,
                c.full_name  AS customer_name,
                c.phone      AS customer_phone,
                c.email      AS customer_email,
                p.paid       AS payment,
                p.total      AS payment_total,
                p.type       AS payment_type,
                p.status     AS payment_status,
                COALESCE(s.title, a.custom_service_name) AS service_title,
                (TIME_TO_SEC(TIMEDIFF(a.end_date, a.start_date)) + a.extras_duration) AS service_duration' )
            ->leftJoin( 'CustomerAppointment', 'ca', 'a.id = ca.appointment_id' )
            ->leftJoin( 'Service', 's', 's.id = a.service_id' )
            ->leftJoin( 'Customer', 'c', 'c.id = ca.customer_id' )
            ->leftJoin( 'Payment', 'p', 'p.id = ca.payment_id' )
            ->leftJoin( 'Staff', 'st', 'st.id = a.staff_id' )
            ->where( 'st.wp_user_id', get_current_user_id() )
            ->sortBy( 'a.start_date' );

        switch ( $export_type ) {
            case 'displayed':
                $query->whereBetween( 'a.start_date', $start_date, $end_date );
                break;
            case 'upcoming':
                $query->whereGte( 'a.start_date', current_time( 'Y-m-d 00:00:00' ) );
                break;
        }

        $output = fopen( 'php://output', 'w' );
        fwrite( $output, pack( 'CCC', 0xef, 0xbb, 0xbf ) );
        fputcsv( $output, $header, $delimiter );

        foreach ( $query->fetchArray() as $row ) {
            // Service duration.
            $row['service_duration'] = BooklyLib\Utils\DateTime::secondsToInterval( $row['service_duration'] );
            // Appointment status.
            $row['status'] = BooklyLib\Entities\CustomerAppointment::statusToString( $row['status'] );
            // Payment title.
            $payment_title = '';
            if ( $row['payment'] !== null ) {
                $payment_title = BooklyLib\Utils\Price::format( $row['payment'] );
                if ( $row['payment'] != $row['payment_total'] ) {
                    $payment_title = sprintf( __( '%s of %s', 'bookly' ), $payment_title, BooklyLib\Utils\Price::format( $row['payment_total'] ) );
                }
                $payment_title .= sprintf(
                    ' %s %s',
                    BooklyLib\Entities\Payment::typeToString( $row['payment_type'] ),
                    BooklyLib\Entities\Payment::statusToString( $row['payment_status'] )
                );
            }
            $row['payment'] = $payment_title;

            $row_data = array_fill( 0, count( $header ), '' );
            foreach ( $row as $key => $value ) {
                $pos = array_search( $key, $column );
                if ( $pos !== false ) {
                    $row_data[ $pos ] = $value;
                }
            }

            // Custom fields
            $customer_appointment = new BooklyLib\Entities\CustomerAppointment();
            $customer_appointment->load( $row['ca_id'] );
            foreach ( (array) BooklyLib\Proxy\CustomFields::getForCustomerAppointment( $customer_appointment ) as $custom_field ) {
                $column_number = array_search( $custom_field['id'], $cf_ids );
                if ( $column_number !== false ) {
                    $row_data[ count( $column ) + $column_number ] = $custom_field['value'];
                }
            }

            fputcsv( $output, $row_data, $delimiter );
        }

        fclose( $output );

        exit;
    }

}