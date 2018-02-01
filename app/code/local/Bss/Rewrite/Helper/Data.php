<?php
class Bss_Rewrite_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function orderBy($data, $field)
    {
        $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
        usort($data, create_function('$a,$b', $code));
        return $data;
    }
}
	 