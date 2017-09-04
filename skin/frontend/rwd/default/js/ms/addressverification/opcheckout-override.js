/**
 * @category    Ms
 * @package     Ms_AddressVerification
 * @license     https://markshust.com/eula/
 *
 * This file just deals with adding in error messaging and allowing bypassing the verification;
 * it doesn't have anything to do with the actual address verification.
 */
// Override core billing nextStep function to bypass USPS Address Verification
Billing.prototype.nextStep = function(transport) {
    if (transport && transport.responseText) {
        try {
            response = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            response = {};
        }
    }

    if (response.error) {
        if ((typeof response.message) == 'string') {
            alert(response.message);
        } else {
            if (window.billingRegionUpdater) {
                billingRegionUpdater.update();
            }

            alert(response.message.join("\n"));
        }

        // Check for address verification error
        if (response.error_addressverification && response.allow_bypass == 1) {
            // Add 'Bypass Address Verification' button if it doesn't already exist on page
            if ($('addressverification_billing') == undefined) {
                $$('#co-billing-form button').first().insert({
                    after: '<button type="button" title="Bypass Address Verification & Continue"'
                        + ' id="addressverification_billing" class="button addressverification" onclick="addressverificationBypassBilling()">'
                        + '<span><span>Bypass Address Verification & Continue</span></span></button>'
                });
            }
        }

        return false;
    }

    checkout.setStepResponse(response);
    payment.initWhatIsCvvListeners();
};

// Override core shipping nextStep function to bypass USPS Address Verification
Shipping.prototype.nextStep = function(transport) {
    if (transport && transport.responseText) {
        try {
            response = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            response = {};
        }
    }
    if (response.error) {
        if ((typeof response.message) == 'string') {
            alert(response.message);
        } else {
            if (window.shippingRegionUpdater) {
                shippingRegionUpdater.update();
            }
            alert(response.message.join("\n"));
        }

        // Check for address verification error
        if (response.error_addressverification && response.allow_bypass == 1) {
            // Add 'Bypass Address Verification' button if it doesn't already exist on page
            if ($('addressverification_shipping') == undefined) {
                $$('#co-shipping-form button').first().insert({
                    after: '<button type="button" title="Bypass Address Verification & Continue"'
                        + ' id="addressverification_shipping" class="button addressverification" onclick="addressverificationBypassShipping()">'
                        + '<span><span>Bypass Address Verification & Continue</span></span></button>'
                });
            }
        }

        return false;
    }

    checkout.setStepResponse(response);
};
