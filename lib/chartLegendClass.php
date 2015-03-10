<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");
require_once ("colorClass.php");

define ("TC_LEGEND_TEXT_PADDING", 3);
define ("TC_TEXT_RECT_PADDING", 5);
define ("TC_LEGEND_TEXT_SPACING", 5);
define ("TC_LEGEND_RECT_WIDTH", 5);
/**
 * Class which represents legend of chart.
 * Legend is drawn on Canvas mainly.
 */
class ChartLegend extends ChartBean 
{
    /**
     * Default constructor
     *
     * @return ChartLegend
     */
    function ChartLegend () {
        $this->background = new Background();
        $this->border = new Border();
        $this->font = new Font (2);
        $this->textColor = new Color(0, 0, 0);
    }

    /**
     * Property: visible
     *
     * @var boolean
     */
    var $visible = true;
    /**
     * Property: background
     *
     * @var Background
     */
    var $background = null;
    /**
     * Property: border
     *
     * @var Border
     */
    var $border = null;
    /**
     * Property: legend opacity
     *
     * @var int
     */
    var $alpha = 100;
    /**
     * Property: legend font
     *
     * @var Font
     */
    var $font = 2;
 
    /**
     * Property: color of legend text
     * Defaults to black.
     * @var Color
     */
    var $textColor = null;
    
    /**
     * Controls whether legend items are drawn vertically or horizontally
     *
     * @var boolean
     */
    var $vertical = true;
    
    /**
     * Returns width of legend
     *
     * @param array() $data
     * array of ChartDataset classes
     * @return int
     */
    function width ($data) {
        if ($this->vertical) {
            $lm = 0;
            
            foreach ($data as $dataset) {
                $item = new ChartStringItem($dataset->get ("name"), $this->font);
                $len = $item->width ();
                if ($len > $lm) $lm = $len;
            }
            
            $total = $lm + TC_LEGEND_TEXT_PADDING * 2 + TC_LEGEND_RECT_WIDTH + TC_TEXT_RECT_PADDING + $this->border->get ("thickness") * 2;
        }
        else {
            $lm = 0;
            $i = 0;
            foreach ($data as $dataset) {
                $item = new ChartStringItem($dataset->get ("name"), $this->font);
                $len = $item->width ();
            
                $lm += $len + TC_LEGEND_TEXT_SPACING+ TC_LEGEND_RECT_WIDTH + TC_TEXT_RECT_PADDING ;
                $i++;
            }
            
            $total = ($lm ) + TC_LEGEND_TEXT_PADDING * 2 + $this->border->get ("thickness") * 2;
        }
        
        return $total;
    }

    /**
     * Returns height of legend
     *
     * @param array() $data
     * array of ChartDataset classes
     * @return int
     */
    function height ($data) {
        if ($this->vertical) {
            $hm = 0; $i = 0;
            
            foreach ($data as $dataset) {
                $item = new ChartStringItem($dataset->get ("name"), $this->font);
                $len = $item->height ();
                if ($len > $hm) $hm = $len;
                $i++;
            }
            
            $total = (($hm+TC_LEGEND_TEXT_SPACING)*$i + TC_LEGEND_TEXT_PADDING * 2) + $this->border->get ("thickness") * 2;
        }
        else {
            $hm = 0; $i = 0;
            
            foreach ($data as $dataset) {
                $item = new ChartStringItem($dataset->get ("name"), $this->font);
                $len = $item->height ();
                if ($len > $hm) $hm = $len;
                $i++;
            }
            
            $total = ($hm + TC_LEGEND_TEXT_PADDING * 2) + $this->border->get ("thickness") * 2;            
        }
        return $total;
    }
    
    /**
     * Draw legend
     *
     * @param array() $data
     *  Array of ChartDataset objects.
     * @return Image
     */
    function render ($data) {
        if ($this->visible) {
            $width = $this->width ($data);
            $height = $this->height ($data);
            $img = imagecreatetruecolor ($width, $height);
    
            // draw bg
            $this->background->render ($img, $width, $height);
            // draw border
            $this->border->render ($img, $width, $height);    
            
            $i = 0; 
            if ($this->vertical) {
                $h = $this->border->get("thickness") + TC_LEGEND_TEXT_PADDING;
                $wt = $this->border->get("thickness") + TC_LEGEND_TEXT_PADDING + TC_TEXT_RECT_PADDING + TC_LEGEND_RECT_WIDTH;
                $wr = $this->border->get("thickness") + TC_LEGEND_TEXT_PADDING;
            }
            else {
                $h = $this->border->get("thickness") + TC_LEGEND_TEXT_PADDING + TC_TEXT_RECT_PADDING + TC_LEGEND_RECT_WIDTH;
                $wt = $this->border->get("thickness") + TC_LEGEND_TEXT_PADDING + TC_TEXT_RECT_PADDING + TC_LEGEND_RECT_WIDTH;
                $wr = $this->border->get("thickness") + TC_LEGEND_TEXT_PADDING;
            }
                
            
            foreach ($data as $dataset) {
                $dataColor = $dataset->get ("color");
                
                if ($dataColor != null) {
                    $r = $dataColor->get ("r"); $g = $dataColor->get ("g"); $b = $dataColor->get ("b");
                }
                else 
                    $dataColor = new Color(0, 0, 0); 
                
                $clr = imagecolorallocate ($img, $r, $g, $b);
                $item = new ChartStringItem ($dataset->get("name"), $this->font);

                $item->set("color", $this->textColor);
                $textclr = imagecolorallocate($img, $this->textColor->get("r"), $this->textColor->get("g"), $this->textColor->get("b"));
                
                if ($this->vertical) {
                    $item->render ($img, $wt, $h);
                    imagefilledrectangle($img, $wr, $h, $wr + TC_LEGEND_RECT_WIDTH, $h + $item->height (), $clr);        
                    imagerectangle($img, $wr, $h, $wr + TC_LEGEND_RECT_WIDTH, $h + $item->height (), $textclr);        
    
                    $h += $item->height () + TC_LEGEND_TEXT_SPACING;
                    $i++;
                } 
                else {
                    $item->render ($img, $h, $wr);
                    imagefilledrectangle($img, $h-TC_LEGEND_RECT_WIDTH-TC_TEXT_RECT_PADDING, $wr, $h-TC_TEXT_RECT_PADDING, $wr + $item->height (), $clr);        
                    imagerectangle($img, $h-TC_LEGEND_RECT_WIDTH-TC_TEXT_RECT_PADDING, $wr, $h-TC_TEXT_RECT_PADDING, $wr + $item->height (), $textclr);        
                    
                    $h += $item->width () + TC_LEGEND_TEXT_SPACING + TC_LEGEND_RECT_WIDTH  + TC_TEXT_RECT_PADDING;
                    $i++;                    
                }
            }

            return $img;
        }
        
        return $img;
    }
}

?>