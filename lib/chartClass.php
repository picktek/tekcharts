<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("utility.php");
require_once ("borderClass.php");
require_once ("backgroundClass.php");
require_once ("marginsClass.php");
require_once ("chartDatasetClass.php");
require_once ("chartBeanClass.php");
require_once ("chartImageMapClass.php");

define ("TC_CHART_AXIS_MIN_MARGIN", 10);
/**
 * Ancestor class for all charts
 *
 */
class Chart extends ChartBean 
{
    /**
     * Default constructor
     *
     * @return Chart
     */
    function Chart () {
        $this->border = new Border ();
        $this->background = new Background ();
    }
    

    /**
     * Property: opacity
     *
     * @var int
     */
    var $alpha = 100;    
    /**
     * Property: width
     *
     * @var int
     */
    var $width;
    /**
     * Property: height
     *
     * @var int
     */
    var $height;    
    /**
     * Property: border
     *
     * @var Border
     */    
    var $border = null;
    /**
     * Property: background
     *
     * @var Background
     */
    var $background = null;
    /**
     * Property: margins
     *
     * @var Margins
     */
    var $margins = null;

    /**
     * Property: data
     *
     * @var array of ChartDataset
     */
    var $data = null;
    
    /**
     * Property: array() of axii
     *
     * @var array()
     */
    var $axii = null;
    
    /**
     * Property: array of steps
     *
     * @var array()
     */
    var $steps = null;
    
    /**
     * ChartDataSeries instance
     *
     * @var ChartDataSeries
     */
    var $dataSeries = null;
    
    /**
     * Margins of chart
     *
     * @var Margins
     */
    var $axisMargins = null;

    /**
     * Controls antialiasing usage
     *
     * @var boolean
     */
    var $antiAlias = true;
    /**
     * Property: count of steps
     *
     * @var int
     */
    var $stepCount = 10;
    /**
     * Property: image map
     *
     * @var ChartImageMap
     */
    var $imageMap = null;
    
    
    /**
     * Renders chart
     *
     * @param ChartCanvas $canvas
     *  Canvas to which this chart belongs to, can be null
     * @return Image
     */
    function render ($canvas) {
    }
    
    /**
     * Returns axis steps for chart
     *
     * @return unknown
     */
    function getSteps () {
        if ($this->data == null)
            return null;
        
        if ($this->steps == null) {
            $this->dataSeries = new ChartDataSeries ($this->data);
            $this->dataSeries->set ("stepCount", $this->stepCount);
            $this->steps = $this->dataSeries->getSteps ();
        }

        return $this->steps;
    }
    
    /**
     * Returns labels for keys
     *
     * @return array()
     */
    function getKeyLabels () {
        if ($this->dataSeries == null) {
            $steps = $this->getSteps ();
        } 

        $ar = $this->dataSeries->mergeDatasets ($this->data, 'keys');
        return $ar;
    }
    
    /**
     * Returns labels for values
     *
     * @return array()
     */
    function getValueLabels () {
        if ($this->dataSeries == null) {
            $steps = $this->getSteps ();
        } 
        else {
            $steps = $this->steps;
        }
        
        $max = $this->dataSeries->getMaxDatasetValue ('values');
        $max = $this->dataSeries->getMaxValueForAxis ($max);
        list ($a, $b, $maj, $c) = $steps;
        $num = 100/$maj + 1;
        $s = $max*$maj/100;
        $labels = array ();
        for ($i = 0; $i < $num; $i++) {
            $labels[$i] = $s;
            $s += $max*$maj/100;
        }        
        
        
        
        return $labels;
    }
    
    /**
     * Automatically adjusts axis margins so that all labels and stuff fits on screen
     *
     */
    function setAxiiMargins () {
        foreach ($this->axii as $axis) {
            if ($axis->get("showLabels")) {
                $labelFont = $axis->get ("labelFont");
                $thickness = $axis->get ("thickness");
                $add = TC_AXIS_LABEL_MARKER_OFFSET;
                
                if ($axis->keyAxis)
                    $labels = $this->getKeyLabels ();
                else
                    $labels = $this->getValueLabels ();
                    
                $maxLabelDim = 0;
                foreach ($labels as $label) {
                    $si = new ChartStringItem ($label, $labelFont);
                    $dim = max ($si->width (), $si->height ());
                    $maxLabelDim = max ($dim, $maxLabelDim);
                }
                
                $maxLabelDim += $thickness + $add + TC_CHART_AXIS_MIN_MARGIN;
                
                switch ($axis->get("type")) {
                    case TC_AXIS_TYPE_X_TOP:
                        $this->axisMargins->set ("top", max ($this->axisMargins->get ("top"), $maxLabelDim));
                        break;
                    case TC_AXIS_TYPE_X_BOTTOM:
                        $this->axisMargins->set ("bottom", max ($this->axisMargins->get ("bottom"), $maxLabelDim));
                        /* TODO */
                        /* make sure we have enough space for label fitting for all other cases */
                        $this->axisMargins->set ("right", max ($this->axisMargins->get ("right"), $maxLabelDim/2));
                        break;
                    case TC_AXIS_TYPE_Y_LEFT:
                        $this->axisMargins->set ("left", max ($this->axisMargins->get ("left"), $maxLabelDim));
                        break;
                    case TC_AXIS_TYPE_Y_RIGHT:
                        $this->axisMargins->set ("right", max ($this->axisMargins->get ("right"), $maxLabelDim));
                        break;
                }
            }
        }
    }

    /**
     * Draws all axii 
     *
     * @param Image $img
     */
    function drawAxii ($img) {
    
        if ($this->axisMargins == null) {
            $this->axisMargins = new Margins (TC_CHART_AXIS_MIN_MARGIN, TC_CHART_AXIS_MIN_MARGIN, 
                                              TC_CHART_AXIS_MIN_MARGIN, TC_CHART_AXIS_MIN_MARGIN);
            /* TODO remove this shit */
            //$this->axisMargins->set ("right", 50);
                                              
            $this->setAxiiMargins ();
        }
        
        if ($this->axii != null) {
            foreach ($this->axii as $key => $axis) {
                $this->axii[$key]->render ($img, $this);
            }
        }
    }
    
    /**
     * Returns axis which has keys
     *
     * @return ChartAxis
     */
    function getKeyAxis () {
        foreach ($this->axii as $key => $axis) {
            if ($this->axii[$key]->keyAxis) {
                return $axis;
            }
        }        
        
        return null;
    }
    
    /**
     * Returns axis which has values 
     *
     * @return ChartAxis
     */    
    function getValueAxis () {
        foreach ($this->axii as $key => $axis) {
            if (!$this->axii[$key]->keyAxis) {
                return $axis;
            }
        }        
        
        return null;
    }
    
    
    function beginImageMap ($name) {
        $this->imageMap = new ChartImageMap ($name);
    }
}

?>