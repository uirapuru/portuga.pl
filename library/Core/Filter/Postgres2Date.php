<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Core_Filter_Postgres2Date implements Zend_Filter_Interface
{
    public function filter($value)
    {
	$iDateStamp = strtotime($value);
	return date(Zend_Registry::get("config")->dateformat, $iDateStamp);
    }

}

?>
