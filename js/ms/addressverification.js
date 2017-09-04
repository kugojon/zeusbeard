/**
 * @category    Ms
 * @package     Ms_AddressVerification
 * @license     https://markshust.com/eula/
 *
 * This file adds hidden input elements to the billing & shipping forms when continue,
 * do we know which addresses have been bypassed.
 */
/**
 * Bypass Address Verification by adding hidden element to billing form.
 */
function addressverificationBypassBilling(){
    // Insert hidden element to bypass address verification
    $('co-billing-form').insert({
        bottom: '<input type="hidden" name="billing[addressverification_bypass]" id="billing:addressverification_bypass" value="1"/>'
    });

    billing.save();

    // Remove elements after saving information
    $('addressverification_billing').remove();
    $('billing:addressverification_bypass').remove();
}

/**
 * Bypass USPS Address Verification by adding hidden element to shipping form.
 */
function addressverificationBypassShipping(){
    // Insert hidden element to bypass address verification
    $('co-shipping-form').insert({
        bottom: '<input type="hidden" name="shipping[addressverification_bypass]" id="shipping:addressverification_bypass" value="1"/>'
    });

    shipping.save();

    // Remove elements after saving information
    $('addressverification_shipping').remove();
    $('shipping:addressverification_bypass').remove();
}

/**
 * Bypass USPS Address Verification by adding hidden element to address form.
 */
function addressverificationBypassAddress(){
    // Insert hidden element to bypass address verification
    $('form-validate').insert({
        bottom: '<input type="hidden" name="addressverification_bypass" id="addressverification_bypass" value="1"/>'
    });

    $('form-validate').submit();
}
