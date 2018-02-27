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


class Plumrocket_SocialLogin_Model_Mydigipass extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'mydigipass';
	
	protected $_responseType = 'code';
    
    protected $_sandbox = false;
    
    protected $_url = 'https://%s.mydigipass.com/oauth/authenticate';

	protected $_fields = array(
					'user_id' => 'uuid',
		            'firstname' => 'first_name',
		            'lastname' => 'last_name',
		            'email' => 'email',
		            'dob' => 'born_on',
                    'gender' => 'gender', // empty
                    'photo' => 'picture', // empty
				);

    protected $_dob = array('year', 'month', 'day', '-');
    protected $_gender = array('male', 'female');

	protected $_buttonLinkParams = array(
					// 'scope' => 'email,user_birthday',
                    'popup' => '1',
				);

    protected $_popupSize = array(800, 650);

	public function _construct()
    {      
        parent::_construct();
        
        $this->_url = sprintf($this->_url, ($this->_sandbox? 'sandbox' : 'www') );

        $this->_buttonLinkParams = array_merge($this->_buttonLinkParams, array(
            'client_id'     => $this->_applicationId,
            'redirect_uri'  => $this->_redirectUri,
            'response_type' => $this->_responseType,
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
        if ($response = $this->_callPost('https://'. ($this->_sandbox? 'sandbox' : 'www') .'.mydigipass.com/oauth/token', $params)) {
            $token = json_decode($response, true);
        }
        $this->_setLog($token, true);

        if (isset($token['access_token'])) {
            $params = array(
                'oauth_token' => $token['access_token']
            );
    
            if ($response = $this->_call('https://'. ($this->_sandbox? 'sandbox' : 'www') .'.mydigipass.com/oauth/user_data', $params)) {
                $data = json_decode($response, true);
            }
            $this->_setLog($data, true);
        }
 
        if (!$this->_userData = $this->_prepareData($data)) {
        	return false;
        }

        $this->_setLog($this->_userData, true);

        return true;
    }

    protected function _prepareData($data)
    {
    	if (empty($data['uuid'])) {
    		return false;
    	}

        return parent::_prepareData($data);
    }

    protected function _callPost($url, $params = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        // curl_setopt($curl, CURLOPT_USERPWD, $this->_applicationId .':'. $this->_secret);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

}