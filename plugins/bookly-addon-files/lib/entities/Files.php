<?php
namespace BooklyFiles\Lib\Entities;

use Bookly\Lib AS BooklyLib;

/**
 * Class Files
 * @package BooklyFiles\Lib\Entities
 */
class Files extends BooklyLib\Base\Entity
{
    protected static $table = 'bookly_files';

    /** @var  string */
    protected $name;
    /** @var  string */
    protected $slug;
    /** @var  string */
    protected $path;
    /** @var  int */
    protected $custom_field_id;

    protected static $schema = array(
        'id'   => array( 'format' => '%d' ),
        'name' => array( 'format' => '%s' ),
        'slug' => array( 'format' => '%s' ),
        'path' => array( 'format' => '%s' ),
        'custom_field_id' => array( 'format' => '%d' ),
    );

    /**
     * Safely delete file
     *
     * @param string $ca_id
     */
    public function deleteSafely( $ca_id = null )
    {
        $files = CustomerAppointmentFiles::query()
            ->where( 'file_id', $this->getId() )
            ->fetchCol( 'customer_appointment_id' );
        $delete = count( $files ) == 0;
        if ( ! $delete && $ca_id ) {
            $delete = $files === array( $ca_id );
        }
        if ( $delete ) {
            BooklyLib\Utils\Common::getFilesystem()->delete( $this->getPath(), 'false', 'f' );
            $this->delete();
        }
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets name
     *
     * @param string $name
     * @return $this
     */
    public function setName( $name )
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets slug
     *
     * @param string $slug
     * @return $this
     */
    public function setSlug( $slug )
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Gets path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets path
     *
     * @param string $path
     * @return $this
     */
    public function setPath( $path )
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Gets custom_field_id
     *
     * @return int
     */
    public function getCustomFieldId()
    {
        return $this->custom_field_id;
    }

    /**
     * Sets custom_field_id
     *
     * @param int $custom_field_id
     * @return $this
     */
    public function setCustomFieldId( $custom_field_id )
    {
        $this->custom_field_id = $custom_field_id;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

}