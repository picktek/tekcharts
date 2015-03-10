<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("lineStyleClass.php");
require_once ("chartBeanClass.php");
require_once ("chartDataSeriesClass.php");

define ("TC_AXIS_TYPE_Y_LEFT", "y-left");
define ("TC_AXIS_TYPE_Y_RIGHT", "y-right");
define ("TC_AXIS_TYPE_X_TOP", "x-top");
define ("TC_AXIS_TYPE_X_BOTTOM", "x-bottom");

define ("TC_AXIS_HORIZONTAL", "x");
define ("TC_AXIS_VERTICAL", "y");

define ("TC_AXIS_STYLE_LINE", "line");

define ("TC_AXIS_LABEL_MARKER_OFFSET", 2);

/**
 * Class which represents chart axis
 *
 */
class ChartAxis extends ChartBean {
    /**
     * Pointer to parent Chart class
     *
     * @var Chart
     */
    var $chart = null;
    
    /**
     * Axis class constructor
     *
     * @param Chart $chart
     * @return ChartAxis
     */
    function ChartAxis ($chart) {
        $this->chart = $chart;
        $this->color = new Color (0, 0, 0);
        $this->gridLineStyle = TC_LINE_STYLE_DOTTED;
        $this->gridLineColor = new Color (200, 200, 200);
        $this->labelFont = new Font (3);
    }
    
    /**
     * Property: controls axis alignment
     *
     * @var int
     */
    var $type = TC_AXIS_TYPE_X_BOTTOM;
    /**
     * Property: constols axis line style
     *
     * @var int
     */
    var $style = TC_AXIS_STYLE_LINE;
    /**
     * Property: controls displaying of major steps
     *
     * @var boolean
     */
    var $showMajorSteps = true;
    /**
     * Property: constrols displaying of minor steps
     *
     * @var boolean
     */
    var $showMinorSteps = true;
    /**
     *  Property: Indicates if this axis is key axis
     *
     * @var boolean
     */
    var $keyAxis = null;    
    
    /**
     *  Property:Color of axis
     *
     * @var Color
     */
    var $color = null;
    
    /**
     * Property: opacity of axis
     *
     * @var int
     */
    var $alpha = 100;

    /**
     * Property: thickness of axis
     * Also used to size marks
     *
     * @var int
     */    
    var $thickness = 10;
    
    /**
     * Property: controls direction of axis
     *
     * @var boolean
     */    
    var $inverse = false;
    
    /**
     * Property: constorls displaying of gridlines
     *
     * @var boolean
     */    
    var $drawGridLines = true;
    
    /**
     * Property: grid lines style
     *
     * @var int
     */
    var $gridLineStyle = null;
    
    /**
     * Property: grid lines color
     *
     * @var Color
     */    
    var $gridLineColor = null;
    
    /**
     * Property: controls displaying of main axis line
     *
     * @var boolean
     */    
    var $drawAxisLine = true;
    
    /**
     * Property: controls displaying of axis marks
     *
     * @var boolean
     */    
    var $drawAxisMarks = true;
    
    /**
     * Property: controls displaying of axis labels
     *
     * @var boolean
     */
    var $showLabels = true;
    
    /**
     * Property: axis labels font
     *
     * @var Font
     */    
    var $labelFont = null;
    
    /**
     * Property: rotation angle of labels
     *
     * @var float
     * In degrees
     */    
    var $labelsAngle =  TC_SI_ANGLE_0;
    
    /**
     * Property: show everty $labelFilter-th label
     *
     * @var int
     * In degrees
     */    
    var $labelFilter =  1;

    function thickness () {
        return $this->thickness;
    }

    /**
     * Axis measurable length
     *
     * @return int
     */
    function length () {
        if ($this->type == TC_AXIS_TYPE_X_BOTTOM || $this->type == TC_AXIS_TYPE_X_TOP)
            return ($this->chart->get("width") - $this->chart->axisMargins->get("left") - $this->chart->axisMargins->get("right"));
        if ($this->type == TC_AXIS_TYPE_Y_LEFT || $this->type == TC_AXIS_TYPE_Y_RIGHT)
            return ($this->chart->get("height") - $this->chart->axisMargins->get("top") - $this->chart->axisMargins->get("bottom"));
    }
    
    /**
     * Axis measurable height
     *
     * @return int
     */
    function height () {
        if ($this->type == TC_AXIS_TYPE_Y_LEFT || $this->type == TC_AXIS_TYPE_Y_RIGHT)
            return ($this->chart->get("width") - $this->chart->axisMargins->get("left") - $this->chart->axisMargins->get("right"));
        if ($this->type == TC_AXIS_TYPE_X_BOTTOM || $this->type == TC_AXIS_TYPE_X_TOP)
            return ($this->chart->get("height") - $this->chart->axisMargins->get("top") - $this->chart->axisMargins->get("bottom"));
    }    
    
    /**
     * Is axis vertical or horizontal
     *
     */
    function getAxisAlignment () {
        if ($this->type == TC_AXIS_TYPE_X_BOTTOM || $this->type == TC_AXIS_TYPE_X_TOP)
            return TC_AXIS_HORIZONTAL;
        if ($this->type == TC_AXIS_TYPE_Y_LEFT || $this->type == TC_AXIS_TYPE_Y_RIGHT)
            return TC_AXIS_VERTICAL;
    }
    
    /**
     * Get label position relative to axis marker startpoint
     *
     * @param float $mx
     * @param float $my
     * @param ChartStringItem $si
     * @param int $markerSize
     * @param float $stepWidthPixels
     */
    function getRelativeLabelPosition ($mx, $my, $si, $markerSize, $stepWidthPixels) {
        $x = $mx;
        $y = $my;
        
        switch ($this->type) {
            case TC_AXIS_TYPE_X_BOTTOM:
                if ($this->keyAxis) {
                    if ($this->inverse)
                        $x += ($stepWidthPixels - $si->width())/2;
                    else
                        $x -= ($stepWidthPixels + $si->width())/2;
                        
                    $y += $markerSize + TC_AXIS_LABEL_MARKER_OFFSET;
                }
                else {
                    $x -= $si->width()/2;
                    $y += $markerSize + TC_AXIS_LABEL_MARKER_OFFSET;
                }
                break;
            case TC_AXIS_TYPE_X_TOP:
                if ($this->keyAxis) {
                    if ($this->inverse)
                        $x += ($stepWidthPixels + $si->width())/2;
                    else
                        $x -= ($stepWidthPixels + $si->width())/2;
                        
                    $y -= $markerSize + TC_AXIS_LABEL_MARKER_OFFSET + $si->height();
                }
                else {
                    $x -= $si->width()/2;
                    $y -= $markerSize + TC_AXIS_LABEL_MARKER_OFFSET + $si->height();
                }
                break;
            case TC_AXIS_TYPE_Y_LEFT:
                if ($this->keyAxis) {
                    if ($this->inverse)
                        $y -= ($stepWidthPixels + $si->height())/2;
                    else
                        $y += ($stepWidthPixels - $si->height())/2;
                        
                    $x -= $markerSize + TC_AXIS_LABEL_MARKER_OFFSET + $si->width();
                }
                else {
                    $x -= $markerSize + TC_AXIS_LABEL_MARKER_OFFSET + $si->width();
                    $y -= $si->height()/2;
                }                
                break;
            case TC_AXIS_TYPE_Y_RIGHT:
                if ($this->keyAxis) {
                    if ($this->inverse)
                        $y -= ($stepWidthPixels + $si->height())/2;
                    else
                        $y += ($stepWidthPixels + $si->height())/2;
                        
                    $x += $markerSize + TC_AXIS_LABEL_MARKER_OFFSET;
                }
                else {
                    $x += $markerSize + TC_AXIS_LABEL_MARKER_OFFSET;
                    $y -= $si->height()/2;
                }                
                break;                
        }
        
        return array ($x, $y);
    }
    
    /**
     * Draw grid lines belonging to axis
     *
     * @param Image $img
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param float $maj
     *  Major step amount
     * @param float $min
     *  Minor step amount
     * @param int $gridLineDir
     * @param int $gridLineStyle
     * @param int $gridLineSize
     */
    function drawGridLines ($img, $x1, $y1, $x2, $y2, $color, $maj, $min,
                            $gridLineDir = TC_LINE_MARKS_CW, $gridLineStyle = TC_LINE_STYLE_DASHED,
                            $gridLineSize = 10) {
        // line length
        $len = sqrt (pow(($x2-$x1), 2) + pow(($y2-$y1), 2));
        // angle
        $angle = atan2(($y2 - $y1), ($x2 - $x1));

        if ($gridLineDir == TC_LINE_MARKS_CCW)
            $newAngle2 = -pi()/2;
        else
            $newAngle2 = pi()/2;
        
        $dx = ($x2 - $x1) * $maj/100;
        $dy = ($y2 - $y1) * $maj/100;            
        
        $a1 = $x1; $b1 = $y1;
        $a2 = 0; $b2 = 0; $a3 = 0; $b3 = 0; 
        $ll = 0; $sl = $size; $ss = $sl/2;
        $deltaL = sqrt ($dx*$dx + $dy*$dy);
        $i = 0;

        while ($ll + $deltaL < $len) {
            $a1 += $dx;
            $b1 += $dy;
            
            // gridlines vector
            $vxx = $gridLineSize * cos ($angle);
            $vyy = $gridLineSize * sin ($angle);            

            $vx3 = cos($newAngle2)*$vxx - sin($newAngle2)*$vyy;
            $vy3 = sin($newAngle2)*$vxx + cos($newAngle2)*$vyy;
            
            $a3 = $a1 + $vx3;
            $b3 = $b1 + $vy3;
            
            $clr2 = imagecolorallocate ($img, $color->get ("r"), $color->get ("g") , $color->get ("b"));

            if ($gridLineStyle != TC_LINE_STYLE_SOLID) {
                $w = imagecolorallocate ($img, 255, 255, 255);
                if ($gridLineStyle == TC_LINE_STYLE_DASHED)
                    $style = array($clr2, $clr2, $clr2, $clr2, $clr2, $clr2, $clr2, $clr2, $w, $w, $w, $w, $w, $w, $w, $w);
                else
                    $style = array($clr2, $clr2, $clr2, $clr2, $w, $w, $w, $w);
                
                imagesetstyle ($img, $style);
                $clr2 = IMG_COLOR_STYLED;
            }            
            
            imageline($img, $a1, $b1, $a3, $b3, $clr2);
               
            $ll += $deltaL;
        }        
        
    }
    
    /**
     * Draw labels on axis
     *
     * @param Image $img
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param Color $color
     * @param float $maj
     *  Major step amount
     * @param float $min
     *  Minor step amount
     * @param array() $labels
     *  Labels to be draw in one-dimensional array
     * @param Font $labelFont
     */
    function drawLabels ($img, $x1, $y1, $x2, $y2, $color, $maj, $min,
                         $labels = null, $labelFont = null) {
                             
                // line length
        $len = sqrt (pow(($x2-$x1), 2) + pow(($y2-$y1), 2));
        // angle
        $angle = atan2(($y2 - $y1), ($x2 - $x1));
        
        $dx = ($x2 - $x1) * $maj/100;
        $dy = ($y2 - $y1) * $maj/100;
        
        $a1 = $x1; $b1 = $y1;
        $ll = 0; 
        $deltaL = sqrt ($dx*$dx + $dy*$dy);
        $i = 0;
        
        while ($ll + $deltaL < $len) {
            $a1 += $dx;
            $b1 += $dy;
            
            if ($i % $this->labelFilter == 0) {
                $si = new ChartStringItem ($labels[$i], $labelFont);
                $si->set ("color", $color);
                $si->set ("angle", $this->labelsAngle);
                list ($lx, $ly) = $this->getRelativeLabelPosition ($a1, $b1, $si, $this->thickness, $deltaL);
                $si->render ($img, $lx, $ly);
            }
            $i++;            
            $ll += $deltaL;
        }   
                                     


    }
    
    /**
     * Draw markers on axis
     *
     * @param Image $img
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param Color $color
     * @param float $maj
     *  Major step amount
     * @param float $min
     *  Minor step amount
     * @param int $size
     *  Major step marker length
     * @param int $dir
     *  Marker direction (clockwise, counter-clockwise)
     */
    function drawMarkers ($img, $x1, $y1, $x2, $y2, $color, $maj, $min, $size, $dir = TC_LINE_MARKS_CW) {
        // line length
        $len = sqrt (pow(($x2-$x1), 2) + pow(($y2-$y1), 2));
        // angle
        $angle = atan2(($y2 - $y1), ($x2 - $x1));
        
        if ($dir == TC_LINE_MARKS_CCW) $newAngle = -pi()/2;
        else $newAngle = pi()/2;

        $dx = ($x2 - $x1) * $maj/100;
        $dy = ($y2 - $y1) * $maj/100;
        
        $a1 = $x1; $b1 = $y1;
        $a2 = 0; $b2 = 0; $ll = 0; $sl = $size; $ss = $sl/2;
        $deltaL = sqrt ($dx*$dx + $dy*$dy);

        while ($ll + $deltaL < $len) {
            // draw minor steps
            if ($min != null) {
                $this->drawMarkers($img, $a1, $b1, $a1 + $dx, $b1 + $dy, $color, $min, null, $ss, $dir);
            }

            $a1 += $dx;
            $b1 += $dy;
            
            $vx = $sl * cos ($angle);
            $vy = $sl * sin ($angle);

            // marks vector           
            $vx2 = cos($newAngle)*$vx - sin($newAngle)*$vy;
            $vy2 = sin($newAngle)*$vx + cos($newAngle)*$vy;
            
            $a2 = $a1 + $vx2;
            $b2 = $b1 + $vy2;

            $clr = imagecolorallocate ($img, $color->get ("r"), $color->get ("g") , $color->get ("b"));
            imageline($img, $a1, $b1, $a2, $b2, $clr);
            $ll += $deltaL;
        }        
    }    
    
    /**
     * Draw main axis line
     *
     * @param Image $img
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     */
    function drawLine ($img, $x1, $y1, $x2, $y2) {
        $clr = imagecolorallocate ($img, $this->color->get ("r"), $this->color->get ("g") , $this->color->get ("b"));
                    
        if ($this->style != TC_LINE_STYLE_SOLID) {
            $w = imagecolorallocate ($img, 255, 255, 255);
            if ($this->style == TC_LINE_STYLE_DASHED)
                $style = array($clr, $clr, $clr, $clr, $w, $w, $w, $w);
            else
                $style = array($clr, $clr, $w, $w);
            
            imagesetstyle ($img, $style);
            $clr = IMG_COLOR_STYLED;
        }
            
        imageline ($img, $x1, $y1, $x2, $y2, $clr);
    }
        
    /**
     * Render axis
     *
     * @param Image$img
     * @param Chart$chart
     */
    function render ($img, $chart = null) {
        
        if ($chart != null) {
            $this->chart = $chart;
        }
        
        list($kMaj, $kMin, $vMaj, $vMin) = $this->chart->getSteps ();
        if ($kMaj == null)
            return;
        /*    
        if ($this->keyAxis == null) {
            if ($this->type == TC_AXIS_TYPE_X_BOTTOM || $this->type == TC_AXIS_TYPE_X_TOP)
                $this->keyAxis = true; 
             else
                $this->keyAxis = false;
        }
        */

        $majStep = 100;
        $minStep = 100;
        $labels = null;
        
        if ($this->keyAxis) {
            $majStep = $kMaj;
            $minStep = $kMin;
            $labels = $this->chart->getKeyLabels ();
        }
        else {
            $majStep = $vMaj;
            $minStep = $vMin;
            $labels = $this->chart->getValueLabels ();
        }
                
        /*
        Determine which axis we are drawing
        Each axis has following geometrical properties:
        x1, y1 - x2, y2
        offset from chart border, which includes label height, neccesary margins etc
        thickness
        major an minor step sizes
        direction of steps (in/out)        
        */
        $x1 = 0; $y1 = 0;
        $x2 = 0; $y2 = 0;
        $stepDir = TC_LINE_MARKS_CW;
        $stepSize = $this->thickness;
        $gridDir = TC_LINE_MARKS_CW;
        $gridLength = 0;
        
        switch ($this->type) {
            case TC_AXIS_TYPE_X_TOP:
                if ($this->inverse) {
                    $x1 =  $this->chart->get("width") - $this->chart->axisMargins->get("right"); $y1 = $this->chart->axisMargins->get("top"); $x2 = $this->chart->axisMargins->get("left"); $y2 = $y1;                       
                    $stepDir = TC_LINE_MARKS_CW;
                    $gridDir = TC_LINE_MARKS_CCW;
                }
                else {
                    $x1 = $this->chart->axisMargins->get("left"); $y1 = $this->chart->axisMargins->get("top"); $x2 = $this->chart->get("width") - $this->chart->axisMargins->get("right"); $y2 = $y1;
                    $stepDir = TC_LINE_MARKS_CW;
                    $gridDir = TC_LINE_MARKS_CW;
                }
                break;
            case TC_AXIS_TYPE_X_BOTTOM:
                if ($this->inverse) {
                    $x1 =  $this->chart->get("width") - $this->chart->axisMargins->get("right"); $y1 = $this->chart->get("height") - $this->chart->axisMargins->get("bottom"); $x2 = $this->chart->axisMargins->get("left"); $y2 = $y1;                       
                    $stepDir = TC_LINE_MARKS_CCW;
                    $gridDir = TC_LINE_MARKS_CW;
                }
                else {
                    $x1 = $this->chart->axisMargins->get("left"); $y1 = $this->chart->get("height") - $this->chart->axisMargins->get("bottom"); $x2 = $this->chart->get("width") - $this->chart->axisMargins->get("right"); $y2 = $y1;
                    $stepDir = TC_LINE_MARKS_CW;
                    $gridDir = TC_LINE_MARKS_CCW;
                }
                break;
            case TC_AXIS_TYPE_Y_LEFT:
                if ($this->inverse) {
                    $x1 =  $this->chart->axisMargins->get("left"); $y1 = $this->chart->axisMargins->get("top"); $x2 = $x1; $y2 = $this->chart->get("height") - $this->chart->axisMargins->get("bottom"); 
                    $stepDir = TC_LINE_MARKS_CW;
                    $gridDir = TC_LINE_MARKS_CCW;
                }
                else {
                    $x1 =  $this->chart->axisMargins->get("left"); $y1 = $this->chart->get("height") - $this->chart->axisMargins->get("bottom"); $x2 = $x1; $y2 = $this->chart->axisMargins->get("top"); 
                    $stepDir = TC_LINE_MARKS_CCW;
                    $gridDir = TC_LINE_MARKS_CW;
                }
                break;
            case TC_AXIS_TYPE_Y_RIGHT:
                if ($this->inverse) {
                    $x1 =  $this->chart->get("width") - $this->chart->axisMargins->get("right"); $y1 = $this->chart->axisMargins->get("top"); $x2 = $x1; $y2 = $this->chart->get("height") - $this->chart->axisMargins->get("bottom"); 
                    $stepDir = TC_LINE_MARKS_CW;
                    $gridDir = TC_LINE_MARKS_CCW;
                }
                else {
                    $x1 =  $this->chart->get("width") - $this->chart->axisMargins->get("right"); $y1 = $this->chart->get("height") - $this->chart->axisMargins->get("bottom"); $x2 = $x1; $y2 = $this->chart->axisMargins->get("top"); 
                    $stepDir = TC_LINE_MARKS_CCW;
                    $gridDir = TC_LINE_MARKS_CW;
                }
                break;
        } 
        
        $gridLength = $this->height ();

        if (!$this->showMinorSteps)
            $minStep = null;
        
    
        if ($this->drawAxisLine)
            $this->drawLine ($img, $x1, $y1, $x2, $y2);
        if ($this->drawAxisMarks)
            $this->drawMarkers ($img, $x1, $y1, $x2, $y2, $this->color, $majStep, $minStep, $stepSize, $stepDir);
        if ($this->drawGridLines) {
            $this->drawGridLines ($img, $x1, $y1, $x2, $y2, $this->gridLineColor, $majStep, $minStep, $gridDir, $this->gridLineStyle, $gridLength);
        }
        if ($this->showLabels) {
            $this->drawLabels ($img, $x1, $y1, $x2, $y2, $this->color, $majStep, $minStep, $labels, $this->labelFont);
        }

    }
    
}

?>