<?php

class Core_Validate_AuthorisePassword extends Zend_Validate_Abstract
{
    const NOT_AUTHORISED = "forms.errors.authError";
    const NO_SEED = "forms.errors.noSeed";
    const DELETED = "forms.errors.deleted";
    const NO_EMAIL = "forms.errors.noEmail";

    protected $_messageTemplates = array(
	self::NOT_AUTHORISED => "Nieprawidłowe hasło",
	self::NO_SEED => "Nie można było pobrać soli",
	self::NO_EMAIL => "Musisz podać email",
	self::DELETED => "Konto skasowane lub zablokowane przez administratora!",
    );

    public function isValid($value, $context = null)
    {
	if ($context == null || !is_array($context))
	{
	    $this->_error(self::NO_EMAIL);
	    return false;
	}

	$sPassword = (string) $value;
	$sEmail = (string) $context["email"];

	$this->_setValue($sPassword);

	$oUserTable = new Model_Users();
	$oUser = $oUserTable->getByEmail($sEmail);

	$sPasswordHash = md5($sPassword);

	/**
	 * Sprawdzam poprawność hasha
	 */
	if ($oUser == null || $sPasswordHash !== $oUser->password)
	{
	    $this->_error(self::NOT_AUTHORISED);
	    return false;
	}
	return true;
    }

}
