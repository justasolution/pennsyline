<?php
namespace BooklyLocations\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyLocations\Lib;
use BooklyLocations\Backend\Modules\Locations\Page;

/**
 * Class Local
 *
 * @package BooklyLocations\Lib\ProxyProviders
 */
abstract class Local extends BooklyLib\Proxy\Locations
{
    /**
     * @inheritDoc
     */
    public static function addBooklyMenuItem()
    {
        add_submenu_page(
            'bookly-menu',
            __( 'Locations', 'bookly' ),
            __( 'Locations', 'bookly' ),
            BooklyLib\Utils\Common::getRequiredCapability(),
            Page::pageSlug(),
            function() { Page::render(); }
        );
    }

    /**
     * @inheritDoc
     */
    public static function findById( $location_id )
    {
        return Lib\Entities\Location::find( $location_id );
    }

    /**
     * @inheritDoc
     */
    public static function findByStaffId( $staff_id )
    {
        return Lib\Entities\Location::query( 'l' )
            ->select( 'l.*' )
            ->leftJoin( 'StaffLocation', 'sl', 'sl.location_id = l.id' )
            ->where( 'sl.staff_id', $staff_id )
            ->find();
    }

    /**
     * @inheritDoc
     */
    public static function servicesPerLocationAllowed()
    {
        return get_option( 'bookly_locations_allow_services_per_location' );
    }

    /**
     * @inheritDoc
     */
    public static function addServices( BooklyLib\Slots\Staff $staff, $staff_id, $service_id )
    {
        if ( ! self::servicesPerLocationAllowed() ) {
            $key = 'staff-locations-' . $staff_id;
            if ( ! self::hasInCache( $key ) ) {
                self::putInCache( $key, Lib\Entities\StaffLocation::query()->where( 'staff_id', $staff_id )->fetchCol( 'location_id' ) );
            }
            $service = $staff->getService( $service_id, 0 );
            foreach ( self::getFromCache( $key ) as $location_id ) {
                $staff->addService(
                    $service_id,
                    $location_id,
                    $service->price(),
                    $service->capacityMin(),
                    $service->capacityMax(),
                    $service->getStaffPreferenceRule(),
                    $service->getStaffPreferenceSettings(),
                    $service->getStaffPreferenceOrder()
                );
            }
        }

        return $staff;
    }

    /**
     * @inheritDoc
     */
    public static function prepareStaffLocationId( $location_id, $staff_id )
    {
        $key = 'staff_location_' . $staff_id . '_' . $location_id;
        if ( ! self::hasInCache( $key ) ) {
            if ( ! self::servicesPerLocationAllowed() ) {
                self::putInCache( $key, 0 );
            } else {
                $custom_services = Lib\Entities\StaffLocation::query()
                    ->where( 'staff_id', $staff_id )
                    ->where( 'location_id', $location_id )
                    ->fetchVar( 'custom_services' );

                if ( $custom_services ) {
                    self::putInCache( $key, $location_id );
                } else {
                    self::putInCache( $key, 0 );
                }
            }
        }

        return self::getFromCache( $key );
    }

    /**
     * @inheritDoc
     */
    public static function prepareStaffScheduleLocationId( $location_id, $staff_id )
    {
        $key = 'staff_location_' . $staff_id . '_' . $location_id;
        if ( ! self::hasInCache( $key ) ) {
            if ( ! self::servicesPerLocationAllowed() ) {
                self::putInCache( $key, 0 );
            } else {
                $custom_schedule = Lib\Entities\StaffLocation::query()
                    ->where( 'staff_id', $staff_id )
                    ->where( 'location_id', $location_id )
                    ->fetchVar( 'custom_schedule' );

                if ( $custom_schedule ) {
                    self::putInCache( $key, $location_id );
                } else {
                    self::putInCache( $key, 0 );
                }
            }
        }

        return self::getFromCache( $key );
    }

    /**
     * @inheritDoc
     */
    public static function prepareStaffSpecialDaysLocationId( $location_id, $staff_id )
    {
        if ( ! self::servicesPerLocationAllowed() ) {
            return 0;
        }
        $key = 'staff_special_days_' . $staff_id . '_' . $location_id;
        if ( ! self::hasInCache( $key ) ) {
            if ( ! self::servicesPerLocationAllowed() ) {
                self::putInCache( $key, 0 );
            } else {
                $custom_special_days = Lib\Entities\StaffLocation::query()
                    ->where( 'staff_id', $staff_id )
                    ->where( 'location_id', $location_id )
                    ->fetchVar( 'custom_special_days' );

                if ( $custom_special_days ) {
                    self::putInCache( $key, $location_id );
                } else {
                    self::putInCache( $key, 0 );
                }
            }
        }

        return self::getFromCache( $key );
    }

    /**
     * @inheritDoc
     */
    public static function prepareAppointmentsQuery( $query )
    {
        $query
            ->addSelect( 'l.name AS location' )
            ->leftJoin( 'Location', 'l', 'l.id = a.location_id', '\BooklyLocations\Lib\Entities' );
    }
    
    /**
     * @inheritDoc
     */
    public static function prepareStaffServiceQuery( $query )
    {
        if ( self::servicesPerLocationAllowed() ) {
            $query
                ->addSelect( 'sl.location_id' )
                ->leftJoin( 'StaffLocation', 'sl', 'sl.staff_id = ss.staff_id', '\BooklyLocations\Lib\Entities' )
                ->whereRaw( '( ss.location_id IS NULL AND sl.custom_services = 0 ) OR ( ss.location_id IS NOT NULL AND sl.custom_services = 1 AND sl.location_id = ss.location_id )', array() );
        }

        return $query;
    }

    /**
     * @inheritDoc
     */
    public static function prepareWorkingSchedule( $working_schedule, $staff_ids )
    {
        if ( self::servicesPerLocationAllowed() ) {
            $working_schedule = Lib\Entities\StaffLocation::query( 'sl' )
                ->select( 'ssi.id, ssi.staff_id, sl.location_id, ssi.day_index, ssi.start_time, ssi.end_time, break.start_time AS break_start, break.end_time AS break_end' )
                ->leftJoin( 'StaffScheduleItem', 'ssi', 'ssi.staff_id=sl.staff_id AND IF(sl.custom_schedule = 1, ssi.location_id = sl.location_id, ssi.location_id IS NULL)', '\Bookly\Lib\Entities' )
                ->leftJoin( 'ScheduleItemBreak', 'break', 'break.staff_schedule_item_id = ssi.id', '\Bookly\Lib\Entities' )
                ->whereIn( 'staff_id', $staff_ids )
                ->whereNot( 'ssi.start_time', null );
        }

        return $working_schedule;
    }

    /**
     * @inheritDoc
     */
    public static function prepareSpecialDays( $special_days, $staff_ids, $start_date, $end_date )
    {
        if ( self::servicesPerLocationAllowed() ) {
            $special_days = Lib\Entities\StaffLocation::query( 'sl' )
                ->select( 'ssd.staff_id, sl.location_id, ssd.date, ssd.start_time, ssd.end_time, break.start_time AS break_start, break.end_time AS break_end' )
                ->leftJoin( 'StaffSpecialDay', 'ssd', 'ssd.staff_id=sl.staff_id AND IF(sl.custom_special_days = 1, ssd.location_id = sl.location_id, ssd.location_id IS NULL)', '\BooklySpecialDays\Lib\Entities' )
                ->leftJoin( 'StaffSpecialDayBreak', 'break', 'break.staff_special_day_id = ssd.id', '\BooklySpecialDays\Lib\Entities' )
                ->whereIn( 'ssd.staff_id', $staff_ids )
                ->whereNot( 'ssd.start_time', null )
                ->whereGte( 'ssd.date', $start_date->format( 'Y-m-d' ) )
                ->whereLte( 'ssd.date', $end_date->format( 'Y-m-d' ) );
        }

        return $special_days;
    }

    /**
     * @inheritDoc
     */
    public static function prepareLocationsForCombinedServices( $locations, $services )
    {
        if ( self::servicesPerLocationAllowed() ) {
            $key = __FUNCTION__ . '-' . implode( '-', $services );
            if ( ! self::hasInCache( $key ) ) {
                $query = BooklyLib\Entities\StaffService::query( 'ss' )
                    ->leftJoin( 'Service', 's', 's.id = ss.service_id', '\Bookly\Lib\Entities' )
                    ->whereIn( 'ss.service_id', $services )
                    ->groupBy( 'ss.service_id, sl.location_id' );
                $query = BooklyLib\Proxy\Shared::prepareStaffServiceQuery( $query );
                self::putInCache( $key, $query->fetchCol( 'sl.location_id' ) );
            }
            foreach ( array_count_values( self::getFromCache( $key ) ) as $value => $count ) {
                if ( $count == count( $services ) ) {
                    $locations[] = $value;
                }
            }
        }

        return $locations;
    }
}