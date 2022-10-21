<?php
namespace BooklyCustomerInformation\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Installer
 * @package BooklyCustomerInformation\Lib
 */
class Installer extends Base\Installer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = array(
            'bookly_customer_information_enabled' => '1',
            'bookly_customer_information_data'    => '[]',
        );
    }
}