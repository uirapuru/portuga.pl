<?php

class Core_Filter_Samogloski implements Zend_Filter_Interface {
    public function filter($sValue) {
        $aSamogloski = array('a','e','u','i','o','y');
        $sWord = str_replace($aSamogloski, array(""), $sValue);
        return $sWord;
    }
}

 