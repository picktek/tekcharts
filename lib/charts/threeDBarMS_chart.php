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
class threeDBarMSChart extends Chart {
    
    function barChart() {
        $this->border = new Border ();
        $this->background = new Background ();
        $this->background->set ("fillColor", new Color(255, 255, 255));
    }    
    
    var $barSpacing = 4;
    var $markOffset = 10;
    var $plotBallSize = 10;
    var $bar3dWidth = 10;
    var $boardHeight = 10;
    var $boardColor = "454542";
    var $backColor = "aeaeae"; 
    var $showBack = true;
    var $labelOffset = 2;
    
    function drawBoard ($img, $horizontal, $width, $height) {
        
        $boardColor = $this->boardColor;
        $backColor = $this->backColor;        
                
        if (!$horizontal) {
            $y = $height-$this->axisMargins->get("bottom")-1;
            $boardHeight = $this->boardHeight;
            //-- Board
            $boardWidth = $width - $this->bar3dWidth * acos (THREED_ANGLE) - $this->axisMargins->get("right") - $this->axisMargins->get("left") + $this->markOffset;
            $a = $this->getKeyAxis();
            $boardWidth = $a->length ();
            
            $color = new Color ($boardColor);
            $bar = new ThreeDBar ($boardWidth/2 + $this->axisMargins->get("left")-$this->markOffset + 3, $y + $boardHeight, $boardWidth, $boardHeight, $color, $this->bar3dWidth);
            $bar->render ($img);
            
            //-- Back
            if ($this->showBack) {
                $color = new Color ($backColor);
                $bar = new ThreeDBar ($boardWidth/2 + $this->axisMargins->get("left") + $this->bar3dWidth * acos (THREED_ANGLE)-$this->markOffset + 3, $y-$this->bar3dWidth * asin (THREED_ANGLE), $boardWidth, $height - $this->bar3dWidth, $color, 0);
                $bar->render ($img);        
            }
        }
        else {
            $y = $height-$this->axisMargins->get("bottom")-1;
            $boardHeight = $this->boardHeight;
            //-- Board
            $boardWidth = $height - $this->bar3dWidth * acos (THREED_ANGLE) - $this->axisMargins->get("top") - $this->axisMargins->get("bottom");
            $backWidth = $width - $this->bar3dWidth * acos (THREED_ANGLE)*2 - $this->axisMargins->get("right") - $this->axisMargins->get("left");
            
            $color = new Color ($boardColor);
            $bar = new ThreeDBar ($this->axisMargins->get("left")-$boardHeight/2, $y , $boardHeight, $boardWidth, $color, $this->bar3dWidth);
            $bar->render ($img);
            
            //-- Back
            if ($this->showBack) {
                $color = new Color ($backColor);
                $bar = new ThreeDBar ($backWidth/2 + $this->axisMargins->get("left") + $this->bar3dWidth * acos (THREED_ANGLE), 
                                        $y-$this->bar3dWidth * asin (THREED_ANGLE)*2+$boardHeight, $backWidth, 
                                        $height - $this->axisMargins->get("top") - $this->axisMargins->get("bottom") - $this->bar3dWidth * asin (THREED_ANGLE), $color, 0);
                $bar->render ($img);                 
            }
        }
    }
    
    function render ($canvas) {
        $width = $this->width;
        $height = $this->height;
        $img = imagecreatetruecolor ($width, $height);
        $this->background->render ($img, $width, $height);
        $this->border->render ($img, $width, $height);

        $this->drawAxii ($img);                
        $keyAxis = $this->getKeyAxis ();
        $valueAxis = $this->getValueAxis ();
        $horizontal = false;
        if ($keyAxis->getAxisAlignment () == TC_AXIS_VERTICAL) {
            $horizontal = true;
        }
        $this->drawBoard($img, $horizontal, $width, $height);
        $this->drawAxii ($img);              
        


        ////////////////////
        $maxValue = $this->dataSeries->getMaxValueForAxis ($this->dataSeries->getMaxDatasetValue ('values'));

        $maxLength = $this->dataSeries->getKeysMaxLength ();
        list($kMaj, $kMin, $vMaj, $vMin) = $this->dataSeries->getSteps ();
        
        $numberOfSets = $dsCount = count ($this->data);
        ////////////////////
        
        $totalBarWidth = $keyAxis->length()*$kMaj/100;
        $oneBarWidth = ($totalBarWidth-$this->markOffset*2)/$numberOfSets - $this->barSpacing;
        imageantialias($img, $this->antiAlias);
        


        // render bars
        if ($numberOfSets != 0) {
            if (!$horizontal) {
                
                $bars = array ();
                $k = 0;
                for ($i = 0; $i < $dsCount; $i++) {
                    $dataset = $this->data[$i];

                    $j = 0;

                    if ($keyAxis->inverse)
                        $x = $width - $this->axisMargins->get("right") - ($this->markOffset + ($oneBarWidth + $this->barSpacing) * $i + $this->barSpacing*0.5 + $oneBarWidth * 0.5);
                    else
                        $x = $this->axisMargins->get("left") + $this->markOffset + ($oneBarWidth + $this->barSpacing) * $i + $this->barSpacing*0.5 + $oneBarWidth * 0.5;

                    $y = $height-$this->axisMargins->get("bottom")-1;                        
                        
                    $data = $dataset->get("data");
                    foreach ($data as $d_key => $d_value) {
                        $color = $dataset->get ("color");
                        
                        $rheight = $d_value*($valueAxis->length())/$maxValue;
                                  
                        if ($keyAxis->inverse) {
                            $bar = new ThreeDBar($x - $totalBarWidth * $j, $y, $oneBarWidth, round ($rheight), $color, $this->bar3dWidth);
                            $bars[$k]['center'] = $x - $totalBarWidth * $j;
                        }
                        else {
                            $bar = new ThreeDBar($x + $totalBarWidth * $j, $y, $oneBarWidth, round ($rheight), $color, $this->bar3dWidth);                            
                            $bars[$k]['center'] = $x + $totalBarWidth * $j;                            
                        }
                        $bars[$k]['bar'] = $bar;
                        $bars[$k]['value'] = $d_value;
                        $bars[$k]['key'] = $d_key;
                        $bars[$k]['height'] = $y - round ($rheight) - $this->labelOffset;
                        $bars[$k]['rheight'] = round ($rheight);
                        $k++;
                        $j++;                
                    }
                }
                if ($keyAxis->inverse) {
                    for ($l = count ($bars)-1; $l >=0 ; $l--) {
                        $bars[$l]['bar']->render($img);
                        $si = new ChartStringItem ($bars[$l]['value'], $keyAxis->get ("labelFont"));
                        $si->set ("color", $keyAxis->get("color"));
                        
                        if ($si->width() > $oneBarWidth) {
                            $siy = $bars[$l]['height'] + $si->height()/2;
                            if ($bars[$l]['rheight'] <= $si->width()+$si->height()/2)
                                $siy = $bars[$l]['height'] + $bars[$l]['rheight'] - $si->width();

                            $si->set ("angle", -TC_SI_ANGLE_90);
                            $si->render($img, $bars[$l]['center']-$si->width()/2, $bars[$l]['height'] + $si->height()/2);
                        }
                        else {
                            $siy = $bars[$l]['height'] - $si->height();

                            $si->set ("angle", -TC_SI_ANGLE_0);
                            $si->render($img, $bars[$l]['center'], $siy);
                        }
                        
                        if ($this->imageMap != null) {
                            $this->imageMap->addRectLink($bars[$l]['bar']->x - $bars[$l]['bar']->width/2, 
                                                         $bars[$l]['bar']->y, 
                                                         $bars[$l]['bar']->x + $bars[$l]['bar']->width/2, 
                                                         $bars[$l]['bar']->y - $bars[$l]['bar']->height, 
                                                         $bars[$l]['key'],
                                                         $dataset->getDataLink ($bars[$l]['key']));
                        }
                    }

                }
                else {
                    for ($l = 0; $l < count ($bars); $l++) {
                        $bars[$l]['bar']->render($img);
                        $si = new ChartStringItem ($bars[$l]['value'], $keyAxis->get ("labelFont"));
                        $si->set ("color", $keyAxis->get("color"));
                        
                        if ($si->width() > $oneBarWidth) {
                            $siy = $bars[$l]['height'] + $si->height()/2;
                            if ($bars[$l]['rheight'] <= $si->width()+$si->height()/2)
                                $siy = $bars[$l]['height'] + $bars[$l]['rheight'] - $si->width();
                                                        
                            $si->set ("angle", -TC_SI_ANGLE_90);
                            $si->render($img, $bars[$l]['center']-$si->width()/2, $siy);
                        }
                        else {
                            $siy = $bars[$l]['height'] - $si->height();
                            
                            $si->set ("angle", -TC_SI_ANGLE_0);
                            $si->render($img, $bars[$l]['center'], $siy);
                        }

                        if ($this->imageMap != null) {
/*                            $this->imageMap->addRectLink($bars[$l]['bar']->x - $bars[$l]['bar']->width/2, 
                                                         $bars[$l]['bar']->y, 
                                                         $bars[$l]['bar']->x + $bars[$l]['bar']->width/2, 
                                                         $bars[$l]['bar']->y - $bars[$l]['bar']->height, 
                                                         $bars[$l]['key'],
                                                         $dataset->getDataLink ($bars[$l]['key']));*/
                            $this->imageMap->addPolyLink($bars[$l]['bar']->getShapePoly (), 
                                                         $bars[$l]['key'],
                                                         $dataset->getDataLink ($bars[$l]['key']));                        }

                    }
                    

                }
                
                
            }
            else {
                $bars = array ();
                $k = 0;
                                
                for ($i = 0; $i < $dsCount; $i++) {
                    $dataset = $this->data[$i];
                    
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
                                            
                        if ($keyAxis->inverse) {
                            $bar = new ThreeDBar ($x+round ($rheight/2), $y + $totalBarWidth * $j, round ($rheight), $oneBarWidth, $color, $this->bar3dWidth);
                            $bars[$k]['center'] =  $y + $totalBarWidth * $j;
                        }
                        else {
                            $bar = new ThreeDBar ($x+round ($rheight/2), $y - $totalBarWidth * $j, round ($rheight), $oneBarWidth, $color, $this->bar3dWidth);
                            $bars[$k]['center'] = $y - $totalBarWidth * $j;
                        }
                        
                        $bars[$k]['bar'] = $bar;
                        $bars[$k]['value'] = $d_value;
                        $bars[$k]['height'] = $x+round ($rheight)+$this->labelOffset;                        
        
                        $k++;
                        $j++;                
                    }
                }
             
                if ($keyAxis->inverse) {
                    for ($l = count ($bars)-1; $l >=0 ; $l--) {
                        $bars[$l]['bar']->render($img);
                        $si = new ChartStringItem ($bars[$l]['value'], $keyAxis->get ("labelFont"));
                        $si->set ("angle", -TC_SI_ANGLE_0);
                        $si->set ("color", $keyAxis->get("color"));
                        $si->render($img, $bars[$l]['height'] - $si->width(), $bars[$l]['center'] - $si->height());
                    }

                }
                else {
                    
                    for ($l = 0; $l < count ($bars); $l++) {
                        $bars[$l]['bar']->render($img);
                        $si = new ChartStringItem ($bars[$l]['value'], $keyAxis->get ("labelFont"));
                        $si->set ("angle", -TC_SI_ANGLE_0);
                        $si->set ("color", $keyAxis->get("color"));
                        $si->render($img, $bars[$l]['height'] - $si->width(), $bars[$l]['center'] - $si->height());
                    }
                        
                }
            }
        }
        
        return $img;
    }
}

?>