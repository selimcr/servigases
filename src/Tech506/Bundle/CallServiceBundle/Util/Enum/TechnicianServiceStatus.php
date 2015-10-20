<?php
namespace Tech506\Bundle\CallServiceBundle\Util\Enum;

use Tech506\Bundle\SecurityBundle\Util\Enum\BasicEnum;

/**
 * Enum with the list of status of a TechnicianService
 */
abstract class TechnicianServiceStatus extends BasicEnum {
    const CREATED = 1;
    const SCHEDULED = 2;
    const CANCELED = 3;
    const REALIZED = 4;
    const COMMISIONED = 5;
}
?>
