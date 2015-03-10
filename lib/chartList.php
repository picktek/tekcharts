<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("config.php");

/**
 * Helper function to include all chart files in specified directory.
 * Chart file is everthing that corresponds to this mask: [a-zA-Z0-9]+_chart\.php
 *
 * @param string $chartsDir
 *  Directory where chart classes are.
 * @param array $ignoreFiles
 *  Array of file/directory names which will be ignored.
 * @param regex $mask
 *  Patter to match chart class files to.
 */
function loadCharts ($chartsDir = "./charts", $ignoreFiles = array ('.', '..'), $mask = "[a-zA-Z0-9]+_chart\.php") {
    $chartsDir = TC_SYSTEM_PATH . "/" . $chartsDir;
    if (is_dir ($chartsDir) && ($handle = opendir ($chartsDir))) {
        while ($file = readdir ($handle)) {
            if (!in_array ($file, $ignoreFiles)) {
                if (!is_dir("$chartsDir/$file") && eregi ($mask, $file)) {
                    $filename = "$chartsDir/$file";
                    include_once ($filename);
                }
            }
        }

        closedir($handle);
    }
}
?>