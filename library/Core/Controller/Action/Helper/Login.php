<?php

class Core_Controller_Action_Helper_Login extends Zend_Controller_Action_Helper_Abstract
{

    private $_oAuth;
    private $_oAdapter;
    private $_oUserTable;

    function init()
    {
	$this->_oAuth = Zend_Auth::getInstance();
	$oAdapterConfig = new Zend_Db_Adapter_Pdo_Pgsql(Zend_Registry::get('config')->resources->db->params);
	$this->_oAdapter = new Zend_Auth_Adapter_DbTable($oAdapterConfig);
	$this->_oAdapter->setTableName('users');
	require_once APPLICATION_PATH."/modules/user/models/DbTable/Users.php";
	$this->_oUserTable = new user_Model_DbTable_Users();
    }

    function setCookie()
    {
	$sHash = md5(microtime());
	$iUserId = intval($this->_oAuth->getIdentity()->id);
	$this->_oUserTable->update(array('cookie_id' => $sHash), "id = $iUserId");
	setcookie('login', $iUserId . ':' . $sHash, strtotime('+1 week'), '/');
    }

    function logout()
    {
	
    }

    function direct($aParams = false) {
	return ($aParams) ? $this->login($aParams) : $this;
    }

    function login(array $aParams)
    {

	if ($this->_oAuth->hasIdentity() === true)
	{
	    $this->_oAuth->clearIdentity();
	}

	switch ($aParams['mode'])
	{
	    case 'normal':
		if (!isset($aParams['email']) || !isset($aParams['password']))
		{
		    throw new Exception('No email or password provided');
		}
		$this->_oAdapter->setIdentityColumn('email')
			->setCredentialColumn('password')
			->setCredentialTreatment('md5(?)');

		$oUser = $this->_oUserTable->getByEmail($aParams['email']);

		$sPasswordHash = Zend_Registry::get('config')->magicword . $aParams['password'] . $oUser->seed;

		$this->_oAdapter->setIdentity($aParams['email']);
		$this->_oAdapter->setCredential($sPasswordHash);
		$oResult = $this->_oAuth->authenticate($this->_oAdapter);
		break;

	    case 'cookie':
		if (!isset($aParams['id']) || !isset($aParams['hash']))
		{
		    throw new Exception('No cookie data provided');
		}

		$this->_oAdapter->setTableName('users')
			->setIdentityColumn('id')
			->setCredentialColumn('cookie_id');

		$this->_oAdapter->setIdentity($aParams["id"])
			->setCredential($aParams["hash"]);
		$oResult = $this->_oAuth->authenticate($this->_oAdapter);
		break;
	}

	if ($oResult->isValid())
	{
	    $oAuthData = $this->_oAdapter->getResultRowObject(null, 'password');
	    $this->_oAuth->getStorage()->write($oAuthData);
	}
	else
	{
	    throw new Exception('Login Error');
	    return false;
	}
	return true;
    }

}