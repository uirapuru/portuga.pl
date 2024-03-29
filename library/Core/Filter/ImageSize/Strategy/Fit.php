<?php

/**
 * @see Core_Filter_ImageSize_Strategy_Interface
 */
require_once 'Core/Filter/ImageSize/Strategy/Interface.php';

/**
 * Strategy for resizing the image by fitting the content into the given 
 * dimensions.
 */
class Core_Filter_ImageSize_Strategy_Fit
    implements Core_Filter_ImageSize_Strategy_Interface
{
    /**
     * Return canvas resized according to the given dimensions.
     * @param resource $image GD image resource
     * @param int $width Output width
     * @param int $height Output height
     * @return resource GD image resource
     */
    public function resize($image, $width, $height)
    {
        $origWidth = imagesx($image);
        $origHeight = imagesy($image);
        
        $rWidth = ceil($origWidth / $width);
        $rHeight = ceil($origHeight / $height);

        if($rWidth > $rHeight) {
            $ratio = $rWidth;
        } else {
            $ratio = $rHeight;
        }
        
        $newWidth = $origWidth / $ratio;
        $newHeight = $origHeight / $ratio;    
        
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        
        return $resized;
    }
}