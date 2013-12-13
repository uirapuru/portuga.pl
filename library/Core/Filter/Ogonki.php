<?php

class Core_Filter_Ogonki implements Zend_Filter_Interface {
    public function filter($sValue) {
        $aPl =      array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');
        $aAsciiPl = array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z');
        
        $aPt =      array("à","á","â","ã","ă","é","ê","í","ó","ô","õ","ú","ü","À","Á","Â","Ã","É","Ê","Í","Ó","Ô","Õ","Ú","Ü","ç","Ç");
        $aAsciiPt = array('a','a','a','a',"a",'e','e','i','o','o','o','u','u','A','A','A','A','E','E','I','O','O','O','U','U','c','C');


        $sWord = str_replace($aPl, $aAsciiPl, $sValue);
        $sWord = str_replace($aPt, $aAsciiPt, $sWord);

        $sWord = strtolower($sWord);

        return $sWord;
    }
}

 