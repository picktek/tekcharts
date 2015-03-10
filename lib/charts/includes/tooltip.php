<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
class Tooltip {
    
    var $border, $background, $textColor, $stroke;
    var $offset = 10;
    var $alpha = 60;
    var $stringItem = null;
    
    function Tooltip ($offset, $text, $font, $color, $borderColor, $backgroundColor, $strokeColor) {
        $font = new Font ($font);
        $this->stringItem = new ChartStringItem ($text, $font);
        $this->offset = $offset;
        $this->background = $backgroundColor;
        $this->border = $borderColor;
        $this->textColor = $color;
        $this->stroke = $strokeColor;
        $this->stringItem->set ("color", $this->textColor);
    }
    
    /**
     * Render tooltip
     *
     * @param Image $img
     * @param float $relx
     *  Point coordinates relative to which tooltip will be drawn
     * @param float $rely
     *  Point coordinates relative to which tooltip will be drawn
     * @param float $dirx
     *  Direction in which tooltip will be heading
     * @param float $diry
     *  Direction in which tooltip will be heading
     * @return void
     */
    function render ($img, $relx, $rely, $dirx, $diry) {
        $dirlen = sqrt($dirx*$dirx+$diry*$diry);
        /* normalize direction vector */
        $nx = $dirx/$dirlen;
        $ny = $diry/$dirlen;
        
        /* get offset vector */
        $ox = $nx*$this->offset;
        $oy = $ny*$this->offset;
        
        /* tooltip upper left corner coords*/
        $tx = $ty = 0;
        $pad = TC_TEXT_RECT_PADDING;
        $w = $this->stringItem->width () + $pad*2;
        $h = $this->stringItem->height () + $pad*2;;

        
        if ($nx >= 0) {
            if ($ny >= 0) {
                $tx = $relx + $ox;
                $ty = $rely + $oy;
            }
            else {
                $tx = $relx + $ox;
                $ty = $rely + $oy - $h;
            }
        }
        else {
            if ($ny >= 0) {
                $tx = $relx + $ox - $w;
                $ty = $rely + $oy;
            }
            else {
                $tx = $relx + $ox - $w;
                $ty = $rely + $oy - $h;
            }
        }

        $backColor = imagecolorallocate($img, $this->background->get("r"), 
                                              $this->background->get("g"), 
                                              $this->background->get("b"));
        $bordColor = imagecolorallocate($img, $this->border->get("r"), 
                                              $this->border->get("g"), 
                                              $this->border->get("b"));
        $strokeColor = imagecolorallocate($img, $this->stroke->get("r"), 
                                                $this->stroke->get("g"), 
                                                $this->stroke->get("b"));                                              
        imagefilledrectangle ($img, $tx, $ty, $tx + $w-1, $ty + $h-1, $backColor);
        imagerectangle($img, $tx, $ty, $tx + $w-1, $ty + $h-1, $bordColor);
        $this->stringItem->render ($img, $tx + $pad, $ty + $pad);
        imageline ($img, $relx, $rely, $relx + $ox, $rely + $oy, $strokeColor);
        
        return $img;
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $img
     * @param unknown_type $relx
     * @param unknown_type $rely
     * @param unknown_type $qNum
     *  Quadrant number
     *   2 | 1
     *   -----
     *   3 | 4
     * @return unknown
     */
    function renderByQuadrant ($img, $relx, $rely, $qNum) {
        switch ($qNum) {
            case 1:
                return $this->render($img, $relx, $rely, 0.5, -0.5);
                break;
            case 2:
                return $this->render($img, $relx, $rely, -0.5, -0.5);
                break;
            case 3:
                return $this->render($img, $relx, $rely, -0.5, 0.5);
                break;
            case 4:
                return $this->render($img, $relx, $rely, 0.5, 0.5);
                break;
        }
    }
}

?>