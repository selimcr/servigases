<?php
namespace Tech506\Bundle\CallServiceBundle\Util\Enum;

use Tech506\Bundle\SecurityBundle\Util\Enum\BasicEnum;

/**
 * Enum with the list of roles availables in the system
 */
abstract class Role extends BasicEnum {
    const ADMIN = 'ROLE_SELLER';
    const SELLER = 'ROLE_SELLER';
    const TECHNICIAN = 'ROLE_TECHNICIAN';
    const EMPLOYEE = 'ROLE_EMPLOYEE'; // Both Seller and Technician are Employee
}
?>
