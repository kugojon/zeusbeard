Product.Configurable.prototype.initialize = function(attributes, links, idPrefix, grid, readonly) {
    this.templatesSyntax = new RegExp(
        '(^|.|\\r|\\n)(\'{{\\s*(\\w+)\\s*}}\')', "");
    this.attributes = attributes; // Attributes
    this.idPrefix = idPrefix; // Container id prefix
    this.links = $H(links); // Associated products
    this.newProducts = []; // For product that's created through Create
    // Empty and Copy from Configurable
    this.readonly = readonly;

    /* Generation templates */
    this.addAttributeTemplate = new Template(
        $(idPrefix + 'attribute_template').innerHTML.replace(/__id__/g,
            "'{{html_id}}'").replace(/ template no-display/g, ''),
        this.templatesSyntax);
    this.addValueTemplate = new Template(
        $(idPrefix + 'value_template').innerHTML.replace(/__id__/g,
            "'{{html_id}}'").replace(/__parentid__/g,
            "'{{html_parentid}}'").replace(/__parentid__/g,
            "'{{html_defaultvalue}}'").replace(/__defaultvalue__/g,
            "'{{value_id}}'").replace(/ template no-display/g, ''),
        this.templatesSyntax);
    this.pricingValueTemplate = new Template(
        $(idPrefix + 'simple_pricing').innerHTML, this.templatesSyntax);
    this.pricingValueViewTemplate = new Template(
        $(idPrefix + 'simple_pricing_view').innerHTML,
        this.templatesSyntax);

    this.container = $(idPrefix + 'attributes');

    /* Listeners */
    this.onLabelUpdate = this.updateLabel.bindAsEventListener(this); // Update
    // attribute
    // label
    this.onValuePriceUpdate = this.updateValuePrice
        .bindAsEventListener(this); // Update pricing value
    this.onValueTypeUpdate = this.updateValueType.bindAsEventListener(this); // Update
    // pricing
    // type
    this.onDefaultValueUpdate = this.updateDefaultValue.bindAsEventListener(this); // Update
    this.onValueDefaultUpdate = this.updateValueUseDefault
        .bindAsEventListener(this);

    /* Grid initialization and attributes initialization */
    this.createAttributes(); // Creation of default attributes

    this.grid = grid;
    this.grid.rowClickCallback = this.rowClick.bind(this);
    this.grid.initRowCallback = this.rowInit.bind(this);
    this.grid.checkboxCheckCallback = this.registerProduct.bind(this); // Associate/Unassociate
    // simple
    // product

    this.grid.rows.each( function(row) {
        this.rowInit(this.grid, row);
    }.bind(this));
};

Product.Configurable.prototype.createValueRow = function(container, value) {
    var templateVariables = $H( {});
    if (!this.valueAutoIndex) {
        this.valueAutoIndex = 1;
    }

    templateVariables.set('html_id', container.id + '_'
        + this.valueAutoIndex);
    templateVariables.set('html_parentid', container.id);
    templateVariables.set('value_id', value);
    templateVariables.update(value);
    var pricingValue = parseFloat(templateVariables.get('pricing_value'));
    if (!isNaN(pricingValue)) {
        templateVariables.set('pricing_value', pricingValue);
    } else {
        templateVariables.unset('pricing_value');
    }
    this.valueAutoIndex++;

    // var li = $(Builder.node('li', {className:'attribute-value'}));
    var li = $(document.createElement('LI'));
    li.className = 'attribute-value';
    li.id = templateVariables.get('html_id');
    li.update(this.addValueTemplate.evaluate(templateVariables));
    li.valueObject = value;
    if (typeof li.valueObject.is_percent == 'undefined') {
        li.valueObject.is_percent = 0;
    }

    if (typeof li.valueObject.pricing_value == 'undefined') {
        li.valueObject.pricing_value = '';
    }

    container.attributeValues.appendChild(li);

    var priceField = li.down('.attribute-price');
    var priceTypeField = li.down('.attribute-price-type');
    var defaultValueField = li.down('.attribute-default-value');

    if (priceTypeField != undefined && priceTypeField.options != undefined) {
        if (parseInt(value.is_percent)) {
            priceTypeField.options[1].selected = !(priceTypeField.options[0].selected = false);
        } else {
            priceTypeField.options[1].selected = !(priceTypeField.options[0].selected = true);
        }
    }

    if(defaultValueField != undefined) {
        if(value.default_value != undefined && value.default_value == 1) {
            defaultValueField.checked = true;
        }
    }

    Event.observe(priceField, 'keyup', this.onValuePriceUpdate);
    Event.observe(priceField, 'change', this.onValuePriceUpdate);
    Event.observe(priceTypeField, 'change', this.onValueTypeUpdate);
    Event.observe(defaultValueField, 'change', this.onDefaultValueUpdate);
    var useDefaultEl = li.down('.attribute-use-default-value');
    if (useDefaultEl) {
        if (li.valueObject.use_default_value) {
            useDefaultEl.checked = true;
            this.updateUseDefaultRow(useDefaultEl, li);
        }
        Event.observe(useDefaultEl, 'change', this.onValueDefaultUpdate);
    }
};

Product.Configurable.prototype.updateDefaultValue = function(event) {
    var li = Event.findElement(event, 'LI');


    this.attributes.each( function(attribute) {
        console.log(attribute);
        console.log(li.valueObject);
        attribute.values.each(function(value){
            if(li.valueObject.product_super_attribute_id == attribute.id) {
                if (value.value_index == li.valueObject.value_index) {
                    value.default_value = true;
                } else {
                    value.default_value = false;
                }
            }
        });
    });

    //li.valueObject.default_value = true;
    //this.updateSimpleForm();
    this.updateSaveInput();
};