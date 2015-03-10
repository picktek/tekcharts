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
require_once ("includes/3dArc.php");

class pieChart extends Chart {
    
    function simpleBarChart() {
        $this->border = new Border ();
        $this->background = new Background ();
        //$this->background->set ("fillColor", new Color(230, 230, 230));
    }    
    
    var $sliced = false;
    var $slicingDistance = 10;
    var $cutoutBiggestPiece = true;
    var $cutoutDistance = 10;
    var $maxPiesInRow = 2;
    // in percent of section width
    var $piesMarginsVert = 0.20;
    var $piesMarginsHorz = 0.20;
    var $pieHeight = 20;
    
    var $shadow;
    var $hilite;
    var $shadowColorGrad = 0.25;
    var $hiliteColorGrad = 0.25;
    var $sliceColorGrad = 0.05;
    
    
    function getNextSliceColor ($color) {
        
        $r = $color->r; $g = $color->g; $b = $color->b;
        $rsh = $r * (1 - $this->sliceColorGrad);       
        $gsh = $g * (1 - $this->sliceColorGrad);       
        $bsh = $b * (1 - $this->sliceColorGrad);
        $c = new Color($rsh, $gsh, $bsh);
        return $c;
    }
    
    function drawPie ($img, $data, $x, $y, $w, $h, $color, $show_labels =false) {
        $pieWidth = $w*(1-2*$this->piesMarginsHorz);
        $pieHeight = $h*(1-2*$this->piesMarginsVert);
        
        $sum = 0;       
        foreach ($data->get("data") as $dkey => $dvalue) {
            $sum += $dvalue;
        }
        
        reset($data);
        $sa = 0;
        $ea = 0;
        $range = 20;
        $maxValue = $data->maxValue();

        imageantialias ($img, $this->antiAlias);
        $arc_n = 0;
        $arcs = array ();
        foreach ($data->get("data") as $dkey => $dvalue) {
            $color = $this->getNextSliceColor ($color);
            
            $ea = $sa + $dvalue/$sum*360;

            $av = deg2rad(($sa + $ea)/2);
            
            $sx = 0; $sy = 0;
            if ($this->sliced) {
                $sx = $this->slicingDistance*cos ($av);
                $sy = $this->slicingDistance*sin ($av);
            }
            
            if ($this->cutoutBiggestPiece) {
                if ($dvalue == $maxValue) {
                    $sx += $this->cutoutDistance*cos ($av);
                    $sy += $this->cutoutDistance*sin ($av); 
                }
            }
            
            $lx = $x + ($pieWidth/2 + $this->slicingDistance*2) * cos ($av);
            $ly = $y + ($pieHeight/2 + $this->slicingDistance*2) * sin ($av);

            $lxx = $x + ($pieWidth/2 + $this->slicingDistance*2) * cos ($av)/1.5;
            $lyy = $y + ($pieHeight/2 + $this->slicingDistance*2) * sin ($av)/1.5;

            $clr = imagecolorallocate ($img, $color->r, $color->g, $color->b);
            imagefilledarc($img, $x + $sx, $y + $sy, $pieWidth, $pieHeight, $sa, $ea, $clr, IMG_ARC_PIE);
            
            
            if ($show_labels) {
                imageline($img, $x + $sx, $y + $sy, $lx, $ly, $clr);
                // TODO: font
                $si = new ChartStringItem ($dkey, null);
                if ($sa >= 270 || $sa <= 90)
                    $ox = 0;
                else
                    $ox = -$si->width();
                
                if ($sa >= 0 && $sa <= 180)
                    $oy = 0;
                else
                    $oy = -$si->height();
                
                $si->render($img, $lx + $ox, $ly + $oy);
                
                $p = round ($dvalue/$sum, 4) * 100;
                $li = new ChartStringItem ($p, 2);
                $li->render($img, $lxx - $li->width()/2, $lyy - $li->height()/2);
            }
            $sa = $ea;
        }
        


    }
    
    function getHiLoColors ($color) {
        $r = $color->r;
        $g = $color->g;
        $b = $color->b;
        
        $rsh = $r * (1 - $this->shadowColorGrad);       
        $gsh = $g * (1 - $this->shadowColorGrad);       
        $bsh = $b * (1 - $this->shadowColorGrad);
               
        $rhi = $r * (1 + $this->hiliteColorGrad);       
        $ghi = $g * (1 + $this->hiliteColorGrad);       
        $bhi = $b * (1 + $this->hiliteColorGrad);       
        
        $this->shadow = new Color( $rsh, $gsh, $bsh);
        $this->hilite = new Color( $rhi, $ghi, $bhi);
    }
        
    
    function render ($canvas) {
        
        $width = $this->width;
        $height = $this->height;
        $img = imagecreatetruecolor ($width, $height);

        $this->background->render ($img, $width, $height);
        $this->border->render ($img, $width, $height);
        
        $count = count ($this->data);
        $columns = min ($this->maxPiesInRow, $count);
        $rows = max (1, ceil ($count/$columns));
        
        $sectionWidth = $width/$columns;
        $sectionHeigth = $height/$rows;

        $x = 0;
        $y = 0;
        $i = $j = 0;
        
        

        foreach ($this->data as $dataset) {
            $this->getHiLoColors ($dataset->get("color"));
            
            
            $x = $sectionWidth*$i + $sectionWidth/2;
            $y = $sectionHeigth*$j + $sectionHeigth/2;
            
            for ($k = $y + $this->pieHeight; $k >= $y; $k--)
                $this->drawPie ($img, $dataset, $x, $k, $sectionWidth, $sectionHeigth, $this->shadow);
            
            $this->drawPie ($img, $dataset, $x, $y, $sectionWidth, $sectionHeigth, $this->hilite, true);            
                
            $i++;

            if ($i >= $columns) {
                $i = 0;
                $j++;
            }
            if ($j == $rows-1) {
                $sectionWidth = $width/($count-$columns*($rows-1));
            }
            
        }
        
        return $img;
    }
}

?>