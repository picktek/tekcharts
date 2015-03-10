<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("includes/tooltip.php");

/**
 * Area chart which can display multiple data sets and be either of horizontal or vertical alignment
 *
 */
class areaChart extends Chart {
    
    function areaChart () {
        $this->border = new Border ();
        $this->background = new Background ();
        $this->background->set ("fillColor", new Color(255, 255, 255));
    }    
    
    var $markOffset = 10;
    var $plotBallSize = 2;
    
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
        
        $totalBarWidth = $keyAxis->length()*$kMaj/100;        
        $maxValue = $this->dataSeries->getMaxValueForAxis ($this->dataSeries->getMaxDatasetValue ('values'));
        
        imageantialias($img, $this->antiAlias);
        
        // render area
        $groups = $this->dataSeries->getDatasetGroupNames ();
        $stack_array = array ();
        
        foreach ($groups as $groupName) {
            $datasets = $this->dataSeries->getDatasetsByGroup ($groupName);
            
            $polygons = array ();
            $polyCount = 0;
            foreach ($datasets as $dataset) {
                // render plots
                $j = 0; 
                $polyIndex = 0;
                $polygon = array ();
                $polyData = array ();
                if (!$horizontal) {
                    if ($keyAxis->inverse)
                        $x = $width - $this->axisMargins->get("right") - $totalBarWidth/2;
                    else
                        $x = $this->axisMargins->get("left") + $totalBarWidth/2;
                        
                    $y = $height-$this->axisMargins->get("bottom")-1;
                }    
                else {
                    $x = $this->axisMargins->get("left") + 1;
    
                    if ($keyAxis->inverse)
                        $y = $this->axisMargins->get("top") + $totalBarWidth/2;
                    else 
                        $y = $height - $this->axisMargins->get("bottom") - $totalBarWidth/2;
                }

                $prevX = $x;
                $prevY = $y;    
                $polygon[] = $x;
                $polygon[] = $y;
                $polyIndex++;
                
                $data = $dataset->get("data");
                foreach ($data as $d_key => $d_value) {
                    $color = $dataset->get ("color");

                    if ($stack_array[$d_key] == null) {
                        $stack_array[$d_key] = $d_value;
                    }
                    else {
                        $d_value += $stack_array[$d_key];
                        $stack_array[$d_key] = $d_value;
                    }
                    
                    $rheight = $stack_array[$d_key]*($valueAxis->length())/$maxValue;
                                  
                    if (!$horizontal) {
                        if ($keyAxis->inverse) {
                            $newX = $x - $totalBarWidth*$j;
                            $newY = $y - round ($rheight);
                        }
                        else {
                            $newX = $x + $totalBarWidth*$j;
                            $newY = $y - round ($rheight);
                        }
                    }
                    else {
                        if ($keyAxis->inverse) {
                            $newX = $x + round ($rheight);
                            $newY = $y + $totalBarWidth*$j;
                        }
                        else {
                            $newX = $x + round ($rheight);
                            $newY = $y - $totalBarWidth*$j;
                        }                        
                    }
                    
                    $clr = imagecolorallocate($img, $color->get("r"), $color->get("g"), $color->get("b"));
                    if ($prevX != null) {
                        //imageline($img, $prevX, $prevY, $newX, $newY, $clr);
                    }
                    
                    
                    $polygon[] = $newX;
                    $polygon[] = $newY;
                    $polyData[] = $d_value;
                    $polyIndex += 1;
                    
                    //imagefilledellipse($img, $newX, $newY, $this->plotBallSize, $this->plotBallSize, $clr);
                    
                    $prevX = $newX;
                    $prevY = $newY;
                    $j++;                
                }
                
                if (!$horizontal) {
                    $polygon[] = $newX;
                    $polygon[] = $y;
                    $polyIndex++;
                }
                else {
                    $polygon[] = $x;
                    $polygon[] = $newY;
                    $polyIndex++;
                }
                
                $polygons[$polyCount]['poly'] = $polygon;
                $polygons[$polyCount]['num'] = $polyIndex;
                $polygons[$polyCount]['color'] = $clr;
                $polygons[$polyCount]['data'] = $polyData;
                $polyCount++;
            }

            $timg = imagecreatetruecolor (imagesx ($img), imagesy ($img));
            $trans = imagecolorallocate($timg, TC_TRANSPARENT_COLOR_R, TC_TRANSPARENT_COLOR_G, TC_TRANSPARENT_COLOR_B);
            $trans = imagecolortransparent ($timg, $trans);
            imagefilledrectangle ($timg, 0, 0, imagesx ($timg), imagesy ($timg), $trans);
            imageantialias ($timg, $this->antiAlias);
            for ($l = $polyCount-1; $l >= 0; $l--) {
                imagefilledpolygon ($timg, $polygons[$l]['poly'], $polygons[$l]['num'], $polygons[$l]['color']);
                
                // draw tooltips
                $data = $polygons[$l]['data'];
                $coords = $polygons[$l]['poly'];
                for ($n = 1; $n < count ($polygons[$l]['poly'])/2-1; $n++) {
                    $tboc =  new Color ("FEFEFE");
                    $tbac =  new Color ("999999");
                    $sc =  new Color ("000000");
                    $tooltip = new Tooltip($this->markOffset, $data[$n-1], 4, $tboc, $tboc, $tbac, $sc);
                    $tooltip->renderByQuadrant($img, $coords[$n*2], $coords[$n*2+1], 4);
                }
            }        
            
            imagecopymerge ($img, $timg, 0, 0, 0, 0, imagesx($timg), imagesy ($timg), $this->alpha*0.6);

        }
        
 
        return $img;
    }
}

?>