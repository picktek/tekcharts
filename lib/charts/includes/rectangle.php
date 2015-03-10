<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
class Rectangle {
    
    var $x1, $x2, $y1, $y2;
    var $color;
    
    function Rectangle ( $x1, $y1, $x2, $y2, $color) {
        $this->x1 = $x1;
        $this->y1 = $y1;
        $this->x2 = $x2;        
        $this->y2 = $y2;
        $this->color = $color;        
    }
    
}

?>