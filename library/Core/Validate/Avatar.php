<?php

class Core_Validate_Avatar extends Zend_Validate_Abstract
{
    const NO_FILE = "validate.avatar.noFile";
    const SIZE = "validate.avatar.size";
    const WIDTH = "validate.avatar.width";
    const HEIGHT = "validate.avatar.height";
    const FORMAT = "validate.avatar.format";

    private $_config;
    protected $_messageTemplates = array(
        self::NO_FILE => "Plik nie istnieje",
        self::SIZE => "Rozmiar zdjęcia zbyt duży",
        self::WIDTH => "Szerokość zdjęcia zbyt duża",
        self::HEIGHT => "Wysokość zdjęcia zbyt duża",
        self::FORMAT => "Zdjęcie jest w nieodpowiednim formacie pliku",
    );

    public function __construct()
    {
        $this->_config = Zend_Registry::get('config')->avatar;
    }

    public function isValid($value, $context = null)
    {

        $sFile = (string) $value;
        if (!file_exists($sFile))
        {
            $this->_error(self::NO_FILE);
            return false;
        }

        if (filesize($sFile) > $this->_config->maxSize)
        {
            $this->_error(self::SIZE);
            return false;
        }

        $aImageSize = getimagesize($sFile);

        if ($aImageSize[0] > $this->_config->maxWidth)
        {
            $this->_error(self::WIDTH);
            return false;
        }

        if ($aImageSize[1] > $this->_config->maxHeight)
        {
            $this->_error(self::HEIGHT);
            return false;
        }

        if (!in_array($aImageSize["mime"], $this->_config->allowedMime->toArray()))
        {
            $this->_error(self::FORMAT);
            return false;
        }

        return true;
    }

}
