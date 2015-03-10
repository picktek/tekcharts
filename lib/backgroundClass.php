<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");
require_once ("colorClass.php");
/**
 * Class which represents visible border for various TekCharts objects
 *
 */
class Background extends ChartBean 
{
    /**
     * Default constructor
     *
     * @return Background
     */
    function Background () {
        $this->fillColor = new Color ("ffffff");
    }

    /**
     * Property: visible
     *
     * @var boolean
     */
    var $visible = true;
    /**
     * Property: fill color
     *
     * @var Color
     */
    var $fillColor = null;
    /**
     * Property: fill color opacity
     *
     * @var int
     */
    var $fillColorAlpha = 100;
    /**
     * Property: background image
     *
     * @var Image
     */
    var $image = null;
    /**
     * Property: image opacity
     *
     * @var int
     */
    var $imageAlpha = 100;

    /**
     * Draw background
     *
     * @param image $img
     * @param int $width
     * @param int $height
     */
    function render ($img, $width, $height) {
        if ($this->visible) {
            $bgColor = $this->fillColor;
            
            if ($bgColor != null) {
                $r = $bgColor->get ("r"); $g = $bgColor->get ("g"); $b = $bgColor->get ("b");
            }
            else { $r = $g = $b = 255; }
            
            $clr = ImageColorAllocate ($img, $r, $g, $b);
            imagefilledrectangle($img, 0, 0, $width, $height, $clr);        

        }
    }
}

?>