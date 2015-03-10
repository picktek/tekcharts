<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/

define('SIMPLE_SHADOW',    .25);     /** Shadow Color is 15% darker */
define('SIMPLE_HILITE',    .25);     /** Highlight Color is 15% brighter */

class  SimpleBar {
    
    /**
     * Attention: $x and $y are the coordinates of the bottom
     * left point of the bar. 
     *
     * @param int $x
     * @param int $y
     * @param int $height
     * @param int $width
     * @param Color $color
     */
    var $x, $y;
    var $height, $width;
    
    var $color, $shadow, $hilite;  
    
    function SimpleBar ($x, $y, $width, $height, $color) {
        
        $this->x = $x;
        $this->y = $y;
        $this->height=$height;
        $this->width=$width;
        $this->color = $color;  

        $this->getHiLoColors();
    }
    
    
    function render ($img) {
        $rect = $this->getRectangle ();
        
        //-- The body
        $clr = $this->color;       
        $tmp_clr = imagecolorallocate ($img, $clr->r, $clr->g, $clr->b);    
        imagefilledrectangle($img, $rect->x1, $rect->y1, $rect->x2, $rect->y2, $tmp_clr);
    }
    
    function getRectangle() {
        $x1 = $this->x;
        $x2 = $this->x + $this->width;
        
        $y1 = $this->y;
        $y2 = $this->y - $this->height;
        
        return new Rectangle ($x1, $y1, $x2, $y2, $color);        
    }
    
    /**
     * Calculate colors for shadow and highlight
     */
    function getHiLoColors() {
        $r = $this->color->r;
        $g = $this->color->g;
        $b = $this->color->b;
        
        $rsh = $r * (1 - THREED_SHADOW);       
        $gsh = $g * (1 - THREED_SHADOW);       
        $bsh = $b * (1 - THREED_SHADOW);
               
        $rhi = $r * (1 + THREED_HILITE);       
        $ghi = $g * (1 + THREED_HILITE);       
        $bhi = $b * (1 + THREED_HILITE);       
        
        $this->shadow = new Color( $rsh, $gsh, $bsh);
        $this->hilite = new Color( $rhi, $ghi, $bhi);
    }
    
    
    function printme () {
        logit ( $this ) ;
    }
    
}
?>