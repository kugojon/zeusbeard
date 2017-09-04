<?php
class Bss_Robots_Model_Observer
{

			public function addMetaCategory(Varien_Event_Observer $observer)
			{
				$controller = $observer->getAction();
                $fullActionName = $controller->getFullActionName();
                if ($fullActionName == 'catalog_category_view' || $fullActionName == 'cms_index_index') { 
                    $dir =  $_GET['dir']; 
                    $limit = $_GET['limit']; 
                    $sid = $_GET['SID'];          
                    if ($dir || $limit || $sid) { 
                          $observer->getLayout()->getBlock('head')->setRobots('NOINDEX,FOLLOW');
                      }
                }
			}
		
}
