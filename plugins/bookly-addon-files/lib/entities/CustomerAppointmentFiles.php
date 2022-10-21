<?php
namespace BooklyFiles\Lib\Entities;

use Bookly\Lib as BooklyLib;

/**
 * Class Files
 * @package BooklyFiles\Lib\Entities
 */
class CustomerAppointmentFiles extends BooklyLib\Base\Entity
{
    protected static $table = 'bookly_customer_appointment_files';

    /** @var  int */
    protected $customer_appointment_id;
    /** @var  int */
    protected $file_id;

    protected static $schema = array(
        'id'      => array( 'format' => '%d' ),
        'customer_appointment_id'    => array( 'format' => '%d', 'reference' => array( 'entity' => 'CustomerAppointment', 'namespace' => '\Bookly\Lib\Entities' ) ),
        'file_id' => array( 'format' => '%d', 'reference' => array( 'entity' => 'Files' ) ),
    );

    /**
     * Detach file from Customer Appointment and unlink file.
     */
    public function deleteCascade()
    {
        if ( $this->isLoaded() ) {
            $fs = BooklyLib\Utils\Common::getFilesystem();
            /** @var CustomerAppointmentFiles[] $caf_list */
            $caf_list = self::query()
                ->where( 'file_id', $this->file_id )
                ->find();
            $delete_file = true;
            foreach ( $caf_list as $caf ) {
                if ( $caf->getCustomerAppointmentId() == $this->customer_appointment_id ) {
                    $caf->delete();
                } else {
                    $delete_file = false;
                }
            }
            if ( $delete_file && ( $file = Files::find( $this->file_id ) ) ) {
                $fs->delete( $file->getPath(), false, 'f' );
                $file->delete();
            }
        }
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets customer_appointment_id
     *
     * @return int
     */
    public function getCustomerAppointmentId()
    {
        return $this->customer_appointment_id;
    }

    /**
     * Sets customer_appointment_id
     *
     * @param int $customer_appointment_id
     * @return $this
     */
    public function setCustomerAppointmentId( $customer_appointment_id )
    {
        $this->customer_appointment_id = $customer_appointment_id;

        return $this;
    }

    /**
     * Gets file_id
     *
     * @return int
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * Sets file_id
     *
     * @param int $file_id
     * @return $this
     */
    public function setFileId( $file_id )
    {
        $this->file_id = $file_id;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

}