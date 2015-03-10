<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");

define (TC_LINE_STYLE_SOLID, 0);
define (TC_LINE_STYLE_DASHED, 1);
define (TC_LINE_STYLE_DOTTED, 2);
define (TC_LINE_MARKS_CW, 1); // clockwise marks
define (TC_LINE_MARKS_CCW, 2); // counter-clockwise marks

class LineStyle extends ChartBean 
{
    /**
     * Default constructor.
     *
     * @return LineStyle
     */
    function LineStyle () {
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
     * Property: style 
     *
     * @var int
     */
    var $style = TC_LINE_STYLE_SOLID;
    

    /**
     * Drwa line using this LineStyle
     *
     * @param Image $img
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     */
    function drawLine ($img, $x1, $y1, $x2, $y2) {
        $clr = imagecolorallocate ($img, $this->color->get ("r"), $this->color->get ("g") , $this->color->get ("b"));
                    
        if ($this->style != TC_LINE_STYLE_SOLID) {
            $w = imagecolorallocate ($img, 255, 255, 255);
            if ($this->style == TC_LINE_STYLE_DASHED)
                $style = array($clr, $clr, $clr, $clr, $w, $w, $w, $w);
            else
                $style = array($clr, $clr, $w, $w);
            
            imagesetstyle ($img, $style);
            $clr = IMG_COLOR_STYLED;
        }
            
        imageline ($img, $x1, $y1, $x2, $y2, $clr);
    }
    
}

?>