<?php
namespace BooklyServiceExtras\Backend\Components\Dialogs\Service\Edit\Forms;

/**
 * Class ServiceExtra
 * @package BooklyServiceExtras\Backend\Components\Dialogs\Service\Edit\Forms
 */
class ServiceExtra extends \Bookly\Lib\Base\Form
{
    protected static $entity_class = 'ServiceExtra';

    protected static $namespace = '\BooklyServiceExtras\Lib\Entities';

    public function configure()
    {
        $this->setFields( array( 'id', 'service_id', 'attachment_id', 'title', 'duration', 'price', 'min_quantity', 'max_quantity' ) );
    }

}