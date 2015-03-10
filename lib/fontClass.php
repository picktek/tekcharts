<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");


define (TC_FONT_NORMAL, 0);
define (TC_FONT_BOLD, 1);
define (TC_FONT_ITALIC, 2);
define (TC_FONT_UNDERLINE, 3);

/**
 * Class which represents fonts in TekCharts.
 * Font can be either GD font or TruType font.
 *
 */
class Font extends ChartBean 
{
    /**
     * Default constructor
     *
     * @param object $font
     * If $font is type of int then GD font is constructed internally, otherwise TTF font.
     * @return Font
     */
    function Font ($font) {
        
        if (is_a ($font, "Font")) {
            $this->size = $font->size;
            $this->weight = $font->weight;
            $this->family = $font->family;
            $this->trueTypeFile = $font->trueTypeFile;
        }
        else    
        if (is_int ($font) || is_null ($font)) {
            
            $this->size = (is_null($font)?5:intval ($font)); 
            $this->trueTypeFile = "";
            $this->family = "system";
        }
        else {
            $this->trueTypeFile = $font;
        }
    }
    
    /**
     * Property: size
     *
     * @var float
     */
    var $size = 5;
    /**
     * Property: fint weight
     *
     * @var int
     */
    var $weight = TC_FONT_NORMAL;
    /**
     * Property: family
     *
     * @var String
     */
    var $family = "system";    
    /**
     * Property:TTF file name if any
     *
     * @var String
     */
    var $trueTypeFile = "";    

    
    /**
     * Returns GD font for usage in imagestring() function
     * Returns 1-5 for PHP built-in fonts.
     * And file name for truetype fonts.
     */
    function getFont () {
        if (!$this->isTtf ()) {
            return $this->size;
        }
        else {
            return $this->trueTypeFile;
        }
    }
    
    /**
     * Returns true if this Font instance represents TTF
     *
     * @return boolean
     */
    function isTtf () {
        if (strlen($this->trueTypeFile) > 0)
            return true;
            
        return false;
    }
}

?>