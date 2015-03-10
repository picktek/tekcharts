<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
/**
 * Probably deprecated!
 *
 */
class ChartException {
    var $error = "";
    function ChartException ($errorMessage) {
        $this->error = $errorMessage;
    }   
    
    function toString () {
        return "Exception has occured: " . $this->error;
    }
    
    function printStackTrace () {
        print debug_backtrace ();
    }
}

?>