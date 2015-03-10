<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
session_start();

require_once ("borderClass.php");
require_once ("backgroundClass.php");
require_once ("marginsClass.php");
require_once ("chartClass.php");
require_once ("chartBeanClass.php");
require_once ("chartLegendClass.php");
require_once ("chartStringItemClass.php");
require_once ("utility.php");


define ("TC_CHART_TITLES_CHART_PADDING", 5);
define ("TC_CHART_CHART_LEGEND_PADDING", 5);
define ("TC_CHART_BORDER_TITLES_PADDING", 5);
define ("TC_CHART_LEGEND_BORDER_PADDING", 5);


define ("TC_TRANSPARENT_COLOR_R", 127);
define ("TC_TRANSPARENT_COLOR_G", 0);
define ("TC_TRANSPARENT_COLOR_B", 127);
define ("TC_TRANSPARENT_COLOR_A", 127);

/**
 * Class which contains all other TekCharts objects such as titles, legend and chart itself
 *
 */
class ChartCanvas extends ChartBean 
{
    function ChartCanvas ($width, $height) {
        $this->width = $width;
        $this->height = $height;
        
        $this->border = new Border ();
        $this->background = new Background ();
        $this->legend = new ChartLegend ();
        $this->chartMargins = new Margins (5, 0, 5, 0);
        $this->title = new ChartStringItem ("", 5);
        $this->subTitle = new ChartStringItem ("", 4);
    }
    
    /**
     * Property: width
     *
     * @var int
     */
    var $width;
    /**
     * Property: height
     *
     * @var int
     */
    var $height;
    /**
     * Property: border
     *
     * @var Border
     */
    var $border = null;
    /**
     * Property: background
     *
     * @var Background
     */
    var $background = null; 
    /**
     * Property: title
     *
     * @var ChartStringItem
     */
    var $title = null;     
    /**
     * Property: subTitle
     *
     * @var ChartStringItem
     */
    var $subTitle = null;     
    /**
     * Property: $chartMargins
     *
     * @var Margins
     */
    var $chartMargins = null;     
    /**
     * Property: chart
     *
     * @var Chart
     */
    var $chart = null;     
    /**
     * Property: legend
     *
     * @var ChartLegend
     */
    var $legend = null;        
    /**
     * Property: titlesVisible
     *
     * @var boolean
     */
    var $titlesVisible = true;

function drupal_to_js($var) {
  switch (gettype($var)) {
    case 'boolean':
      return $var ? 'true' : 'false'; // Lowercase necessary!
    case 'integer':
    case 'double':
      return $var;
    case 'resource':
    case 'string':
      return '"'. str_replace(array("\r", "\n", "<", ">", "&"),
                              array('\r', '\n', '\x3c', '\x3e', '\x26'),
                              addslashes($var)) .'"';
    case 'array':
      if (array_keys($var) === range(0, sizeof($var) - 1)) {
        $output = array();
        foreach($var as $v) {
          $output[] = drupal_to_js($v);
        }
        return '[ '. implode(', ', $output) .' ]';
      }
      // Fall through
    case 'object':
      $output = array();
      foreach ($var as $k => $v) {
        $output[] = drupal_to_js(strval($k)) .': '. drupal_to_js($v);
      }
      return '{ '. implode(', ', $output) .' }';
    default:
      return 'null';
  }
}
    
    /**
     * Renders canvas and chart attached to it (if any).
     *
     * @param string $type
     *  Any of these values: 'jpg', 'png', 'gif'
     * @param string $out
     *  Where to output chart image: "scr" - screen, "ses" - session
     * @return Image
     */    
    function render ($type = "png", $out = "scr", $name = "chart") {
        $img = imagecreatetruecolor ($this->width, $this->height);
        imagealphablending($img, true);
        $height = 0;
        $width = $this->width;
        
        // draw bg
        $this->background->render ($img, $this->width, $this->height);
        // draw border
        $this->border->render ($img, $this->width, $this->height);    
        $height += $this->border->get("thickness");
        
        // draw titles
        if ($this->titlesVisible) {
            $height += TC_CHART_BORDER_TITLES_PADDING;
            $this->title->render ($img, $this->title->centerX($this->width), $height);
            $height += $this->title->height ();
            $this->subTitle->render ($img, $this->subTitle->centerX($this->width), $height);
            $height += $this->subTitle->height ();
            $height += TC_CHART_TITLES_CHART_PADDING;
        }
        
        // draw legend
        $legendImg = $this->legend->render ($this->chart->get ("data"));
        if ($legendImg != null) {
            $lw = imagesx ($legendImg);
            $lh = imagesy ($legendImg);
            imagecopymerge ($img, $legendImg, $this->chartMargins->get ("right") + ($width-$lw)/2, $this->height - $lh - TC_CHART_LEGEND_BORDER_PADDING, 0, 0, $lw, $lh, $this->legend->get ("alpha"));
        }
        
        // draw chart
        $cw = $this->width - ($this->chartMargins->get ("left") + $this->chartMargins->get ("right"));
        $ch = $this->height - ($this->chartMargins->get ("top") + $this->chartMargins->get ("bottom") + 
                                $lh + $height + TC_CHART_TITLES_CHART_PADDING + TC_CHART_CHART_LEGEND_PADDING + 
                                $this->border->get("thickness")*2);
        
        if ($this->chart != null) {
            $this->chart->set ("width", $cw);
            $this->chart->set ("height", $ch);
            $chartImg = $this->chart->render($this);
            imagecopymerge ($img, $chartImg, $this->chartMargins->get ("right"), $height + TC_CHART_TITLES_CHART_PADDING, 0, 0, $cw, $ch, $this->chart->get ("alpha"));
        }
    
        if ($this->chart->imageMap != null)
            $this->chart->imageMap->fixCoords ($this->chartMargins->get ("right"), $height + TC_CHART_TITLES_CHART_PADDING);
/*        $im = imagecreatetruecolor(imagesx($img)/2, imagesy ($img)/2);

        imagecopyresampled($im, $img, 0, 0, 0, 0, imagesx($img)/2, imagesy($img)/2, imagesx($im), imagesy($im));
        imagedestroy($img);*/
    
        if ($out == "scr") {
            header ("Content-type: image/png");
            imagepng($img);
        }
        else if ($out == "ses") {
            ob_start();
            imagepng($img);
            $s = base64_encode (ob_get_contents ());
            ob_end_clean();
        
            $_SESSION['tekcharts_img_'.$name] = $s;
            if ($this->chart->imageMap != null)
                $_SESSION['tekcharts_map_'.$name] = $this->chart->imageMap->getMap ();
            else
                $_SESSION['tekcharts_map_'.$name] = "";
            
        }
        else if ($out == "map") {
            if ($this->chart->imageMap != null)
                return $this->chart->imageMap->getMap ();
        }
        
        //echo "<a href=test_session.php>click me</a>";
        imagedestroy($img);
    }
}

?>