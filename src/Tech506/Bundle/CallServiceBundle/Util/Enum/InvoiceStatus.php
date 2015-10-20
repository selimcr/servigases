<?php
namespace Tech506\Bundle\CallServiceBundle\Util\Enum;

use Tech506\Bundle\SecurityBundle\Util\Enum\BasicEnum;

/**
 * Enum with the list of status of an Invoice
 */
abstract class InvoiceStatus extends BasicEnum {
    const CREATED = 1;
    const FINISHED = 99;
}
?>
