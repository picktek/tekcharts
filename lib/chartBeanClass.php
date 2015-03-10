<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
/**
 * Class that all other bean classes in TekCharts are required to extend
 * Allows accessing properties in form $obj->get("prop") and $obj->set("prop", $value)
 */
class ChartBean {
    /**
     * Get bean property.
     *
     * @param string $property
     *  Name of property.
     * @return property_value
     */
    function get ($property) {
        $vars = get_class_vars (get_class ($this));
        //$reflection = new ReflectionClass (get_class ($this));
        //$vars = $reflection->getdefaultProperties ();        

        if (array_key_exists($property, $vars)) {
            return $this->$property;
        }
        
        die ("Property " . $property . " not found in " . get_class ($this));
    }

    /**
     * Set bean property.
     *
     * @param string $property
     *  Name of property.
     * @param property_value $p_value
     *  Value of property.
     * @return property_value
     */
    function set ($property, $p_value) {
        $vars = get_class_vars (get_class ($this));
        //$reflection = new ReflectionClass (get_class ($this));
        //$vars = $reflection->getdefaultProperties ();        
        
        if (array_key_exists($property, $vars)) {
           // if (is_a ($vars[$property], get_class($p_value)))
            $this->$property = $p_value;
    		
            return $this->$property;
        }

        die ("Property " . $property . " not found in " . get_class ($this));
    }
        
}

?>