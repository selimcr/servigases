<?php
namespace Tech506\Bundle\SecurityBundle\Util\Enum;

/**
 * Enum with the list of roles
 */
abstract class RolesEnum extends BasicEnum {
    const ADMINISTRATOR = "ROLE_ADMIN";
    const TECHNICIAN = "ROLE_TECHNICIAN";
    const SELLER = "ROLE_SELLER";
}
?>
