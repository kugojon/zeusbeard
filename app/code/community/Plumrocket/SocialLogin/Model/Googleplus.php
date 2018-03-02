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


class Plumrocket_SocialLogin_Model_Googleplus extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'googleplus';
	
    protected $_url = 'https://accounts.google.com/o/oauth2/auth';

	protected $_fields = array(
					'user_id' => 'id',
		            'firstname' => 'given_name',
		            'lastname' => 'family_name',
		            'email' => 'email',
		            'dob' => 'birthday',
                    'gender' => 'gender',
                    'photo' => 'picture',
				);

    protected $_dob = array('year', 'month', 'day', '-');
    protected $_gender = array('male', 'female');

	protected $_buttonLinkParams = array(
					'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
				);

    protected $_popupSize = array(450, 450);

	public function _construct()
    {      
        parent::_construct();
        
        $this->_buttonLinkParams = array_merge($this->_buttonLinkParams, array(
            'client_id'     => $this->_applicationId,
            'redirect_uri'  => $this->_redirectUri,
            'response_type' => $this->_responseType
        ));
    }

    public function loadUserData($response)
    {
    	if (empty($response)) {
            return false;
        }

        $data = array();

        $params = array(
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'code' => $response,
            'redirect_uri' => $this->_redirectUri,
            'grant_type' => 'authorization_code',
        );

        $token = null;
        if ($response = $this->_callPost('https://accounts.google.com/o/oauth2/token', $params)) {
            $token = json_decode($response, true);
        }
    
        if (isset($token['access_token'])) {
            $params['access_token'] = $token['access_token'];
    
            if ($response = $this->_call('https://www.googleapis.com/oauth2/v1/userinfo', $params)) {
                $data = json_decode($response, true);
            }
            $this->_setLog($data);
        }
        
        if (!$this->_userData = $this->_prepareData($data)) {
        	return false;
        }

        $this->_setLog($this->_userData, true);

        return true;
    }

    protected function _prepareData($data)
    {
    	if (empty($data['id'])) {
    		return false;
    	}

        return parent::_prepareData($data);
    }

    protected function _callPost($url, $params = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    public function getSocialUrl()
    {
        if ($id = $this->getUserId()) {
            return 'https://plus.google.com/u/0/' . $id;
        }
        return null;
    }

}