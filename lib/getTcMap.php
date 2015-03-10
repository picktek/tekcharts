<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
session_start();

function getTcMap ($name) {
    if (!is_null($name)) {
        $s = $_SESSION['tekcharts_map_'.$name];
        return $s;
    }
}


?>