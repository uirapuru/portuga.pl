<?php

class Core_Validate_UserExists extends Zend_Validate_Abstract
{
    const NO_USER = "forms.errors.userDoesNotExists";
    
    protected $_messageTemplates = array(
        self::NO_USER => "Użytkownik nie istnieje"
    );

    public function isValid($value, $context = null)
    {
        $sEmail = (string) $value;

        $oUserTable = new Model_Users();
        $oUser = $oUserTable->getByEmail($sEmail);

        if ($oUser["email"] != $sEmail)
        {
            $this->_error(self::NO_USER);
            return false;
        }

        return true;
    }
}
