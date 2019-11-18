<?php
class Bss_RwCategoriesEnhanced_Block_Html_Topmenu extends Mage_Page_Block_Html_Topmenu
{
    protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $catId = explode("category-node-", $child->getId());
            $categoryComplete = Mage::getModel('catalog/category')->load($catId[1]);

            $catLabel = '';
            if ($categoryComplete->getMeigeeCatCustomlabel()) {
                $catLabel = '<em class="category-label ' . $categoryComplete->getMeigeeCatCustomlabel() . '">' . $categoryComplete->getMeigeeCatLabeltext() . '</em>';
            }

            if(Mage::getStoreConfig('meigee_categoriesenhanced/options/status') and ($categoryComplete->getMeigeeCatMenutype() != 1) ){
                $childrenWrapClass = 'menu-wrapper row-cms';

                if ($categoryComplete->getMeigeeCatCustomlink()) {
                    if ($categoryComplete->getMeigeeCatCustomlink() == '/') {
                        $itemUrl = Mage::getBaseUrl();
                    }
                    elseif ($categoryComplete->getMeigeeCatCustomlink() == '#') {
                        $itemUrl = '#';
                    }
                    else $itemUrl = $categoryComplete->getMeigeeCatCustomlink();
                }
                else $itemUrl = $child->getUrl();

                // Get ratio value
                $ratio = explode("/", $categoryComplete->getMeigeeCatRatio());

                $child->setLevel($childLevel);
                $child->setIsFirst($counter == 1);
                $child->setIsLast($counter == $childrenCount);
                $child->setPositionClass($itemPositionClassPrefix . $counter);

                $outermostClassCode = '';
                $outermostClass = $menuTree->getOutermostClass();

                if ($childLevel == 0 && $outermostClass) {
                    $outermostClassCode = ' class="' . $outermostClass . '" ';
                    $child->setClass($outermostClass);
                }

                if ($childLevel == 1) {
                    $html .= '<li class="level1">';
                }
                else {
                    $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
                }

                $subTitle = '';
                if ($childLevel == 1) {
                    $subTitle = ' class="subtitle"';
                }
                if ($categoryComplete->getMeigeeCatBlockTop() && $childLevel > 0) {
                    $html .= '<div class="top-content">' . $this->helper('cms')->getBlockTemplateProcessor()->filter($categoryComplete->getMeigeeCatBlockTop()) . '</div><div class="clear"></div>';
                }

                if (!$categoryComplete->getMeigeeCatSubcontent()) {
                    $html .= '<a href="' . $itemUrl . '" ' . $outermostClassCode . '><span' . $subTitle . '>'
                        . $this->escapeHtml($child->getName()) . '</span>' . $catLabel . '</a>';
                }
                elseif ($categoryComplete->getMeigeeCatSubcontent() && $childLevel == 0) {
                    $html .= '<a href="' . $itemUrl . '" ' . $outermostClassCode . '><span' . $subTitle . '>'
                        . $this->escapeHtml($child->getName()) . '</span>' . $catLabel . '</a>';
                }

                if ($child->hasChildren()) {
                    if (!empty($childrenWrapClass) && $childLevel == 0) {
                        if($categoryComplete->getMeigeeCatMaxQuantity() and is_numeric($categoryComplete->getMeigeeCatMaxQuantity())){
                            $columnsCount = $categoryComplete->getMeigeeCatMaxQuantity();
                        }else{
                            $columnsCount = Mage::getStoreConfig('meigee_categoriesenhanced/options/column_count');
                        }
                        $columnsCount = ' columns="'.$columnsCount.'"';
                        $html .= '<div class="' . $childrenWrapClass . '"'.$columnsCount.'>';
                    }
                    if ($categoryComplete->getMeigeeCatSubcontent()) {
                        $html .= '<div class="col-12 sub-content">' . $this->helper('cms')->getBlockTemplateProcessor()->filter($categoryComplete->getMeigeeCatSubcontent()) . '</div>';
                    }
                    else {
                        if ($categoryComplete->getMeigeeCatBlockTop() && $childLevel == 0) {
                            $html .= '<div class="top-content">' . $this->helper('cms')->getBlockTemplateProcessor()->filter($categoryComplete->getMeigeeCatBlockTop()) . '</div><div class="clear"></div>';
                        }
                        if ($categoryComplete->getMeigeeCatBlockRight()) {
                            $html .= '<div class="col-'. $ratio[0] .' alpha">';
                        }
                        $html .= '<ul class="level' . $childLevel . '">';
                        $html .= $this->_getHtml($child, $childrenWrapClass);
                        $html .= '</ul>';

                        if (!empty($childrenWrapClass) && $childLevel == 0) {
                            if ($categoryComplete->getMeigeeCatBlockRight()) {
                                $html .= '</div>';
                                $html .= '<div class="col-'. $ratio[1] .' omega right-content">' . $this->helper('cms')->getBlockTemplateProcessor()->filter($categoryComplete->getMeigeeCatBlockRight()) . '</div>';
                            }
                            $html .= '<div class="clear"></div>';
                        }
                        if ($categoryComplete->getMeigeeCatBlockBottom()) {
                            $html .= '<div class="bottom-content">' . $this->helper('cms')->getBlockTemplateProcessor()->filter($categoryComplete->getMeigeeCatBlockBottom()) . '</div><div class="clear"></div>';
                        }
                    }
                    if (!empty($childrenWrapClass) && $childLevel == 0) {
                        $html .= '<div class="transparent"></div></div>';
                    }
                }
                $html .= '</li>';

                $counter++;

            }
            else{
                if($categoryComplete->getMeigeeCatCustomlink()) {
                    if ($categoryComplete->getMeigeeCatCustomlink() == '/') {
                        $itemUrl = Mage::getBaseUrl();
                    }
                    if ($categoryComplete->getMeigeeCatCustomlink() == '#') {
                        $itemUrl = '#';
                    }
                    else $itemUrl = $categoryComplete->getMeigeeCatCustomlink();
                }
                else $itemUrl = $child->getUrl();

                $child->setLevel($childLevel);
                $child->setIsFirst($counter == 1);
                $child->setIsLast($counter == $childrenCount);
                $child->setPositionClass($itemPositionClassPrefix . $counter);

                $outermostClassCode = '';
                $outermostClass = $menuTree->getOutermostClass();

                if ($childLevel == 0 && $outermostClass) {
                    $outermostClassCode = ' class="' . $outermostClass . '" ';
                    $child->setClass($outermostClass);
                }


                if(!Mage::getStoreConfig('meigee_categoriesenhanced/options/status')){
                    $catLabel = '';
                }
                $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
                $html .= '<a href="' . $itemUrl . '" ' . $outermostClassCode . '><span>'
                    . $this->escapeHtml($child->getName()) . '</span>'.$catLabel.'</a>';

                if ($child->hasChildren()) {
                    if (!empty($childrenWrapClass)) {
                        $isDefaultMenu = '';
                        if($categoryComplete->getMeigeeCatMenutype() == 1){
                            $isDefaultMenu = ' default-menu';
                        }
                        $html .= '<div class="' . $childrenWrapClass . $isDefaultMenu . '">';
                    }
                    $html .= '<ul class="level' . $childLevel . '">';
                    $html .= $this->_getHtml($child, $childrenWrapClass);
                    $html .= '</ul>';

                    if (!empty($childrenWrapClass)) {
                        $html .= '<div class="transparent"></div></div>';
                    }
                }
                $html .= '</li>';

                $counter++;
            }
        }
        return $html;
    }
}
			