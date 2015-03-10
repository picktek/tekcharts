<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("includes/3dbar.php");
require_once ("includes/rectangle.php");
require_once ("includes/rectangleLabel.php");


class threeDBarChart extends Chart {
    
    var $depth = 0;
    
    function threeDBarChart() {
        $this->border = new Border ();
        $this->background = new Background ();
        $this->background->set ("fillColor", new Color(230, 230, 230));
    }    
    
    function render ($canvas) {
        $width = $this->width;
        $height = $this->height;
        $img = imagecreatetruecolor ($width, $height);
        
        $this->background->render ($img, $width, $height);
        $this->border->render ($img, $width, $height);
        
        
        $num = count ($this->data[0]->get ("data"));
        if ($num != 0) {
            
            $boardColor = "454542";
            $backColor = "aeaeae";
    
            $bw = 70;
            $bh = 5;

            $boardWidth = $width - $bw;
            
            $barWidth = ($boardWidth - 2*5)/$num - 5;
            $delta = $barWidth + 5;
           
            $x = 5*4 + $barWidth/2; $y = $height - 5;        
                    
            
            //-- Board
            $color = new Color ($boardColor);
            $bar = new ThreeDBar (($width - $bw * acos (THREED_ANGLE))/2, $y, $boardWidth, $bh, $color, $bw);
            $bar->render ($img);
            
            //-- Back
            $color = new Color ($backColor);
            $bar = new ThreeDBar (($width + $bw * acos (THREED_ANGLE))/2, $y-$bw * asin (THREED_ANGLE) - $bh, $width - $bw, $height - $bw, $color,0);
            $bar->render ($img);
            
            //-- Bars
            $maxValue = null;
            $maxKey = null;
            $numericData = $this->data[0]->get ("data");
            foreach ($numericData as $d_key => $d_value) {
                if (($maxValue == null) || ($d_value > $maxValue)) {
                    $maxValue = $d_value;
                    $maxKey = $d_key;
                }
            }

            $i = 0;
            foreach ($numericData as $d_key => $d_value) {
                $color = $this->data[0]->get ("color");
                $rheight = $d_value*($height - $bw - 5)/$maxValue;
                $bar = new ThreeDBar ($x + $i * $delta, $y - $bh *2, $barWidth, $rheight, $color, $bw - 2*5);
                $bar->render ($img);
              
                $label = new RectangleLabel ($barWidth - 4, -1, $d_key);
                $labelImg = $label->render ();
                
                imagecopymerge($img, $labelImg, $x + $i * $delta + 2 - $barWidth/2, $y - $bh *2 - 2 - $barWidth/2, 0, 0, imagesx ($labelImg), imagesy ($labelImg), $label->alpha);
                
                $i++;
            }            
            
        }
        return $img;
    }
}

?>