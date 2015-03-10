<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
session_start();

$name = $_GET['name'];
if (!is_null($name)) {
    $s = $_SESSION['tekcharts_img_'.$name];
    header ("Content-type: image/png");
    echo base64_decode($s);
}

?>