<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Core_Filter_Date2Postgres implements Zend_Filter_Interface {
    public function filter($value){
	$iDateStamp = strtotime($value);
	return date("Y-m-d",$iDateStamp);
    }
}
?>
