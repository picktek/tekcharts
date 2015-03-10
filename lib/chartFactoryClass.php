<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartList.php");
require_once ("chartExceptionClass.php");

/**
 * Class which is used to create Chart's children.
 *
 */
class ChartFactory
{
    /**
     * Property: path to charts directory
     *
     * @var unknown_type
     */
    var $chartsPath = "charts/";
    
    
    /**
     * Creates Chart of $type type
     *
     * @param string $type
     * 'genericBar', 'simpleBar', 'pie', ...
     * @return Chart 
     */
    function createChart ($type) {
        static $chartsLoaded = false;
        
        if (!$chartsLoaded) {
            loadCharts ();
            $chartsLoaded = true;
        }
        
        if (($type == null) || (strlen ($type) == 0))
            return null;
            
        $className = $type."Chart";

        if (class_exists ($className) && strtoupper(get_parent_class($className)) == strtoupper("Chart")) {
            $chart = new $className ();
            return $chart;
        }
        
        die ("Chart of type: " . $type . " can not be instantiated.");
    }
}

?>