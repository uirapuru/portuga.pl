<?php

class Default_EmbedController
        extends Zend_Controller_Action
{

    public function init()
    {

        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {

    }

    public function translateAction()
    {
        $sWord = trim($this->_request->getParam("q", false));

        if ($sWord)
        {
            $oAlpha = new Zend_Filter_Alpha(true);
            $sWord = $oAlpha->filter(trim(strip_tags($sWord)));

            $oTranslator = new Model_Translator();
            $oTranslationsModel = new Model_Translations();
            $oFilter = new Core_Filter_Ogonki();

            $oResult = $oTranslationsModel->lookFor($sWord);
            $aGoogleTranslation = $oTranslator->translate($sWord);

            // sprawdzam czy już nie ma takiego wpisu w bazie
            $oDoesExistsPt = new Zend_Validate_Db_RecordExists(array(
                        "table" => "translations",
                        "field" => "filtered_pt"
                    ));
            $oDoesExistsPl = new Zend_Validate_Db_RecordExists(array(
                        "table" => "translations",
                        "field" => "filtered_pl"
                    ));

            $bPt = $oDoesExistsPt->isValid($oFilter->filter($aGoogleTranslation["pt"]));
            $bPl = $oDoesExistsPl->isValid($oFilter->filter($aGoogleTranslation["pl"]));

            if (!$bPt || !$bPl)
            {
                $this->view->aGoogleTranslation = $aGoogleTranslation;
            }
            $this->view->word = $sWord;
            $this->view->translations = $oResult->toArray();
        }
    }

}

