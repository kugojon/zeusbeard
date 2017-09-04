<?php
/**
 * @category    Ms
 * @package     Ms_AddressVerification
 * @license     https://markshust.com/eula/
 */
class Ms_AddressVerification_Helper_Smartystreets
    extends Mage_Core_Helper_Abstract
{
    /**
     * Check to see if the address should be verified.
     *
     * @param $address object
     * @return bool
     */
    public function shouldVerifyAddress($address) {
        return $address->countryId == 'US' && ! $address->isBypassed ? true : false;
    }

    /**
     * Verify address, run through error checking and return the result.
     *
     * @param $address
     * @return object
     */
    public function verifyAddress($address)
    {
        $result = array();

        $apiResponse = Mage::helper('ms_addressverification/smartystreets')->verifyAddressApi($address);

        $result['error'] = Mage::helper('ms_addressverification/smartystreets')->getErrorFromResponse($apiResponse);

        if (empty($result['error'])) {
            // If no errors, let's pull object and it's first index (always exists, see error checking)
            $apiResponse = json_decode($apiResponse);
            $apiResponse = $apiResponse[0];

            $region = Mage::getModel('directory/region')->loadByCode($apiResponse->components->state_abbreviation, 'US');
            $zip4 = isset($apiResponse->components->plus4_code) ? '-' . $apiResponse->components->plus4_code : '';
            $result['address'] = array(
                'street'     => array(
                    0 => $apiResponse->delivery_line_1,
                    1 => isset($apiResponse->delivery_line_2) ? $apiResponse->delivery_line_2 : '',
                ),
                'city'       => $apiResponse->components->city_name,
                'region_id'  => $region->getId(),
                'postcode'   => $apiResponse->components->zipcode . $zip4,
                'country_id' => 'US' // everything passed to this API is in U.S.
            );

            Mage::helper('ms_addressverification')->updateExistingAddress($address->type, $result);
        }

        return $result;
    }

    /**
     * Call Smarty Streets API for address verification.
     *
     * @param $address object
     * @return string
     */
    public function verifyAddressApi($address) {
        // Prep object for SmartyStreets-specific call
        $address->street    = $address->street1;
        $address->secondary = $address->street2;
        $address->zipcode   = $address->postcode;

        $params = Mage::helper('ms_addressverification/smartystreets')->addressToParams($address);
        $reqUrl = Mage::getStoreConfig('ms_addressverification/smartystreets/us_street_address_api_url')
            . "?$params";

        $ch = curl_init($reqUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = (string) curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            $result = $error;
        }

        return trim($result);
    }

    /**
     * Build the params/querystring from address object.
     *
     * @param $address object
     * @return string
     */
    public function addressToParams($address)
    {
        $authId = Mage::getStoreConfig('ms_addressverification/smartystreets/auth_id');
        $authToken = Mage::getStoreConfig('ms_addressverification/smartystreets/auth_token');

        $params  = "auth-id=$authId";
        $params .= "&auth-token=$authToken";
        $params .= isset($address->street)      ? '&street='    . rawurlencode($address->street)    : '';
        $params .= isset($address->secondary)   ? '&secondary=' . rawurlencode($address->secondary) : '';
        $params .= isset($address->city)        ? '&city='      . rawurlencode($address->city)      : '';
        $params .= isset($address->state)       ? '&state='     . rawurlencode($address->state)     : '';
        $params .= isset($address->zipcode)     ? '&zipcode='   . rawurlencode($address->zipcode)   : '';

        return $params;
    }

    /**
     * Returns a string containing errors (if any).
     *
     * @param $response string
     * @return array
     */
    public function getErrorFromResponse($response)
    {
        $error = array();
        $responseObj = json_decode($response);

        if ($response == 'Unauthorized') {
            $message = 'Smarty Streets Error: Authentication error.';
            $error = array(
                'error' => -1,
                'message' => Mage::helper('ms_addressverification')->__($message),
                'error_addressverification' => 1,
                'allow_bypass' => Mage::getStoreConfig('ms_addressverification/general/allow_bypass'),
            );
        } elseif (! count($responseObj)) {
            $error = array(
                'error' => -1,
                'message' => Mage::helper('ms_addressverification')->__('Address not found.'),
                'error_addressverification' => 1,
                'allow_bypass' => Mage::getStoreConfig('ms_addressverification/general/allow_bypass'),
            );
        } elseif ($responseObj[0]->analysis->dpv_match_code == 'N'
            || $responseObj[0]->analysis->dpv_match_code == ''
        ) {
            $error = array(
                'error' => -1,
                'message' => Mage::helper('ms_addressverification')->__('Address not deliverable.'),
                'error_addressverification' => 1,
                'allow_bypass' => Mage::getStoreConfig('ms_addressverification/general/allow_bypass'),
            );
        } elseif ($responseObj[0]->analysis->dpv_match_code == 'D') {
            $message = 'The address you entered was found but more information is needed (such as an apartment, suite, or box number) to match to a specific address.';
            $error = array(
                'error' => -1,
                'message' => Mage::helper('ms_addressverification')->__($message),
                'error_addressverification' => 1,
                'allow_bypass' => Mage::getStoreConfig('ms_addressverification/general/allow_bypass'),
            );
        }

        return $error;
    }
}
