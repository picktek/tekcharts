<?php

  require_once (dirname(__FILE__) . '/../lib/tekCharts.php');

  function req_def ($varname, $defval) {
    return empty($_GET[$varname]) ? $defval : $_GET[$varname];
  }

  function _tekcharts_prepare() {
   $data = cache_get ($cid);
    $concErr = false;
    if ($data && $data != 0) {
      $data = unserialize( $data->data );
      if ($data == null ) $concErr = true;
    } else {
      $concErr = true;
    }
    
    if ($concErr) {
      $data = new ChartDataset ();
      $data->set ("data", array());
    }

  //$width = $_GET['width'];
  //$height = $_GET['height'];
    $height = ($height > 200) ? $height : 200;
  //$title = $_GET['title'];
  //$subTitle = $_GET['subTitle'];
  //$tcg_type = $_GET['graphtype'];
  //$color = $_GET['color'];
  
  $titlesVisible =  (!$title || trim($title) == "") ? false : true;


// create Canvas object and set its properties
    $canvas = new ChartCanvas ($width, $height);
    $canvas->set ("background", new Background());
    
    $canvas->set ("title", new ChartStringItem($title, 5));
    $canvas->set ("subTitle", new ChartStringItem($subTitle, 4));
    $canvas->set ("titlesVisible", false);
    
    $border = new Border ();
    $border->set ("style", TC_BORDER_SOLID);
    $border->set ("visible", false);
    $border->set ("color", new Color ("00000"));
    $canvas->set ("border", $border);
    
    $legend = $canvas->get("legend");
    $lb = $legend->get("border");
    $lb->set("style", TC_BORDER_DOTTED);
    $legend->set("alpha", 80);
    $legend->set ("vertical", false);
    $legend->set ("visible", false);
    $canvas->set ("legend", $legend);
    

    $data->set ("renderAs", $tcg_type);
    $data->set ("color", new Color($color));
    

if ($tcg_type == "bars") {
// use ChartFactory to instantiate bar chart class
    $chart = ChartFactory::createChart("threeDBarMS");
    $cbord = $chart->get("border");
    $cbord->set("visible", false);
    $chart->set("border", $cbord);
// bind dataset to chart
    $chart->set ("data", array ($data));
    $chart->set ("alpha", 80);
    $chart->set ("stepCount", 4);
    $chart->set ("showBack", false);
    $chart->set ("boardColor", "ACBB99");
    $chart->set ("bar3dWidth", 15);
    $chart->set ("labelOffset", 12);
 
    $xAxis = new ChartAxis($chart);
    $xAxis->set ("type", TC_AXIS_TYPE_X_BOTTOM);
    $xAxis->set ("showMinorSteps", false);
    $xAxis->set ("drawGridLines", false);
    $xAxis->set ("drawAxisLine", false);
    $xAxis->set ("drawAxisMarks", false);
    $xAxis->set ("keyAxis", true);
    $xAxis->set ("labelsAngle", -45);
    //$xAxis->set ("labelFont", "");

    
    $yAxis = new ChartAxis($chart);
    $yAxis->set ("type", TC_AXIS_TYPE_Y_LEFT);
    $yAxis->set ("showMinorSteps", false);
    $yAxis->set ("drawGridLines", false);
    $yAxis->set ("showLabels", false);
    $yAxis->set ("drawAxisLine", false);
    $yAxis->set ("drawAxisMarks", false);
    $yAxis->set ("gridLineStyle", TC_LINE_STYLE_SOLID);
    
} else {

    // use ChartFactory to instantiate bar chart class
    $chart = ChartFactory::createChart("bar");
    $cbord = $chart->get("border");
    $cbord->set("visible", false);
    $chart->set("border", $cbord);
// bind dataset to chart
    $chart->set ("data", array ($data));
    $chart->set ("alpha", 80);
    $chart->set ("plotBallSize", 7);
    $chart->set ("showDropDownLines", true);
    $chart->set ("showShadow", true);
    $chart->set ("dropDownColor", new Color("FBCBB8"));
    
      $xAxis = new ChartAxis($chart);
    $xAxis->set ("type", TC_AXIS_TYPE_X_BOTTOM);
    $xAxis->set ("showMinorSteps", false);
    $xAxis->set ("drawAxisMarks", false);
    $xAxis->set ("drawGridLines", false);
    $xAxis->set ("drawAxisLine", false);
    $xAxis->set ("keyAxis", true);
    $xAxis->set ("labelsAngle", -45);
    $xAxis->set ("labelFilter", 4);
    $xAxis->set ("labelFont", 2);
    
    
    $yAxis = new ChartAxis($chart);
    $yAxis->set ("type", TC_AXIS_TYPE_Y_LEFT);
    $yAxis->set ("labelFont", 1);
    $yAxis->set ("drawAxisLine", false);
    $yAxis->set ("showMinorSteps", false);
    $yAxis->set ("drawAxisMarks", false);
    //$yAxis->set ("showLabels", false);
}
    
 
	    
    $chart->set ("axii", array($xAxis, $yAxis));
    $cname = _tekcharts_getUniqName ($tcg_type, $cid);
    $chart->set ("imageMap", new ChartImageMap($cname));
    
   // associate chart with canvas
    $canvas->set ("chart", $chart);

   // visualize the resulting chart
    if ($outImg)
       $canvas->render ('png');
    else	
       return $canvas->render ('png', 'map', $cname);
}

  $width  = req_def('width', 500);
  $height = req_def('height', 400);
  
  $title  = req_def('title', 'Sample Chart');
  $subTitle = req_def('subTitle', 'Sample Chart Subtitle');
  $tcg_type = req_def('graphtype', 'threeDBar');
  $color = req_def('color', '#ffddcc');
  $cid = req_def('cid', 0);

  _tekcharts_prepare ($cid, $tcg_type, $color, $width, $height, $title, $subTitle, true);
