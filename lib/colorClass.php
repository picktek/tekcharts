<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");
/**
 * Class which represents color,
 * Contants helper utility to allow constructing colors in "xxyyzz" string form.
 *
 */
class Color extends ChartBean {
    var $r, $g, $b;
    
    /**
     * R, G and B parameters are in Decimal
     * not Hexadecimal, because PHP/GD require
     * decimal values :-(
     *
     * @param int $r
     * @param int $g
     * @param int $b
     * @return Color
     */
    function Color ($r = NULL, $g = NULL, $b = NULL) {
        
        if ($r != null && $g == null && $b == null && is_string ($r)) { //Hexadecimal constructor
            list ($r, $g, $b) = $this->ColorHexToDec ($r);
        }
        else if ($r!=null && $g!=null & $b != null) {
	        if ($r < 0) $r = 0; if ($r>255) $r =255;
	        if ($g < 0) $g = 0; if ($g>255) $g =255;
	        if ($b < 0) $b = 0; if ($b>255) $b =255;
        }
        
        $this->r = (int)$r;
        $this->g = (int)$g;
        $this->b = (int)$b;        
    }
    
    /**
     * Convert rrggbb string to array of color components
     *
     * @param string $color
     * In rrggbb format
     * @return array(rr, gg, bb)
     */
    function ColorHexToDec ($color) {
        return array (hexdec (substr ($color, 0, 2)), hexdec (substr ($color, 2, 2)), hexdec (substr ($color, 4, 2)));
    }
    
    
}
?>