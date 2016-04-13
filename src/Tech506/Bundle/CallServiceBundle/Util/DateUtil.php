<?php
namespace Tech506\Bundle\CallServiceBundle\Util;

/**
 *
 */
class DateUtil {

    public static function getDateValueFromUI($value) {
        $date = null;
        if(isset($value)) {
            $value = trim($value);
            if ($value != "") {
                $date = \DateTime::createFromFormat('d/m/Y', $value);
            }
        }
        return $date;
    }
}
?>
