<?php

/**
 * @todo Dorobić jakieś sensowne powiadomienia o błędach!
 * @todo przesyłanie dla ratingu zamienić na posta
 * @todo dołożyć ograniczenie dlugośći
 * 
 */
class Default_IndexController
        extends Zend_Controller_Action
{

    public function init()
    {
        parent::init();

        $oAjaxContext = $this->_helper->getHelper('AjaxContext');
        $oAjaxContext->addActionContext('getTts', 'json')->initContext();

        $this->captcha = new Zend_Service_ReCaptcha("6Lc3L8ISAAAAAA4qWQjL7J_gCRILmtZ-4HkcPfHs", "6Lc3L8ISAAAAANHFeUJ2MuQPL1vU4gaD7lPT9W6P");
    }

    public function indexAction()
    {

        $sWord = trim($this->_request->getParam("word", null));

        $this->view->captcha = $this->captcha->getHtml();

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

    public function aboutAction()
    {
        
    }

    public function rateAction()
    {
        $sHash = $this->_request->getParam("id", false);

        if ($sHash)
        {
            $aData = @unserialize(base64_decode($sHash));

            if (!$aData)
            {
                echo "<div class='container_16'>Błąd!</div>";
                return;
            }

            $aData = array_intersect_key($aData, array("id" => 0, "pt" => 0, "pl" => 0));
            $aData = array_map('trim', $aData);

            $oFilter = new Core_Filter_Ogonki();
            $oTranslations = new Model_Translations();
            $oNotes = new Model_Notes();

            $oDoesExistsPt = new Zend_Validate_Db_RecordExists(array(
                        "table" => "translations",
                        "field" => "filtered_pt"
                    ));
            $oDoesExistsPl = new Zend_Validate_Db_RecordExists(array(
                        "table" => "translations",
                        "field" => "filtered_pl"
                    ));

            $bPt = $oDoesExistsPt->isValid($oFilter->filter($aData["pt"]));
            $bPl = $oDoesExistsPl->isValid($oFilter->filter($aData["pl"]));

            $sIp = $_SERVER['REMOTE_ADDR'];

            if ($bPt && $bPl)
            {
                if ($aData["id"] == "goo")
                    return;

                $oCheckIp = $oNotes->fetchRow("fk_translations = " . intval($aData["id"]) . " and ip = '{$sIp}'");

                $iAddedSecondsAgo = time() - strtotime($oCheckIp->date);
                
                if ($oCheckIp == null || $iAddedSecondsAgo < 3600 || $sIp == "127.0.0.1")
                {
                    echo "OK, robie!";
                    $oNotes->insert(array(
                        "fk_translations" => $aData["id"],
                        "ip" => $sIp,
                        "date" => date("Y-m-d H:i:s"),
                    ));

                    $iResult = $oTranslations->update(array(
                                "note" => new Zend_Db_Expr('note + 1')
                                    ), "id = " . intval($aData["id"]));

                    if ($iResult == 0)
                    {
                        echo "Błąd!";
                    }
                }
            }
            else if (!$bPt || !$bPl || $aData["id"] == "goo")
            {
                $iResult = $oTranslations->insert(array(
                            "pl" => trim(strip_tags($aData["pl"])),
                            "pt" => trim(strip_tags($aData["pt"])),
                            "filtered_pt" => trim(strip_tags($oFilter->filter($aData["pt"]))),
                            "filtered_pl" => trim(strip_tags($oFilter->filter($aData["pl"]))),
                            "date_added" => date("Y-m-d H:i:s"),
                            "ip" => $sIp,
                            "moderated" => 0,
                            "note" => 1
                        ));
                if ($iResult == 0)
                {
                    echo "Błąd!";
                }

                $oNotes->insert(array(
                    "fk_translations" => $iResult,
                    "ip" => $sIp,
                    "date" => date("Y-m-d H:i:s"),
                ));
            }
        }

        $this->_redirect($_SERVER["HTTP_REFERRER"]);
    }

    public function newAction()
    {

        if ($this->_request->isPost())
        {

            $oResult = $this->captcha->verify(
                            $_POST['recaptcha_challenge_field'],
                            $_POST['recaptcha_response_field']
            );

            if(!$oResult->isValid())
            {
                echo "Wpisałeś nieprawidłowe słowa! Przejdź wstecz i spróbuj ponownie";
                return;
            }

            $aData = array_intersect_key($this->_request->getParams(), array("wordPt" => 0, "wordPl" => 0));
            $oTranslations = new Model_Translations();
            $oFilter = new Zend_Filter;
            $oFilter->addFilter(new Core_Filter_Ogonki());
            $oFilter->addFilter(new Zend_Filter_Alpha(true));

            $oLastDate = $oTranslations->lastInsertByIp($_SERVER['REMOTE_ADDR']);
            if (isset($oLastDate["date_added"]))
            {
                $iDifference = time() - strtotime($oLastDate["date_added"]);

                if ($iDifference < 60)
                {
                    echo "Musisz odczekać 1 minutę przed dodaniem kolejnego wpisu!";
                    return;
                    //$this->_redirect($this->view->baseUrl());
                }
            }

            $oDoesExistsPt = new Zend_Validate_Db_RecordExists(array(
                        "table" => "translations",
                        "field" => "filtered_pt"
                    ));
            $oDoesExistsPl = new Zend_Validate_Db_RecordExists(array(
                        "table" => "translations",
                        "field" => "filtered_pl"
                    ));

            $bPt = $oDoesExistsPt->isValid($oFilter->filter($aData["wordPt"]));
            $bPl = $oDoesExistsPl->isValid($oFilter->filter($aData["wordPl"]));

            if ($bPt && $bPl)
            {
                echo "Taki wpis już istnieje!";
                return;
            }

            $iResult = $oTranslations->insert(array(
                        "pl" => trim(strip_tags($aData["wordPl"])),
                        "pt" => trim(strip_tags($aData["wordPt"])),
                        "filtered_pl" => trim(strip_tags($oFilter->filter($aData["wordPl"]))),
                        "filtered_pt" => trim(strip_tags($oFilter->filter($aData["wordPt"]))),
                        "date_added" => date("Y-m-d H:i:s"),
                        "ip" => $_SERVER['REMOTE_ADDR'],
                        "moderated" => 0,
                        "note" => 0
                    ));
            /**
             * @todo na wypadek gdyby nie dodał - błąd
             */
        }

        echo "Dzięki za wpis!";
        //$this->_redirect($this->view->baseUrl());
    }

    public function getTtsAction()
    {
        $sWord = $this->_request->getParam("word", null);
        $sLangCode = $this->_request->getParam("language", null);

        $oTranslator = new Model_Translator();

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=translation.mp3");
        header("Content-Type: audio/mpeg");
        header("Content-Transfer-Encoding: binary");
        echo $oTranslator->getTTS($sWord, $sLangCode);
    }

}

