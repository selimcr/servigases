<?php
namespace Tech506\Bundle\CallServiceBundle\Util\Enum;

use Tech506\Bundle\SecurityBundle\Util\Enum\BasicEnum;

/**
 * Enum with the list of status of a TechnicianService
 */
abstract class TechnicianServiceStatus extends BasicEnum {
    const CREATED = 10;
    const SCHEDULED = 20;
    const RE_SCHEDULE = 25;
    const CANCELED = 30;
    const REALIZED = 40;
    const LIQUIDADO = 45;
    const COMMISIONED = 50;
}
?>
