<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("includes/simpleBar.php");
require_once ("includes/simpleAlphaBar.php");
require_once ("includes/rectangle.php");
require_once ("includes/rectangleLabel.php");

class simpleBarChart extends Chart {
    
    function simpleBarChart() {
        $this->border = new Border ();
        $this->background = new Background ();
        //$this->background->set ("fillColor", new Color(230, 230, 230));
    }    
    
    function render ($canvas) {
        $width = $this->width;
        $height = $this->height;
        $img = imagecreatetruecolor ($width, $height);

        

        $this->background->render ($img, $width, $height);
        $this->drawAxii ($img);

        $this->border->render ($img, $width, $height);
        $maxValue = null;
        $maxKey = null;
        $numericData = $this->data[0]->get ("data");

        $maxValue = $this->dataSeries->getMaxValueForAxis ($this->dataSeries->getMaxDatasetValue ('values'));
        
        $num = count ($this->data[0]->get ("data"));
        list($kMaj, $kMin, $vMaj, $vMin) = $this->dataSeries->getSteps ();
        
        if ($num != 0) {
            
            $x = $this->axisMargins->get("left");
            $y = $height-$this->axisMargins->get("bottom");
            $axii = $this->axii;
            $keyAxis = $this->getKeyAxis();
            $valueAxis = $this->getValueAxis();
            $barWidth = $keyAxis->length()*$kMaj/100;
            $delta = $barWidth;
            $i = 0;
            foreach ($numericData as $d_key => $d_value) {
                $color = $this->data[0]->get ("color");
                
                $rheight = $d_value*($valueAxis->length())/$maxValue;
                                    
                $bar = new SimpleAlphaBar ($x + $barWidth * $i + 10, $y, $barWidth-20, $rheight, $color, $this->alpha);
                $bar->render ($img);

                $i++;
            }            
            
        }
       
        return $img;
    }
}

?>