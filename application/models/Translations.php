<?php

class Model_Translations
        extends Zend_Db_Table
{

   protected $_name="translations";

   public function lookFor($sWord)
   {
       $oFilter = new Core_Filter_Ogonki();

       $sWordFiltered = $oFilter->filter($sWord);
       $sWord = strtolower($sWord);
       
       $oSelect = $this->select();
       $oSelect->from($this->_name,array("id","pt","pl","note"));
       $oSelect->where("LOWER(pl) LIKE ?","%".$sWord."%");
       $oSelect->orWhere("LOWER(pt) LIKE ?","%".$sWord."%");
       $oSelect->orWhere("LOWER(filtered_pl) LIKE ?","%".$sWordFiltered."%");
       $oSelect->orWhere("LOWER(filtered_pt) LIKE ?","%".$sWordFiltered."%");
       $oSelect->limit(50);
       $oSelect->order("note DESC");

       return $this->fetchAll($oSelect);
   }
   public function lastInsertByIp($sIp)
   {
       $oSelect = $this->select();
       $oSelect->from($this->_name,array("date_added"));
       $oSelect->where("ip = ?",$sIp);
       $oSelect->order('date_added DESC');
       $oSelect->limit(1);

       return $this->fetchRow($oSelect);
   }
}

