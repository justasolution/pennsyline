<?php
namespace BooklyServiceExtras\Lib\Entities;

use Bookly\Lib;

/**
 * Class ServiceExtra
 *
 * @package BooklyServiceExtras\Lib\Entities
 */
class ServiceExtra extends Lib\Base\Entity
{
    /** @var int */
    protected $service_id;
    /** @var int */
    protected $attachment_id;
    /** @var string */
    protected $title;
    /** @var int */
    protected $duration = 900;
    /** @var float */
    protected $price = 0;
    /** @var int */
    protected $min_quantity = 0;
    /** @var int */
    protected $max_quantity = 1;
    /** @var int */
    protected $position;

    protected static $table = 'bookly_service_extras';

    protected static $schema = array(
        'id'            => array( 'format' => '%d' ),
        'service_id'    => array( 'format' => '%d', 'reference' => array( 'entity' => 'Service', 'namespace' => '\Bookly\Lib\Entities' ) ),
        'attachment_id' => array( 'format' => '%d' ),
        'title'         => array( 'format' => '%s' ),
        'duration'      => array( 'format' => '%d' ),
        'price'         => array( 'format' => '%f' ),
        'min_quantity'  => array( 'format' => '%d' ),
        'max_quantity'  => array( 'format' => '%d' ),
        'position'      => array( 'format' => '%d', 'sequent' => true ),
    );

    /**
     * Get title (if empty returns "Untitled").
     *
     * @param string $locale
     * @return string
     */
    public function getTranslatedTitle( $locale = null )
    {
        return Lib\Utils\Common::getTranslatedString( 'service_extra_' . $this->getId(), $this->getTitle() != '' ? $this->getTitle() : __( 'Untitled', 'bookly' ), $locale );
    }

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets service_id
     *
     * @return int
     */
    public function getServiceId()
    {
        return $this->service_id;
    }

    /**
     * Sets service_id
     *
     * @param Lib\Entities\Service $service
     * @return $this
     */
    public function setService( Lib\Entities\Service $service )
    {
        return $this->setServiceId( $service->getId() );
    }

    /**
     * Sets service_id
     *
     * @param int $service_id
     * @return $this
     */
    public function setServiceId( $service_id )
    {
        $this->service_id = $service_id;

        return $this;
    }

    /**
     * Gets attachment_id
     *
     * @return int
     */
    public function getAttachmentId()
    {
        return $this->attachment_id;
    }

    /**
     * Sets attachment_id
     *
     * @param int $attachment_id
     * @return $this
     */
    public function setAttachmentId( $attachment_id )
    {
        $this->attachment_id = $attachment_id;

        return $this;
    }

    /**
     * Gets title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle( $title )
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Sets duration
     *
     * @param int $duration
     * @return $this
     */
    public function setDuration( $duration )
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Gets price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice( $price )
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Gets min_quantity
     *
     * @return int
     */
    public function getMinQuantity()
    {
        return $this->min_quantity;
    }

    /**
     * Sets min_quantity
     *
     * @param int $min_quantity
     * @return $this
     */
    public function setMinQuantity( $min_quantity )
    {
        $this->min_quantity = $min_quantity;

        return $this;
    }

    /**
     * Gets max_quantity
     *
     * @return int
     */
    public function getMaxQuantity()
    {
        return $this->max_quantity;
    }

    /**
     * Sets max_quantity
     *
     * @param int $max_quantity
     * @return $this
     */
    public function setMaxQuantity( $max_quantity )
    {
        $this->max_quantity = $max_quantity;

        return $this;
    }

    /**
     * Gets position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition( $position )
    {
        $this->position = $position;

        return $this;
    }

    /**************************************************************************
     * Overridden Methods                                                     *
     **************************************************************************/

    public function save()
    {
        $return = parent::save();
        if ( $this->isLoaded() ) {
            // Register string for translate in WPML.
            do_action( 'wpml_register_single_string', 'bookly', 'service_extra_' . $this->getId(), $this->getTitle() );
        }

        return $return;
    }

}