<?php

class Model_Translator
{

    protected $_link = "http://ajax.googleapis.com/ajax/services/language/translate"; //?v=1.0&q=%s&langpair=%s|%s
    protected $_ttsLink = "http://translate.google.com/translate_tts"; //?q=i%20niestety!&tl=pl
    protected $_checkLang = "http://ajax.googleapis.com/ajax/services/language/detect"; 
    const PL = "pl";
    const PT = "pt";

    public function translate($sWord) {

        $sWord = trim(strip_tags($sWord));

        /**
         * Sprawdzamy język i próbujemy ustalić czy polski czy portugalski
         */
//        $oCheckLang = new Zend_Http_Client($this->_checkLang, array(
//            'adapter'   => 'Zend_Http_Client_Adapter_Curl',
//            'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
//        ));
//        $oCheckLang->setParameterGet("v", "1.0");
//        $oCheckLang->setParameterGet("q", $sWord);
//
//        $oCheckedLang = $oCheckLang->request(Zend_Http_Client::GET);
//        $aCheckedLang = Zend_Json_Decoder::decode($oCheckedLang->getBody());
//        $sCheckedLang = $aCheckedLang["responseData"]["language"];

        /**
         * Pobieramy tłumaczenia dla obu na wszelki wypadek i porównujemy levenshteinem
         */
        $oClient = new Zend_Http_Client($this->_link, array(
            'adapter'   => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(
                //CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "utf-8"
                ),
        ));

        $oClient->setParameterGet("v", "1.0");
        $oClient->setParameterGet("q", $sWord);

        /**
         * Tłumaczenie na portugalski
         */
        $oClient->setParameterGet("langpair", self::PL ."|" . self::PT);
        $oResponsePortuguese = $oClient->request(Zend_Http_Client::GET);
        $aResponsePortuguese = Zend_Json_Decoder::decode($oResponsePortuguese->getBody());
        $sResponsePortuguese = $aResponsePortuguese["responseData"]["translatedText"];
        $iLevenPt = levenshtein($sResponsePortuguese, $sWord);

        /**
         * Tłumaczenie na polski
         */
        $oClient->setParameterGet("langpair", self::PT ."|" . self::PL);
        $oResponsePolish = $oClient->request(Zend_Http_Client::GET);
        $aResponsePolish = Zend_Json_Decoder::decode($oResponsePolish->getBody());
        $sResponsePolish = $aResponsePolish["responseData"]["translatedText"];
        $iLevenPl = levenshtein($sResponsePolish, $sWord);

        if($iLevenPl <= $iLevenPt)
            return array("id" => 'goo', "pl" => $sWord, "pt" => $sResponsePortuguese);
        else
            return array("id" => 'goo', "pl" => $sResponsePolish, "pt" => $sWord);
    }

    public function getTTS($sWord,$sLangCode)
    {
        $oClient = new Zend_Http_Client($this->_ttsLink, array(
            'adapter'   => 'Zend_Http_Client_Adapter_Curl',
            //'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
        ));

        $oClient->setParameterGet("q", $sWord);
        $oClient->setParameterGet("tl", $sLangCode);

        $oResponse = $oClient->request(Zend_Http_Client::GET);
        return $oResponse->getBody();
    }

}

