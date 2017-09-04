<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_MeigeewidgetsHarbour_Model_Brandsview
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'list', 'label'=>'Simple List'),
            array('value'=>'slider', 'label'=>'Slider')
        );
    }

}