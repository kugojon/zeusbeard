<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meigeeteam.com <nick@meigeeteam.com>
 * @copyright Copyright (C) 2010 - 2014 Meigeeteam
 *
 */
class Meigee_MeigeewidgetsHarbour_Block_Types_Widgetcontent extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Override method to output our custom image
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        // Get the default HTML for this option
        $html = parent::_getElementHtml($element);

	    $html = '<div class="title"><h2>Click on element to remove it from the widget</h2></div><div class="title-2"><h2>Click on element to add it to widget</h2></div><div class="widget-holder"><div class="widget-content">';
        $value = $element->getValue();
		if ($values = $element->getValues()) {
            foreach ($values as $option) {
				$html.= $this->_optionToHtml($element, $option, $value);
            }
        }
        $html.= $element->getAfterElementHtml();
	    $html.= '</div><div class="items-container">
			<div class="product_name-holder"><div class="product_name-sub"></div></div>
			<div class="rating_stars-holder"><div class="rating_stars-sub"></div></div>
			<div class="reviews">
				<div class="rating_cust_link-holder"><div class="rating_cust_link-sub"></div></div>
				<div class="rating_add_review_link-holder"><div class="rating_add_review_link-sub"></div></div>
			</div>
			<div class="add_to_cart-holder"><div class="add_to_cart-sub"></div></div>
			<div class="price"><div class="price-holder"><div class="price-sub"></div></div></div>
			<div class="wishlist-holder"><div class="wishlist-sub"></div></div>
			<div class="compare-holder"><div class="compare-sub"></div></div>
		</div></div>';

        return $html;
    }

	/**
	 * Override method to output wrapper
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @param Array $option
	 * @param String $selected
	 * @return String
	 */
    protected function _optionToHtml($element, $option, $selected)
    {
		$html = "<div class='img-box'><img class='product-img' src='".Mage::getDesign()->getSkinUrl("images/widget_product_img.jpg")."' alt='' /></div>
		<div class='left-col'>
			<div class='item'><h2 class='product_name'>Sweatshirt With Varsity Print</h2></div>
			<div class='item'><img class='rating_stars' src='".Mage::getDesign()->getSkinUrl("images/widget_product_ratings.jpg")."' alt='' /></div>
			<div class='reviews'>
				<div class='item inline'>
					<span class='rating_cust_link'>3 Review(s)</span>
				</div>
				<span class='review-divide'> | </span>
				<div class='item inline'>
					<span class='rating_add_review_link'>Add Your Review</span>
				</div>
			</div>
			<div class='clear'></div>
		</div>
		<div class='item f-none'>
			<div class='price'>
				<div class='regular-price'>$285.00</div>
			</div>
		</div>
		<div class='item cart-btn inline'>
			<img class='add_to_cart' src='".Mage::getDesign()->getSkinUrl("images/widget_product_cart.jpg")."' alt='' />
		</div>
		<div class='item inline'>
			<img class='wishlist' src='".Mage::getDesign()->getSkinUrl("images/widget_product_wishlist.jpg")."' alt='' />
		</div>
		<div class='item inline'>
			<img class='compare' src='".Mage::getDesign()->getSkinUrl("images/widget_product_compare.jpg")."' alt='' />
		</div>
		<script type='text/javascript'>
			function hideLabels(labelsAttr){
				labelsAttr = ['price', 'add_to_cart', 'wishlist', 'compare', 'rating_stars', 'rating_cust_link', 'rating_add_review_link', 'product_name'];
				labels = $$('.form-list label');
				labels.each(function(item){
					labelsAttr.each(function(attr){
						if(item.readAttribute('for') && item.readAttribute('for').indexOf(attr) !=-1){
							setTimeout(function(){
								item.up(1).hide();
							}, 10);
						}
					});
				});
			}
			hideLabels();
			
			/* title hider */
			function titleHider(){
				$('widget_options').childElements().each(function(section){
					if(!section.hasClassName('no-display')){
						if(section.select('.items-container .item').length){
							section.select('.title-2')[0].show();
						}else{
							section.select('.title-2')[0].hide();
						}
					}
				});
			}
			titleHider();
			
			function elementsHider(){
				$('widget_options').childElements().each(function(section){
					if(!section.hasClassName('no-display')){
						/* reviews dividers */
						if(section.select('.widget-content .reviews .rating_cust_link').length && section.select('.widget-content .reviews .rating_add_review_link').length){
							section.select('.widget-content .reviews .review-divide')[0].show();
						}else{
							section.select('.widget-content .reviews .review-divide')[0].hide();
						}
						
						/* hide review when empty */
						if(!section.select('.widget-content .reviews .rating_cust_link').length && !section.select('.widget-content .reviews .rating_add_review_link').length){
							section.select('.widget-content .reviews')[0].hide();
						}else{
							section.select('.widget-content .reviews')[0].show();
						}
						
						/* title hider */
						titleHider();
						
						/* bottom divider */
						topBlocks = true;
						botBlocks = true;
						if(section.select('.widget-content .product_name').length || section.select('.widget-content .rating_stars').length || section.select('.widget-content .rating_cust_link').length || section.select('.widget-content .rating_add_review_link').length || section.select('.widget-content .regular-price').length || section.select('.widget-content .add_to_cart').length){
							topBlocks = true;
						}else{
							topBlocks = false;
						}
						if(section.select('.widget-content .wishlist').length || section.select('.widget-content .compare').length){
							botBlocks = true;
						}else{
							botBlocks = false;
						}
						
					}
				});
			}
			
			function option(name, status, sectionId){
				$(sectionId).select('select').each(function(select){
					parameter = 'parameters[' + name;
					if(select.readAttribute('name').indexOf(parameter) != -1){
						if(status == 'set'){
							select.value = 1;
						}else{
							select.value = 0;
						}
					}
				});
			}
			
			function itemsHandler(elements){
				$('widget_options').childElements().each(function(section){
					elements.each(function(item){
						if(!section.hasClassName('no-display')){
							section.select(item)[0].up(0).stopObserving().on('click', function(event){
								event.stopPropagation();
								elem = this.childElements()[0].className;
								newLoc = '.' + elem + '-holder';
								holder = section.select(newLoc)[0];
								sub = holder.select('div.' + elem + '-sub')[0];
								content = this.replace(sub);
								holder.insert(content);
								option(elem, 'clear', section.readAttribute('id'));
								
								holder.select('.item')[0].childElements()[0].on('click', function(event){
									event.stopPropagation();
									getClass = this.up(1).className;
									getHome = section.select('.' + getClass.replace('-holder', '-sub'))[0];
									returnSub = getHome.replace(this.up(0));
									section.select('.' + getClass)[0].insert(returnSub);
									elementsHider();
									option(this.className, 'set', section.readAttribute('id'));
									this.stopObserving(); //destroy handler
								});
								elementsHider();
							});
						}
					});
				});
			}
			itemsHandler(['.product_name', '.rating_stars', '.rating_cust_link', '.rating_add_review_link', '.price',  '.add_to_cart', '.wishlist', '.compare']);
			
			/* Event Simulate */
			function triggerEvent(element, eventName){
				// safari, webkit, gecko
				if (document.createEvent){
					var evt = document.createEvent('HTMLEvents');
					evt.initEvent(eventName, true, true);
					return element.dispatchEvent(evt);
				}
				
				// Internet Explorer
				if (element.fireEvent){
					return element.fireEvent('on' + eventName);
				}
			}
			
			/* Read widget options */
			function optionsReader(){
				parameters = ['price', 'add_to_cart', 'wishlist', 'compare', 'rating_stars', 'rating_cust_link', 'rating_add_review_link', 'product_name'];
				setTimeout(function(){
					$('widget_options').childElements().each(function(section){
						if(!section.hasClassName('no-display')){
							parameters.each(function(param){
								section.select('select').each(function(select){
									parameter = 'parameters[' + param;
									if(select.readAttribute('name').indexOf(parameter) != -1){
										if(((select.up(1).readAttribute('style') == null) || (select.up(1).readAttribute('style').indexOf('display: none') == -1)) && (select.value == 0)){
											if(section.select('.widget-content .' + param).length){
												triggerEvent(section.select('.widget-content .' + param)[0].up(0), 'click'); //fire event
											}
										}
									}
								});
							});
						}
					});
				}, 100);
			}
			optionsReader();
			
			/* Actions for template switcher */
			function templateActions(){
				function templateHandler(event){
					optionsReader();
					hideLabels();
					
					/* template change action */
					if((event.target.value.indexOf('masonry_grid.phtml') !=-1)){
						event.target.up(2).select('.widget-holder')[0].up(1).hide();
					}else{
						event.target.up(2).select('.widget-holder')[0].up(1).show();
					}
					
					if((event.target.value.indexOf('footer_list.phtml') !=-1)){
						event.target.up(2).select('.widget-holder')[0].select('.rating_stars, .reviews, .rating_cust_link, .rating_add_review_link, .review-divide').each(function(item){
							item.hide();
						});
					}
				}
				
				/* event handler for template switcher */
				$('widget_options').childElements().each(function(section){
					if(!section.hasClassName('no-display')){
						section.select('select').each(function(select){
							if(select.readAttribute('name').indexOf('parameters[template]') !=-1){
								select.observe('change', templateHandler);
							}
						});
					}
				});
			}
			templateActions();
			
		</script>";
        return $html;
    }
}