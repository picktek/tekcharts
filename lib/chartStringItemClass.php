<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");
require_once ("colorClass.php");
require_once ("fontClass.php");
require_once ("utility.php");

define (TC_SI_ANGLE_NORMAL, 0);
define (TC_SI_ANGLE_90, -90);
define (TC_SI_ANGLE_45, -45);

/**
 * Class which represents strings drawn on various chart parts
 * Should be used everywhere instead of direct string rendering.
 */
class ChartStringItem extends ChartBean 
{
    /**
     * Default constructor
     *
     * @param string $text
     * @param Font $font
     * @return ChartStringItem
     */
    function ChartStringItem ($text, $font) {
        $this->color = new Color ("000000");
        $this->font = new Font ($font);
        $this->text = $text;
    }

    /**
     * Property: visible
     *
     * @var boolean
     */
    var $visible = true;
    /**
     * Property: color
     *
     * @var Color
     */
    var $color = null;
    /**
     * Property: opacity
     *
     * @var int
     */
    var $alpha = 100;
    /**
     * Property: font
     *
     * @var Font
     */
    var $font = 5;
    /**
     * Property: text
     *
     * @var String
     */
    var $text = "";
    
    /**
     * Property: angle
     *
     * @var unknown_type
     */
    var $angle = TC_SI_ANGLE_NORMAL;

    /**
     * Width of string item
     *
     * @return int
     */
    function width () {
        if ($this->font->isTtf ()) {
            $width = ttffontwidth ($this->font->get ("size"), $this->font->getFont (), $this->text, $this->angle);
        }
        else {
            $width = imagefontwidth ($this->font->getFont ()) * strlen (strval($this->text));
        }
        
        return $width;
    }
    
    /**
     * Height of string item
     *
     * @return int
     */
    function height () {
        if ($this->font->isTtf ()) {
            $height = ttffontheight ($this->font->get ("size"), $this->font->getFont (), $this->text, $this->angle);
        }
        else {
            $height = imagefontheight ($this->font->getFont ());
        }        
        
        return $height;
    }
    
    /**
     * X coordinate to draw string item centered by X-axis
     *
     * @param int $width
     * @return int
     */
    function centerX ($width) {
        return ($width - $this->width ())/2;
    }
    
    /**
     * Renders string item on specified image.
     *
     * @param Image $img
     * @param int $x
     * @param int $y
     * @return $img
     */
    function render ($img, $x, $y) {
        if ($this->visible && strlen ($this->text) > 0) {
            if ($this->font->isTtf()) {
                drawttftext($img, $this->font->get("size"), $x, $y, 
                            $this->color->get("r"), $this->color->get("g"), $this->color->get("b"), 
                            $this->font->getFont(), $this->text, $this->angle);
            }
            else {
                if (round($this->angle) == 0) {
                    $color = $this->color;
                    $r = $color->get ("r"); $g = $color->get ("g"); $b = $color->get ("b");
                    $clr = imagecolorallocate ($img, $r, $g, $b);
            
                    imagestring ($img, $this->font->getFont(), $x, $y, $this->text, $clr);
                }
                else {
                    $trans = imagecolorallocate ($img, 255, 255, 255);
                    $im = imagecreatetruecolor ($this->width(), $this->height ());

                    imagealphablending($im, false);
                    imageantialias($im, false);
                    imagecolortransparent ($im, $trans);
                    
                    imagefilledrectangle($im, 0, 0, imagesx ($im), imagesy ($im), $trans);
                    
                    $color = $this->color;
                    $r = $color->get ("r"); $g = $color->get ("g"); $b = $color->get ("b");
                    $clr = imagecolorallocate ($img, $r, $g, $b);
            
                    imagestring ($im, $this->font->getFont(), 0, 0, $this->text, $clr);                    
                    $im = imagerotate($im, $this->angle, $trans);
                    imagecolortransparent ($im, $trans);
                    
                    imagecopymerge ($img, $im, $x+$this->width()/2-$this->height()/2, $y, 0, 0, imagesx ($im), imagesy ($im), $this->alpha);
                }
            }
            
            return $img;
        }
        
        return $img;
    }
}

?>