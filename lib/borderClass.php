<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");

define (TC_BORDER_SOLID, 0);
define (TC_BORDER_DASHED, 1);
define (TC_BORDER_DOTTED, 2);
/**
 * Class which represents visible border for various TekChart objects
 *
 */
class Border extends ChartBean 
{
    /**
     * Default consructor
     *
     * @return Border
     */
    function Border () {
        $this->color = new Color ("000000");
    }
    
    /**
     * Property: visible
     *
     * @var boolean
     */    
    var $visible = true;
    /**
     * Property: thickness
     *
     * @var int
     */
    var $thickness = 1;
    
    /**
     * Property: border color
     *
     * @var Color
     */
    var $color = null;

    /**
     * Property: style - TC_BORDER_SOLID, TC_BORDER_DASHED, TC_BORDER_DOTTED
     *
     * @var int
     */
    var $style = TC_BORDER_SOLID;
    
    /**
     * Enter description here...
     *
     * @param image $img
     * @param int $width
     * @param int $height
     */
    function render ($img, $width, $height) {
        if ($this->visible) {
            $bgColor = $this->color;
            $r = $bgColor->get ("r"); $g = $bgColor->get ("g"); $b = $bgColor->get ("b");
            $clr = ImageColorAllocate ($img, $r, $g, $b);
            
            if ($this->style != TC_BORDER_SOLID) {
                $w = imagecolorallocate ($img, 255, 255, 255);
                if ($this->style == TC_BORDER_DASHED)
                    $style = array($clr, $clr, $clr, $clr, $w, $w, $w, $w);
                else
                    $style = array($clr, $clr, $w, $w);
                
                imagesetstyle ($img, $style);
                $clr = IMG_COLOR_STYLED;
            }
            
            imagerectangle ($img, 0, 0, $width - 1, $height -1, $clr);
        }
    }
}

?>