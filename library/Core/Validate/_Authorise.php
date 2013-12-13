<?php

class Core_Validate_Authorise extends Zend_Validate_Abstract
{
    private $_oAuthAdapter;

    const NOT_AUTHORISED = "forms.errors.authError";
    const NO_SEED = "forms.errors.noSeed";
    const DELETED = "forms.errors.deleted";
    
    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context))
        {
            if (!isset($context['password']))
            {
                $this->_error(self::NO_PASSWORD);
                return false;
            }
        }

        $this->_oAuthAdapter = new Zend_Auth_Adapter_DbTable();
        $oAuth = Zend_Auth::getInstance();
        $this->_oAuthAdapter->setTableName('users')
                ->setIdentityColumn('email')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('md5(?)');

        $oUserTable = new user_Model_DbTable_Users();
        $oUser = $oUserTable->getByEmail($value);
	/**
	 * Błąd jezeli nie ma seed'a
	 */
        if (!isset($oUser["seed"]))
        {
            $this->_error(self::NO_SEED);
            return false;
        }
	/**
	 * Błąd jezeli konto zostało skasowane
	 */
        if ($oUser["deleted"] != null)
        {
            $this->_error(self::DELETED);
            return false;
        }

	/**
	 * Zlepiamy hasło: magicword, hasło i seed
	 */
        $password = Zend_Registry::get('config')->magicword . $context['password'] . $oUser->seed;

	/**
	 * Następuje zalogowanie
	 */
        $this->_oAuthAdapter->setIdentity($value);
        $this->_oAuthAdapter->setCredential($password);
        $result = $oAuth->authenticate($this->_oAuthAdapter);
	/**
	 * Błąd, jeżeli dane są niepoprawne
	 */
        if (!$result->isValid())
        {
            $this->_error(self::NOT_AUTHORISED);
            return false;
        }
        return true;
    }

    public function getAuthAdapter()
    {
        return $this->_oAuthAdapter;
    }

}
