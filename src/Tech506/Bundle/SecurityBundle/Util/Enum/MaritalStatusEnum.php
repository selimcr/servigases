<?php
namespace Tech506\Bundle\SecurityBundle\Util\Enum;

/**
 * Enum with the list of Marital Status of a Person
 */
abstract class MaritalStatusEnum extends BasicEnum {
    const SINGLE = 1;
    const FREE_UNION = 2;
    const MARRIED = 3;
    const DIVORCED = 4;
    const WIDOWED = 5;
}
?>
