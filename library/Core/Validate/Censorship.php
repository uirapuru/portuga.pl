<?php

class Core_Validate_Censorship extends Zend_Validate_Abstract
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

    private $_pt = array(
        "porra",
        "caralho",
        "puta",
        "culho",
        "merda",
        "foda",
        "foder",
        "fudeu"
    );

    private $_pl = array(
        "kurwa",
        "cipa",
        "gówno",
        "chuj",
        "pedał",
        "jebać",
        "jebany",
        "skurwiel",
        "chujowo",
        "huj",
        "hujowo",
        "kutas",
        "spierdalaj",
        "pierdolić",
        "pierdolony",
        "pierdolona"
    );

    public function isValid($value, $context = null)
    {
        $oFilter = new Zend_Filter();
        $oFilter->addFilter(new Core_Filter_Ogonki());
        $oFilter->addFilter(new Core_Filter_Samogloski());

        $sWord = trim(strip_tags($oFilter->filter($value)));
        $aBadwords = array_merge($this->_pl,$this->_pt);
        
        foreach($aBadwords as $sBadword) {
            $sBadword = trim(strip_tags($oFilter->filter($sBadword)));
            $iLength = levenshtein($sBadword, $sWord);

            if($iLength < 2) {
            Zend_Debug::dump(array(
                "word" => $sWord,
                "badword" => $sBadword,
                "length" => $iLength
            ));
            }
        }

        return;


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
