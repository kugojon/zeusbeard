function debug(string) {
    console.log(string);
}

Element.prototype.triggerEvent = function(eventName)
{
    if (document.createEvent)
    {
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent(eventName, true, true);

        return this.dispatchEvent(evt);
    }

    if (this.fireEvent)
        return this.fireEvent('on' + eventName);
};

if(typeof Product.ConfigurableSwatches != 'undefined') {
    Product.ConfigurableSwatches.prototype.attachOptEvents = function (opt) {
        var attr = opt.attr;
        // Swatch Events
        if (opt._f.isSwatch) {
            opt._e.a.observe('click', function (event) {
                Event.stop(event);
                this._F.currentAction = "click";
                // set new last option
                attr._e._last.selectedOption = attr._e.selectedOption;
                // Store selected option
                attr._e.selectedOption = opt;

                // Run the event
                this.onOptionClick(attr);
                attr._e.optionSelect.triggerEvent('change');
                return false;
            }.bind(this)).observe('mouseenter', function () {
                this._F.currentAction = "over-swatch";
                // set active over option to this option
                this._E.optionOver = opt;
                this.onOptionOver();
                // set the new last option
                this._E._last.optionOver = this._E.optionOver;
            }.bind(this)).observe('mouseleave', function () {
                this._F.currentAction = "out-swatch";
                this._E.optionOut = opt;
                this.onOptionOut();
            }.bind(this));
        }
    };
}

$jq(document).ready(function(){

    // on product page load - load only media

		var val = $jq(this).val();
        var optionsCount = $jq("select.super-attribute-select").length;
        var values = {};

        $jq("select.super-attribute-select").each(function(i, obj){
            var $obj = $jq(obj);
            var attrId = $obj.attr('id').replace(/[a-z]*/, '');
            values[attrId] = $obj.val();
        });

        var allowedProducts = {};
        jQuery.each(values, function(key, value){
            var options = spConfig.config.attributes[key].options;

            for(var i = 0; i < options.length; i++) {
                if(options[i].id == value) {
                    for(var j = 0; j < options[i].products.length; j++) {

                        if(typeof allowedProducts[options[i].products[j]] == 'undefined') {
                            allowedProducts[options[i].products[j]] = 0;
                        }

                        allowedProducts[options[i].products[j]] = allowedProducts[options[i].products[j]] + 1;
                    }
                }
            }
        });

        $jq.each(allowedProducts, function(key, value) {
            if(value == optionsCount) {
                $jq.ajax({
                    url:		mage_base_url + "configurl/index/media/",
                    type:		"POST",
                    data:       {"product_id" : key, "onlyMedia" : true},
                    dataType:	"json",
                    error: 		function(xhr, status, error){
                        debug(xhr);
                    },
                    success:	function(data, status, xhr){
                        if(data && data.update) {
                        
                            $jq.each(data.update, function(blockClass, blockHtml){
                                $jq(blockClass).html(blockHtml);
                            });

                            if(typeof ProductMediaManager != 'undefined') {
                                ProductMediaManager.init(); // Magento 1.9
                            } else {
                                $jq('.product-img-box #image').on('load', function(){
                                    product_zoom = new Product.Zoom('image', 'track', 'handle', 'zoom_in', 'zoom_out', 'track_hint');
                                });
                            }
                        }
                    }
                });
            }
        });

    // options dropdown onChange

    $jq("select.super-attribute-select").on('change', function(){
        var val = $jq(this).val();
        var optionsCount = $jq("select.super-attribute-select").length;
        var values = {};

        $jq("select.super-attribute-select").each(function(i, obj){
            var $obj = $jq(obj);
            var attrId = $obj.attr('id').replace(/[a-z]*/, '');
            values[attrId] = $obj.val();
        });

        var allowedProducts = {};
        jQuery.each(values, function(key, value){
            var options = spConfig.config.attributes[key].options;

            for(var i = 0; i < options.length; i++) {
                if(options[i].id == value) {
                    for(var j = 0; j < options[i].products.length; j++) {

                        if(typeof allowedProducts[options[i].products[j]] == 'undefined') {
                            allowedProducts[options[i].products[j]] = 0;
                        }

                        allowedProducts[options[i].products[j]] = allowedProducts[options[i].products[j]] + 1;
                    }
                }
            }
        });

        $jq.each(allowedProducts, function(key, value) {
            if(value == optionsCount) {
                $jq.ajax({
                    url:		mage_base_url + "configurl/index/media/",
                    type:		"POST",
                    data:       "product_id="+key,
                    dataType:	"json",
                    error: 		function(xhr, status, error){
                        debug(xhr);
                    },
                    success:	function(data, status, xhr){
                        if(data && data.update) {
                        
                        	if (!$jq(data.update.image_id).html()){
	                        	console.log(data.update.image_id + ' - Product image block not can be updated or image is empty for selected option, try another class/id and check Improved Configurable Product extension manual.')
                        	}
                        	
							if (!$jq(data.update.description_id).html()){
	                        	console.log(data.update.description_id + ' - Product description block not can be updated, try another class/id and check Improved Configurable Product extension manual.')
                        	}
                        	
                        	if (!$jq(data.update.short_description_id).html()){
	                        	console.log(data.update.short_description_id + ' - Product short description block not can be updated, try another class/id and check Improved Configurable Product extension manual.')
                        	}
                        	
                        	if (!$jq(data.update.title_id).html()){
	                        	console.log(data.update.title_id + ' - Product title block not can be updated, try another class/id and check Improved Configurable Product extension manual.')
                        	}
							
                        
                            $jq.each(data.update, function(blockClass, blockHtml){
                                $jq(blockClass).html(blockHtml);

                            });

                            if(typeof ProductMediaManager != 'undefined') {
                                ProductMediaManager.init(); // Magento 1.9
                            } else {
                                $jq('.product-img-box #image').on('load', function(){
                                    product_zoom = new Product.Zoom('image', 'track', 'handle', 'zoom_in', 'zoom_out', 'track_hint');
                                });
                            }
                        }
                    }
                });
            }
        });
    });

    $jq('#qty').on('keyup', function(){
        var qty = parseInt($jq(this).val());
        var product = [];

        spConfig.settings.each(function(element) {
            if(typeof element.options[element.selectedIndex].config != 'undefined') {
                if (product.length) {
                    product = product.intersect(element.options[element.selectedIndex].config.allowedProducts).uniq();
                } else {
                    product = element.options[element.selectedIndex].config.allowedProducts;
                }
            }
        });

        if(product.length && qty > 0) {
            var productId = product[0];
            var prevQty = 0;
            var productPrice = 0;

            if(priceConfig[productId].tierPrices.length > 0) {
                prevQty = priceConfig[productId].tierPrices[0].price_qty;
                priceConfig[productId].tierPrices.each(function(obj){
                    if(qty >= obj.price_qty && obj.price_qty >= prevQty) {
                        productPrice = obj.price;
                    }
                    prevQty = obj.price_qty;
                });
            }

            if(productPrice > 0) {
                if($jq('#product-price-' + productId).find('.price').length) {
                    $jq('#product-price-' + productId).find('.price').text(optionsPrice.formatPrice(productPrice));
                } else {
                    $jq('#product-price-' + productId).text(optionsPrice.formatPrice(productPrice));
                }
            } else {
                if(priceConfig[productId]) {
                    var priceBlock = $jq(priceConfig[productId].priceBlockHtml);
                    if(priceBlock.find('#product-price-' + productId).find('.price').length) {
                        productPrice = priceBlock.find('#product-price-' + productId).find('.price').text().trim();
                    } else {
                        productPrice = priceBlock.find('#product-price-' + productId).text().trim();
                    }
                    if($jq('#product-price-' + productId).find('.price').length) {
                        $jq('#product-price-' + productId).find('.price').text(productPrice);
                    } else {
                        $jq('#product-price-' + productId).text(productPrice);
                    }
                }
            }
        }
    });
});

Product.Config.prototype.reloadPrice = function(){
    var price    = 0;
    var oldPrice = 0;
    for(var i=this.settings.length-1;i>=0;i--){
        var selected = this.settings[i].options[this.settings[i].selectedIndex];
        if(selected.config){
            price    += parseFloat(selected.config.price);
            oldPrice += parseFloat(selected.config.oldPrice);
        }
    }

    var product = [];

    this.settings.each(function(element) {
        if(typeof element.options[element.selectedIndex].config != 'undefined') {
            if (product.length) {
                product = product.intersect(element.options[element.selectedIndex].config.allowedProducts).uniq();
            } else {
                product = element.options[element.selectedIndex].config.allowedProducts;
            }
        }
    });

    if(product.length) {
        var productId = product[0];
        if(priceConfig[productId]) {
            jQuery(priceConfig.priceClass).replaceWith(priceConfig[productId].priceBlockHtml);

            if(priceConfig[productId].tierPricesHtml) {
                jQuery(priceConfig.tierPriceClass).remove();
                jQuery('.price-info').append(priceConfig[productId].tierPricesHtml);
            }
        }
        
        var title = null;
        if (typeof priceConfig[productId] !== 'undefined') {
            if (typeof priceConfig[productId].title !== 'undefined') {
                title = priceConfig[productId].title;
            }
            History.pushState(null, title, priceConfig[productId].url);
        }        
    } else {
        optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
        optionsPrice.reload();
    }

    return price;
};

Product.Config.prototype.getOptionLabel = function(option, price) {
    return option.label;
};