<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_MeigeewidgetsHarbour_Model_Templates
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'meigee/meigeewidgetsharbour/grid.phtml', 'label'=>'Grid'),
			array('value'=>'meigee/meigeewidgetsharbour/masonry_grid.phtml', 'label'=>'Masonry Grid'),
            array('value'=>'meigee/meigeewidgetsharbour/list.phtml', 'label'=>'List'),
			array('value'=>'meigee/meigeewidgetsharbour/footer_list.phtml', 'label'=>'Footer List'),
            array('value'=>'meigee/meigeewidgetsharbour/slider.phtml', 'label'=>'Slider')
        );
    }

}