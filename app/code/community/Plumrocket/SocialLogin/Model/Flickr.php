<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SocialLogin
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_SocialLogin_Model_Flickr extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'flickr';

    const URL_REQUEST_TOKEN = 'https://www.flickr.com/services/oauth/request_token';
    const URL_AUTHORIZE = 'https://www.flickr.com/services/oauth/authorize';
    const URL_ACCESS_TOKEN = 'https://www.flickr.com/services/oauth/access_token';
    const URL_ACCOUNT_DATA = 'https://api.flickr.com/services/rest/';

    protected $_responseType = array('oauth_token', 'oauth_verifier');

	protected $_fields = array(
					'user_id' => 'user_nsid',
		            'firstname' => 'fullname_fn',
		            'lastname' => 'fullname_ln',
		            'email' => 'email', // empty
		            'dob' => 'birthday', // empty
                    'gender' => 'gender', // empty
                    'photo' => 'photoUrl',
				);

    protected $_gender = array('male', 'female');

	protected $_buttonLinkParams = null;

    protected $_popupSize = array(700, 700);

    
    public function _construct()
    {      
        parent::_construct();
    }

    public function getProviderLink()
    {
        $token = $this->_getStartToken();
        if (!empty($token['oauth_token'])) {
            $this->_buttonLinkParams = self::URL_AUTHORIZE .'?oauth_token='. $token['oauth_token']. '&perms=read';
        }
        return parent::getProviderLink();
    }

    // Step 2.
    public function loadUserData($response)
    {
        if (empty($response['oauth_token']) || empty($response['oauth_verifier'])) {
            return false;
        }
        
        $data = array();
        $session = Mage::getSingleton('customer/session');

        $oauth_nonce = md5(uniqid(rand(), true));
        $oauth_timestamp = time();

        $oauth_token = $response['oauth_token'];
        $oauth_verifier = $response['oauth_verifier'];
        $oauth_token_secret = $session->getData($this->_type .'_oauth_token_secret');

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode(self::URL_ACCESS_TOKEN)."&";
        $oauth_base_text .= urlencode("oauth_consumer_key=".$this->_applicationId."&");
        $oauth_base_text .= urlencode("oauth_nonce=".$oauth_nonce."&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_timestamp=".$oauth_timestamp."&");
        $oauth_base_text .= urlencode("oauth_token=".$oauth_token."&");
        $oauth_base_text .= urlencode("oauth_verifier=".$oauth_verifier."&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        $key = $this->_secret .'&'. $oauth_token_secret;
        $oauth_signature = base64_encode(hash_hmac('sha1', $oauth_base_text, $key, true));

        $url = self::URL_ACCESS_TOKEN;
        $url .= '?oauth_consumer_key='.$this->_applicationId;
        $url .= '&oauth_nonce='.$oauth_nonce;
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp='.$oauth_timestamp;
        $url .= '&oauth_version=1.0';
        $url .= '&oauth_token='.urlencode($oauth_token);
        $url .= '&oauth_verifier='.urlencode($oauth_verifier);
        $url .= '&oauth_signature='.urlencode($oauth_signature);

        if ($response = $this->_callGET($url)) {
            parse_str($response, $result);
        }else{
            return;
        }
        $this->_setLog($result, true);

        // Get user data.
        if (!empty($result['user_nsid'])) {
            
            $params = array(
                'method' => 'flickr.people.getInfo',
                'api_key' => $this->_applicationId,
                'user_id' => $result['user_nsid'],
                'format' => 'json',
                'nojsoncallback' => '1',
            );

            if ($response = $this->_callGet(self::URL_ACCOUNT_DATA, $params)) {
                $data = json_decode($response, true);
            }
            $this->_setLog($data, true);

        }

        $data = is_array($data)? array_merge($result, $data) : $result;
        
        if (!$this->_userData = $this->_prepareData($data)) {
            return false;
        }

        $this->_setLog($this->_userData, true);

        return true;
    }

    // Step 1.
    protected function _getStartToken()
    {
        $oauth_nonce = md5(uniqid(rand(), true));
        $oauth_timestamp = time();

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode(self::URL_REQUEST_TOKEN)."&";
        $oauth_base_text .= urlencode("oauth_callback=".urlencode($this->_redirectUri)."&");
        $oauth_base_text .= urlencode("oauth_consumer_key=".$this->_applicationId."&");
        $oauth_base_text .= urlencode("oauth_nonce=".$oauth_nonce."&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_timestamp=".$oauth_timestamp."&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        $key = $this->_secret."&";
        $oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        $url = self::URL_REQUEST_TOKEN;
        $url .= '?oauth_callback='.urlencode($this->_redirectUri);
        $url .= '&oauth_consumer_key='.$this->_applicationId;
        $url .= '&oauth_nonce='.$oauth_nonce;
        $url .= '&oauth_signature='.urlencode($oauth_signature);
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp='.$oauth_timestamp;
        $url .= '&oauth_version=1.0';

        if ($response = $this->_callGet($url)) {
            parse_str($response, $result);
        }

        if (!empty($result['oauth_token_secret'])) {
            $session = Mage::getSingleton('customer/session');
            $session->setData($this->_type .'_oauth_token_secret', $result['oauth_token_secret']);
        }

        $this->_setLog($result, true);

        return $result;
    }

    protected function _prepareData($data)
    {
        if (empty($data['user_nsid'])) {
            return false;
        }

        // Name.
        if (!empty($data['fullname'])) {
            $nameParts = explode(' ', $data['fullname'], 2);
            $data['fullname_fn'] = $nameParts[0];
            $data['fullname_ln'] = !empty($nameParts[1])? $nameParts[1] : '';
        }

        // Photo.
        if (!empty($data['person']['iconfarm']) && !empty($data['person']['iconserver'])) {
           $data['photoUrl'] = "http://farm{$data['person']['iconfarm']}.staticflickr.com/{$data['person']['iconserver']}/buddyicons/{$data['user_nsid']}.jpg"; 
        }

        return parent::_prepareData($data);
    }

    protected function _callGet($url, $params = array())
    {
        if (is_array($params) && $params) {
            $url .= '?'. urldecode(http_build_query($params));
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        
        // curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_USERAGENT, 'pslogin');

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

}