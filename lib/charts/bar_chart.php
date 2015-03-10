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

/**
 * Bar chart which can display multiple data sets and be either of horizontal or vertical alignment
 *
 */
class barChart extends Chart {
    
    function barChart() {
        $this->border = new Border ();
        $this->background = new Background ();
        $this->background->set ("fillColor", new Color(255, 255, 255));
    }    
    
    var $barSpacing = 4;
    var $markOffset = 10;
    var $plotBallSize = 10;
    var $showDropDownLines = false;    
    var $dropDownColor = null;    
    var $showShadow =false;
    var $shadowOffset = 3;    
    
    function render ($canvas) {
        $width = $this->width;
        $height = $this->height;
        $img = imagecreatetruecolor ($width, $height);
        $this->background->render ($img, $width, $height);

        $this->drawAxii ($img);
        $this->border->render ($img, $width, $height);
        
        ////////////////////
        $maxValue = $this->dataSeries->getMaxValueForAxis ($this->dataSeries->getMaxDatasetValue ('values'));

        $keyAxis = $this->getKeyAxis ();
        $valueAxis = $this->getValueAxis ();
        $horizontal = false;
        
        if ($keyAxis->getAxisAlignment () == TC_AXIS_VERTICAL) {
            $horizontal = true;
        }
        
        $maxLength = $this->dataSeries->getKeysMaxLength ();
        list($kMaj, $kMin, $vMaj, $vMin) = $this->dataSeries->getSteps ();
        
        // $numberOfSets = count ($this->data);
        $numberOfSets = 0;
        foreach ($this->data as $ds) {
            if ($ds->get("renderAs") == "bars")
                $numberOfSets++;
        }
        
        $dsCount = count ($this->data);
        ////////////////////
        
        imageantialias($img, $this->antiAlias);
        $totalBarWidth = $keyAxis->length()*$kMaj/100;
        
        // render bars
        if ($numberOfSets != 0) {
            
            $oneBarWidth = ($totalBarWidth-$this->markOffset*2)/$numberOfSets - $this->barSpacing;

            if (!$horizontal) {
                for ($i = 0; $i < $dsCount; $i++) {
                    $dataset = $this->data[$i];
                    if ($dataset->get("renderAs") != "bars")
                        continue;

                    $j = 0;

                    if ($keyAxis->inverse)
                        $x = $width - $this->axisMargins->get("right") - ($this->markOffset + ($oneBarWidth + $this->barSpacing) * $i + $this->barSpacing*0.5);
                    else
                        $x = $this->axisMargins->get("left") + $this->markOffset + ($oneBarWidth + $this->barSpacing) * $i + $this->barSpacing*0.5;

                    $y = $height-$this->axisMargins->get("bottom")-1;
                    
                        
                    $data = $dataset->get("data");
                    foreach ($data as $d_key => $d_value) {
                        $color = $dataset->get ("color");
                        
                        $rheight = $d_value*($valueAxis->length())/$maxValue;
                                  
                        if ($keyAxis->inverse)          
                            $bar = new SimpleAlphaBar ($x - $totalBarWidth * $j, $y, -$oneBarWidth, round ($rheight), $color, $this->alpha);
                        else
                            $bar = new SimpleAlphaBar ($x + $totalBarWidth * $j, $y, $oneBarWidth, round ($rheight), $color, $this->alpha);
                        $bar->render ($img);
        
                        $j++;                
                    }
                    
                }
            }
            else {
                for ($i = 0; $i < $dsCount; $i++) {
                    $dataset = $this->data[$i];
                    if ($dataset->get("renderAs") != "bars")
                        continue;
                    
                    $j = 0;
                    $x = $this->axisMargins->get("left") + 1;
    
                    if ($keyAxis->inverse)
                        $y = $this->axisMargins->get("top")  + $this->markOffset + ($oneBarWidth + $this->barSpacing) * $i + $this->barSpacing*0.5;
                    else 
                        $y = $height-$this->axisMargins->get("bottom") - ($this->markOffset + ($oneBarWidth + $this->barSpacing) * $i + $this->barSpacing*0.5);
                    
                        
                    $data = $dataset->get("data");
                    foreach ($data as $d_key => $d_value) {
                        $color = $dataset->get ("color");
                        
                        $rheight = $d_value*($valueAxis->length())/$maxValue;
                                            
                        if ($keyAxis->inverse)
                            $bar = new SimpleAlphaBar ($x, $y + $totalBarWidth * $j, round ($rheight), -$oneBarWidth, $color, $this->alpha);
                        else
                            $bar = new SimpleAlphaBar ($x, $y - $totalBarWidth * $j, round ($rheight), $oneBarWidth, $color, $this->alpha);
                        $bar->render ($img);
        
                        $j++;                
                    }
                    
                }            
            }
        }
        
        // render plots
        if (!$horizontal) {
            for ($i = 0; $i < $dsCount; $i++) {
                $dataset = $this->data[$i];
                if ($dataset->get("renderAs") != "plot")
                    continue;

                $j = 0;

                if ($keyAxis->inverse)
                    $x = $width - $this->axisMargins->get("right") - $totalBarWidth/2;
                else
                    $x = $this->axisMargins->get("left") + $totalBarWidth/2;
                    
                $y = $height-$this->axisMargins->get("bottom")-1;
                
                $prevX = null;
                $prevY = null;    
                $data = $dataset->get("data");
                foreach ($data as $d_key => $d_value) {
                    $color = $dataset->get ("color");
                    
                    $rheight = $d_value*($valueAxis->length())/$maxValue;
                              
                    if ($keyAxis->inverse) {
                        $newX = $x - $totalBarWidth*$j;
                        $newY = $y - round ($rheight);
                    }
                    else {
                        $newX = $x + $totalBarWidth*$j;
                        $newY = $y - round ($rheight);
                    }
                    
                    $clr = imagecolorallocate($img, $color->get("r"), $color->get("g"), $color->get("b"));
                    $clr2 = imagecolorallocate($img, 200,  200,  200);
                    if ($prevX != null && $this->showShadow) {
                        imageline($img, $prevX+$this->shadowOffset, $prevY+$this->shadowOffset, $newX+$this->shadowOffset, $newY+$this->shadowOffset, $clr2);
                        imageline($img, $prevX+1+$this->shadowOffset, $prevY+$this->shadowOffset, $newX+1+$this->shadowOffset, $newY+$this->shadowOffset, $clr2);                        
                        imageline($img, $prevX+$this->shadowOffset, $prevY-1+$this->shadowOffset, $newX+$this->shadowOffset, $newY-1+$this->shadowOffset, $clr2);
                        imageline($img, $prevX-1+$this->shadowOffset, $prevY+$this->shadowOffset, $newX-1+$this->shadowOffset, $newY+$this->shadowOffset, $clr2);
                        imageline($img, $prevX+$this->shadowOffset, $prevY+1+$this->shadowOffset, $newX+$this->shadowOffset, $newY+1+$this->shadowOffset, $clr2);
                    }
                    if ($prevX != null) {
                        imageline($img, $prevX, $prevY, $newX, $newY, $clr);
                        imageline($img, $prevX+1, $prevY, $newX+1, $newY, $clr);                        
                        imageline($img, $prevX, $prevY-1, $newX, $newY-1, $clr);
                        imageline($img, $prevX-1, $prevY, $newX-1, $newY, $clr);
                        imageline($img, $prevX, $prevY+1, $newX, $newY+1, $clr);
                    }
                    
                    if ($this->showDropDownLines && $j % $keyAxis->get("labelFilter") == 0) {
                        if ($this->dropDownColor == null)
                            $ddclr = $clr;
                        else 
                            $ddclr = imagecolorallocate($img, $this->dropDownColor->r, $this->dropDownColor->g, $this->dropDownColor->b);
                        imageline($img, $newX, $newY, $newX, $newY+ round ($rheight) + $this->markOffset, $ddclr);
                    }
                    
                    $prevX = $newX;
                    $prevY = $newY;
                    $j++;                
                }
                
                $prevX = null;
                $prevY = null;    
                $j = 0;
                $data = $dataset->get("data");
                foreach ($data as $d_key => $d_value) {
                    $color = $dataset->get ("color");
                    
                    $rheight = $d_value*($valueAxis->length())/$maxValue;
                              
                    if ($keyAxis->inverse) {
                        $newX = $x - $totalBarWidth*$j;
                        $newY = $y - round ($rheight);
                    }
                    else {
                        $newX = $x + $totalBarWidth*$j;
                        $newY = $y - round ($rheight);
                    }
                    
                    $clr = imagecolorallocate($img, $color->get("r"), $color->get("g"), $color->get("b"));
                    imagefilledellipse($img, $newX, $newY, $this->plotBallSize, $this->plotBallSize, $clr);
                    $innerclr = imagecolorallocate($img, $this->background->fillColor->r, $this->background->fillColor->g, $this->background->fillColor->b);
                    imagefilledellipse($img, $newX, $newY, $this->plotBallSize*0.5, $this->plotBallSize*0.5, $innerclr);
                    
                    if ($this->imageMap != null) {
                        $this->imageMap->addCircleLink($newX, $newY, $this->plotBallSize, $d_key, $dataset->getDataLink ($d_key));
                    }
                    
                    $prevX = $newX;
                    $prevY = $newY;
                    $j++;                
                }                
            }
        }
        else {
            for ($i = 0; $i < $dsCount; $i++) {
                $dataset = $this->data[$i];
                if ($dataset->get("renderAs") != "plot")
                    continue;

                $j = 0;

                $x = $this->axisMargins->get("left") + 1;

                if ($keyAxis->inverse)
                    $y = $this->axisMargins->get("top") + $totalBarWidth/2;
                else 
                    $y = $height - $this->axisMargins->get("bottom") - $totalBarWidth/2;
                
                $prevX = null;
                $prevY = null;    
                $data = $dataset->get("data");
                foreach ($data as $d_key => $d_value) {
                    $color = $dataset->get ("color");
                    
                    $rheight = $d_value*($valueAxis->length())/$maxValue;
                              
                    if ($keyAxis->inverse) {
                        $newX = $x + round ($rheight);
                        $newY = $y + $totalBarWidth*$j;
                    }
                    else {
                        $newX = $x + round ($rheight);
                        $newY = $y - $totalBarWidth*$j;
                    }
                    
                    $clr = imagecolorallocate($img, $color->get("r"), $color->get("g"), $color->get("b"));
                    
                    if ($prevX != null) {
                        imageline($img, $prevX, $prevY, $newX, $newY, $clr);
                        imageline($img, $prevX+1, $prevY, $newX+1, $newY, $clr);                        
                        imageline($img, $prevX, $prevY-1, $newX, $newY-1, $clr);
                        imageline($img, $prevX-1, $prevY, $newX-1, $newY, $clr);
                        imageline($img, $prevX, $prevY+1, $newX, $newY+1, $clr);
                    }

                    if ($this->showDropDownLines) {
                        if ($this->dropDownColor == null)
                            $ddclr = $clr;
                        else 
                            $ddclr = imagecolorallocate($img, $this->dropDownColor->r, $this->dropDownColor->g, $this->dropDownColor->b);
                        imageline($img, $newX, $newY, $newX - round ($rheight) - $this->markOffset, $newY, $ddclr);
                    }
                    
                    $prevX = $newX;
                    $prevY = $newY;
                    $j++;                
                }

                $prevX = null;
                $prevY = null;    
                $j = 0;
                $data = $dataset->get("data");
                foreach ($data as $d_key => $d_value) {
                    $color = $dataset->get ("color");
                    
                    $rheight = $d_value*($valueAxis->length())/$maxValue;
                              
                    if ($keyAxis->inverse) {
                        $newX = $x + round ($rheight);
                        $newY = $y + $totalBarWidth*$j;
                    }
                    else {
                        $newX = $x + round ($rheight);
                        $newY = $y - $totalBarWidth*$j;
                    }
                    
                    $clr = imagecolorallocate($img, $color->get("r"), $color->get("g"), $color->get("b"));
                    imagefilledellipse($img, $newX, $newY, $this->plotBallSize, $this->plotBallSize, $clr);
                    $innerclr = imagecolorallocate($img, $this->background->fillColor->r, $this->background->fillColor->g, $this->background->fillColor->b);
                    imagefilledellipse($img, $newX, $newY, $this->plotBallSize*0.5, $this->plotBallSize*0.5, $innerclr);
                    
                    
                    $prevX = $newX;
                    $prevY = $newY;
                    $j++;                
                }                               
                
                
            }         
        }
        
        return $img;
    }
}

?>