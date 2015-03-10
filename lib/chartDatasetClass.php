<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");

define ("TC_DATASET_KEY_TYPE_STRING", "string");
define ("TC_DATASET_KEY_TYPE_NUMERIC", "numeric");
define ("TC_DATASET_GENERAL_GROUP", "all");

define ("TC_DATASET_LINKS_ONEFORALL", 0);
define ("TC_DATASET_LINKS_ONEFOREACH", 1);
 
/**
 * Class wchich represents dataset.
 * Also contants dataset color controlling utilities.
 *
 */
class ChartDataset extends ChartBean {
    function ChartDataset () {
        $this->color = $this->getNextPresetColor ();
    }
    
    
    function getNextPresetColor () {
        static $colIndex = 0;
        static $colors = null;
        static $initialized = false;
        
        if (!$initialized) {
            $colIndex = 0;
            $colors = array ();
            /*
            $colors[0] = new Color ("D8B750");
            $colors[1] = new Color ("94B042");
            $colors[2] = new Color ("F8B98E");
            $colors[3] = new Color ("ADC7D8"); */

            $colors[0] = new Color ("7A94A9");
            $colors[1] = new Color ("67850F");
            $colors[2] = new Color ("AF8B1D");
            $colors[3] = new Color ("B66F41");

            $initialized = true;

        }
            
        $col = $colors[$colIndex];
        $colIndex += 1;
        
        return $col;   
    }    
    /**
     * Property: data
     *
     * @var array
     */
    var $data = array ();
    /**
     * Property: data hrefs
     *
     * @var array
     */
    var $dataLinks = array ();    
    /**
     * Property: data hrefs
     *
     * @var array
     */
    var $dataLinksType = TC_DATASET_LINKS_ONEFOREACH;    
    /**
     * Property: data length
     *
     * @var int
     */
    //var $length = 0;
    /**
     * Property: data type
     *
     * @var string
     */
    var $dataType;
    /**
     * Property: render as: 'bar', 'plot' etc, interpreted in graph type context
     *
     * @var string
     */
    var $renderAs;
    /**
     * Property: dataset name
     *
     * @var string
     */
    var $name;
    /**
     * Property: dataset color
     *
     * @var Color
     */
    var $color;
    /**
     * Property: Indicates which group this dataset belongs to
     *
     * @var string
     */
    var $group = TC_DATASET_GENERAL_GROUP;
    /**
     * Property: Indicates whether keys are string type
     *
     * @var boolean
     */
    var $keyStringType = false;    
    
    /**
     * number of entries in data
     *
     * @return int
     */
    function getDataLength () {
        return count ($this->data);
    }
    
    /**
     * Return maximal value from dataset
     *
     * @return var
     */
    function maxValue () {
        $max = null;
        foreach ($this->data as $key => $value) {
            if ($max == null || $value > $max) {
                $max = $value;
            }
        }
        
        return $max;
    }
    
    /**
     * Return minimal value from dataset
     *
     * @return var
     */
    function minValue () {
        $min = null;
        foreach ($this->data as $key => $value) {
            if ($min == null || $value < $min) {
                $min = $value;
            }
        }
        
        return $min;
    }
    
    /**
     * Return maximal key from dataset (if numeric)
     *
     * @return var
     */    
    function maxKey () {
        $max = null;
        foreach ($this->data as $key => $value) {
            if ($max == null || $key > $max) {
                $max = $key;
            }
        }
        
        return $max;
    }
    
    /**
     * Return minimal key from dataset (if numeric)
     *
     * @return var
     */
    function minKey () {
        $min = null;
        foreach ($this->data as $key => $value) {
            if ($min == null || $key < $min) {
                $min = $key;
            }
        }
        
        return $min;
    }    
    
    
    function getMinMaxKeyValue () {
        $minK = null;
        $minV = null;
        $maxK = null;
        $maxV = null;
        
        foreach ($this->data as $key => $value) {
            if ($minK == null || $key < $minK) {
                $minK = $key;
            }
            if ($minV == null || $value < $minV) {
                $minV = $value;
            }
            if ($maxK == null || $key > $maxK) {
                $maxK = $key;
            }
            if ($maxV == null || $value > $maxV) {
                $maxV = $key;
            }
        }
        
        return array ($minK, $maxK, $minV, $maxV);
    }
    
    /**
     * Returns key type, either string or numeric
     *
     */
    function getKeyType () {
        $type = TC_DATASET_KEY_TYPE_NUMERIC;
        foreach ($this->data as $key => $value) {
            if (is_string($key) || $this->keyStringType) {
                $type = TC_DATASET_KEY_TYPE_STRING;
                $this->keyStringType = true;
                break;
            }
        }        
        $this->keyStringType = false;
        return $type;
    }

    function getDataLink ($key) {
        if ($this->dataLinksType == TC_DATASET_LINKS_ONEFORALL)
            return $this->dataLinks;
        else 
            return $this->dataLinks[$key];
    }
}

?>