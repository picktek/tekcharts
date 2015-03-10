<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");

/**
 * Class which allows construction of imagemap matching chart output image
 *
 */
class ChartImageMap extends ChartBean 
{
    /**
     * Default consructor
     * @param string $name 
     * name of image map
     * @return ChartImageMap
     */
    function ChartImageMap ($name) {
        $this->mapHtml = '<map name="'.$name.'">';
        $shapes = array (); 
    }
    
    /**
     * Property: html of image map
     *
     * @var string
     */    
    var $mapHtml = "";

    var $shapes = array ();
    
    function addRectLink ($x1, $y1, $x2, $y2, $value, $link) {
        $i = count($this->shapes);
        
        $this->shapes[$i]['x1'] = $x1;
        $this->shapes[$i]['x2'] = $x2;
        $this->shapes[$i]['y1'] = $y1;
        $this->shapes[$i]['y2'] = $y2;
        $this->shapes[$i]['value'] = $value;
        $this->shapes[$i]['type'] = 'rect';
        $this->shapes[$i]['link'] = $link;
    }
    
    function addPolyLink ($points, $value, $link) {
        $i = count($this->shapes);
        
        $this->shapes[$i]['points'] = $points;
        $this->shapes[$i]['value'] = $value;
        $this->shapes[$i]['type'] = 'poly';
        $this->shapes[$i]['link'] = $link;
    }    
    
    function addCircleLink ($x1, $y1, $r, $value, $link) {
        $i = count($this->shapes);
        $this->shapes[$i]['x1'] = $x1;
        $this->shapes[$i]['y1'] = $y1;
        $this->shapes[$i]['r'] = $r;
        $this->shapes[$i]['value'] = $value;
        $this->shapes[$i]['type'] = 'circle';
        $this->shapes[$i]['link'] = $link;
    }

    function fixCoords ($dx, $dy) {
        for ($l = 0; $l < count ($this->shapes); $l++) {
            $shape = $this->shapes[$l];
            if ($shape['type'] == 'rect') {
                $shape['x1'] += $dx;
                $shape['y1'] += $dy;
                $shape['x2'] += $dx;
                $shape['y2'] += $dy;
            }
            if ($shape['type'] == 'circle') {
                $shape['x1'] += $dx;
                $shape['y1'] += $dy;
            }
            if ($shape['type'] == 'poly') {
                $pts = $shape['points'];
                $len = count ($pts);
                for ($i = 0; $i < $len; $i++) {
                    $pts[$i]['x'] += $dx;
                    $pts[$i]['y'] += $dy;
                }
                $shape['points'] = $pts;
            }
            
            $this->shapes[$l] = $shape;
        }
        
    }
    /**
     * Enter description here...
     *
     * @return string
     * HTML of image map
     */
    function getMap () {
        
        for ($l = 0; $l < count ($this->shapes); $l++) {
            $shape = $this->shapes[$l];
            
            if ($shape['type'] == 'rect') {
                $this->mapHtml .= '<area shape="rect" coords="'.round($shape['x1']).','.round($shape['y1']).','.
                                    round($shape['x2']).','.round($shape['y2']).'" href="'.$shape['link'].'" title="'.$shape['value'].'"/>';
            }
            if ($shape['type'] == 'circle') {
                $this->mapHtml .= '<area shape="circle" coords="'.round($shape['x1']).','.round($shape['y1']).','.round($shape['r']).'" href="'.$shape['link'].'" title="'.$shape['value'].'"/>';
            }
            if ($shape['type'] == 'poly') {
                $this->mapHtml .= '<area shape="poly" coords="';
                
                $pts = $shape['points'];
                $len = count ($pts);
                for ($i = 0; $i < $len; $i++) {
                    if ($i == 0)
                        $this->mapHtml .= (round($pts[$i]['x']).','.round($pts[$i]['y']));
                    else
                        $this->mapHtml .= (','.round($pts[$i]['x']).','.round($pts[$i]['y']));
                }
                
                $this->mapHtml .= '" href="'.$shape['link'].'" title="'.$shape['value'].'"/>';
            }
        }

        return $this->mapHtml.'</map>';
    }
}

?>