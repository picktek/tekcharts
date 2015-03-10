<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartBeanClass.php");

/**
 * Class which represents margins for various objects
 *
 */
class Margins extends ChartBean 
{
    /**
     * Default constructor
     *
     * @param int $left
     * @param int $top
     * @param int $right
     * @param int $bottom
     * @return Margins
     */
    function Margins ($left = 0, $top = 0, $right = 0, $bottom = 0) {
        $this->left = $left;
        $this->top = $top;
        $this->right = $right;
        $this->bottom = $bottom;
    }

    /**
     * Property: top margin
     *
     * @var int
     */    
    var $top = 0;
    /**
     * Property: right margin
     *
     * @var int
     */    
    var $right = 0;
    /**
     * Property: bottom margin
     *
     * @var int
     */    
    var $bottom = 0;
    /**
     * Property: left margin
     *
     * @var int
     */    
    var $left = 0;

}

?>