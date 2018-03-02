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


class Plumrocket_SocialLogin_Model_Livejournal extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'livejournal';
    protected $_protocol = 'OpenID';
    protected $_identifier = '_ID_.livejournal.com';
    protected $_title = 'Enter your LiveJournal username';


    protected $_responseType = array(
                    'openid_mode',
                    'openid_identity',
                    'openid_sreg_nickname',
                    'openid_sreg_email',
                    'openid_sreg_dob',
                    'openid_sreg_gender',
                );

	protected $_fields = array(
					'user_id' => 'openid_identity',
		            'firstname' => 'nickname_fn', // empty
		            'lastname' => 'nickname_ln', // empty
		            'email' => 'openid_sreg_email', // empty
		            'dob' => 'openid_sreg_dob', // empty
                    'gender' => 'openid_sreg_gender', // empty
                    'photo' => 'photo', // empty
				);

    protected $_dob = array('year', 'month', 'day', '-');
    protected $_gender = array('M', 'F');

	protected $_buttonLinkParams = null;

    protected $_popupSize = array(730, 450);

	public function _construct()
    {      
        parent::_construct();
    }

    public function loadUserData($response)
    {
        if (empty($response['openid_mode']) || $response['openid_mode'] == 'cancel') {
            return false;
        }

        $data = array();
 
        if ($response['openid_mode'] == 'id_res') {
            $data = $response;
        }

        if (!$this->_userData = $this->_prepareData($data)) {
        	return false;
        }

        $this->_setLog($this->_userData, true);

        return true;
    }

    public function prepareIdentifier($identifier)
    {
        if (!$identifier = trim($identifier)) {
            return false;
        }

        if (false === strpos($identifier, str_replace('_ID_', '', $this->_identifier))) {
            $identifier = str_replace('_ID_', $identifier, $this->_identifier);
        }

        return $identifier;

    }

    public function getTitle()
    {
        return Mage::helper('pslogin')->__($this->_title);
    }

    protected function _prepareData($data)
    {
    	if (empty($data['openid_identity'])) {
    		return false;
    	}

        // Name.
        if (!empty($data['openid_sreg_nickname'])) {
            $nameParts = explode(' ', $data['openid_sreg_nickname'], 2);
            $data['nickname_fn'] = $nameParts[0];
            $data['nickname_ln'] = !empty($nameParts[1])? $nameParts[1] : '';
        }

        return parent::_prepareData($data);
    }

}