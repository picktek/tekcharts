<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("utility.php");
require_once ("chartDatasetClass.php");

/**
 * Class for manipulation of ChartDataset groups 
 * Used to calculate steps on axii and max axis value
 */
class ChartDataSeries extends ChartBean 
{
    /**
     * Each argument should be array of ChartDataset class
     *
     * @return ChartDataSeries
     */
    function ChartDataSeries ($datasets) {
        
        $this->datasets = $datasets;
    }

    /**
     * Property: datasets
     *
     * @var array()
     */
    var $datasets = null;

    /**
     * Property: count of steps
     *
     * @var int
     */
    var $stepCount = 10;
    
    /**
     * Get values from multiple datasets (in one group) by key.
     *
     * @param string $group
     * Group name
     * @param string $key
     * @return array()
     */
    function getGroupedDatasetsValuesByKey ($group, $key) {
        $v = array();
        if ($this->datasets != null) {
            foreach ($this->datasets as $data) {
                $grp = $data->get("group");
                if ($grp != TC_DATASET_GENERAL_GROUP && $grp == $group) {
                    $d = $data->get("data");
                    if (array_key_exists ($key, $d))
                        array_push ($v, $d[$key]);
                }
            }
        }
        
        return $v;        
    }
    
    /**
     * Get grouped datasets
     *
     * @param string $group
     * @return array()
     * array of ChartDataset
     */
    function getDatasetsByGroup ($group) {
        $ds = array ();
        if ($this->datasets != null) {
            foreach ($this->datasets as $data) {
                $grp = $data->get("group");
                if ($grp != TC_DATASET_GENERAL_GROUP && $grp == $group) {
                    array_push($ds, $data);
                }
            }
        }
        
        return $ds;
    }    
    
    /**
     * Get sum() of values with same key and group
     *
     * @param string $group
     * @param string$key
     * @return var
     */
    function getSummaryValueByGroup ($group, $key) {
        $v = 0;
        if ($this->datasets != null) {
            foreach ($this->datasets as $data) {
                $grp = $data->get("group");
                if ($grp != TC_DATASET_GENERAL_GROUP && $grp == $group) {
                    $d = $data->get("data");
                    if (array_key_exists ($key, $d))
                        $v += $d[$key];
                }
            }
        }
        
        return $v;
    }
    
    /**
     * Get all group names
     *
     * @return array()
     */
    function getDatasetGroupNames () {
        $groups = array ();
        
        $i = 0;
        foreach ($this->datasets as $data) {
            $group = $data->get ("group");
            if ($group != TC_DATASET_GENERAL_GROUP) {
                $groups[$i] = $group;
            }
        }
        
        return array_unique ($groups);
    }

    /**
     * Gell all keys by group
     *
     * @param string $g
     * Group name
     * @return array()
     */
    function getGroupedDatasetsKeys ($g = TC_DATASET_GENERAL_GROUP) {
        $keys = array ();
        
        $i = 0;
        foreach ($this->datasets as $data) {
            $group = $data->get ("group");
            if ($group != TC_DATASET_GENERAL_GROUP && $group == $g) {
                $keys = array_merge ($keys, array_keys($data->get("data")));
            }
        }
        
        return array_unique ($keys);
    }
    
    /**
     * Get summary values of all groups 
     *
     * @return array()
     */
    function getGroupedDatasetsAdditionalValues () {
        $vals = array ();
        
        $groups = $this->getDatasetGroupNames ();

        foreach ($groups as $group) {
            $keys = $this->getGroupedDatasetsKeys ($group);
            foreach ($keys as $key) {
                array_push ($vals, $this->getSummaryValueByGroup($group, $key));
            }
        }

        return $vals;
    }
    
    /**
     * Check if keys are strings
     *
     * @return boolean
     */
    function areKeysStringType () {
        foreach ($this->datasets as $data) {
            if ($data->getKeyType () == TC_DATASET_KEY_TYPE_NUMERIC)
                return false;
        }
        
        return true;
    }
    
    /**
     * Get maximum dataset length
     *
     * @return unknown
     */
    function getKeysMaxLength () {
        $max = 0;
        foreach ($this->datasets as $data) {
            $count = $data->getDataLength ();
            if ($count > $max)
                $max = $count;
        }
        
        return $max;
    }
    
    /**
     * Exponent of value
     *
     * @param var $val
     * @return var
     */
    function expon ($val) {
        $expon = 0;
        while ($val > 10) {
            $val = $val/10;
            $expon++;
        }
        
        return array ($val, $expon);
    }
    
    /**
     * $val should be in range [0, 10)
     *
     * @param unknown_type $val
     */
    function goodCeil ($val) {
        $frac = $val - intval($val);
        
        if ($frac < 0.5)
            $val = intval($val) + 0.5;
        else
            $val = ceil ($val);
            
        return $val;
    }
    /**
     * Get max value for axis by max value of dataset
     *
     * @param var $val
     * @return var
     */
    function getMaxValueForAxis ($val) {
        $val2 = ($val*1.2);
        list ($m, $e) = $this->expon($val2);
        
        $m = $this->goodCeil($m) * pow (10, $e);
        
        return $m;        
    }
    
    /**
     * Get numerical step size by max value
     *
     * @param  var $val
     * @return var
     */
    function getNumericStepByMax ($val) {
        $m = $this->getMaxValueForAxis($val);
        
        $maj = $m/$this->stepCount;                
        return ($maj/$m)*100;        
    }

    /**
     * Returns all datasets in data series merged by either keys or values parts
     *
     * @param array or ChartDataset $arr
     * @param string $type
     *  'keys' for merge by keys and 'values' for merge by values
     * @return array()
     */
    function mergeDatasets ($arr, $type='keys') {
        $ar = array ();
        foreach ($arr as $data) {
            $data = $data->get("data");
            if ($type == 'keys')
                $v = array_keys ($data);
            else
                $v = array_values ($data);
            $ar = array_merge ($ar, $v);
        }        
        
        return $ar;
    }
    
    /**
     * Reurns _absolute_ maximal value in merged datasets
     *
     * @param string $type
     * 'keys' for merge by keys and 'values' for merge by values
     * @return <maximal value>
     */
    function getMaxDatasetValue ($type) {
        // find absolute max value
        $ar = $this->mergeDatasets ($this->datasets, $type);
        
        if ($type == "values") {
            $add = $this->getGroupedDatasetsAdditionalValues ();
            $ar = array_merge ($ar, $add);
        }
        
        //array_push()
        $max = null;
        foreach ($ar as $val) {
            if ($max == null || abs($val) > $max) {
                $max = abs ($val);
            }
        }        
        
        return $max;
    }
    
    /**
     * Get array() of major and minor steps
     *
     * @param unknown_type $type
     * @return unknown
     */
    function getNumericSteps ($type) {
        // merge arrays
        $max = $this->getMaxDatasetValue ($type);
        
        $singleMajorStep = $this->getNumericStepByMax ($max);        
        $singleMinorStep = 100/5;
        return array ($singleMajorStep, $singleMinorStep);
    }
    
    /**
     * Returns both major and minor steps for axis in array, in percents of axis total length
     *
     */
    function getSteps () {
        $steps = array ();

        if ($this->datasets == null)
            return null;

        // first find steps for key axis in datasets
        // if we have strings in keys such as "Jan", "Feb" etc
        if ($this->areKeysStringType ()) {
            $count = $this->getKeysMaxLength ();
            
            $singleMajorStep = 98/($count); // we take 94% because we might need a slight offset from end of an axis
            $singleMinorStep = 100/5;
            
            $steps[0] = $singleMajorStep;
            $steps[1] = $singleMinorStep;
        }
        else {
            
            list ($steps[0], $steps[1]) = $this->getNumericSteps ('keys');
        }
        
        // second find steps for value axis
        list ($steps[2], $steps[3]) = $this->getNumericSteps ('values');
        
        return $steps;
    }
}

?>