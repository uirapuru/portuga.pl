<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Core_Filter_Seo implements Zend_Filter_Interface
{

    public function filter($value)
    {

        $value = mb_strtolower((string) $value, "UTF-8");

        return preg_replace(
            array(
                "/ó/", "/ł/", "/ż/", "/ź/", "/ę/", "/ą/", "/ć/", "/ś/", "/ń/",
                "/([^a-z0-9_\s])/",
                "/\s{1,}/"
            ),
            array(
                "o", "l", "z", "z", "e", "a", "c", "s", "n",
                " ",
                "_"
            ),
            $value
        );
    }

}

?>
