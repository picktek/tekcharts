<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
/**
 * Deprecated!
 *
 */
class ChartStepsFormula {
    public function getSteps ($formula, $datasets, $dimensionMin, $dimensionMax) {
        
        if ($formula == "default") {
            $max = -100000;
            foreach ($datasets as $dataset) {
                foreach ($dataset as $name => $value) {
                    if ($value > $max)
                        $max = $value;
                }
            }
            
            $numSteps = 10;
            $stepSize = $max/$numSteps;
            $steps = array();
            for ($i = 0; $i < $numSteps; $i++) {
                $steps[$i] = ($dimensionMax-$dimensionMin)*($i+1)*$stepSize/$max;
            }
                    
        }
        
        
    }
}