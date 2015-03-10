<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
define('THREED_SHADOW',    .25);     /** Shadow Color is 15% darker */
define('THREED_HILITE',    .35);     /** Highlight Color is 15% brighter */
define('THREED_WIDTH',      35);    /** The width of shadow and hilite */
define('THREED_ANGLE',      .7);    /** The width of shadow and hilite */

class  ThreeDBar {
    
    /**
     * Attention: $x and $y are the coordinates of the bottom
     * centerpoint of the graph. This unusual position is picked
     * because we need this graph to be used within a charting program
     * and there that's what makes sense.
     *
     * @param int $x
     * @param int $y
     * @param int $height
     * @param int $width
     * @param Color $color
     */
    var $x, $y;
    var $height, $width;
    
    var $color, $shadow, $hilite;  
    
    var $shapePoints = null;
    
    function ThreeDBar( $x, $y, $width, $height, $color, 
                        $threed_width = THREED_WIDTH ) {
        
        $this->x = $x;
        $this->y = $y;
        $this->height=$height;
        $this->width=$width;
        $this->color = $color;  
        $this->threed_width = $threed_width;

        $this->getHiLoColors();
    }
    
    
    function render ( $img ) {
        $pts = array ();
        $rect = $this->getRectangle();
        
        //-- The body
        $clr = $this->color;       
        $tmp_clr = ImageColorAllocate($img, 
                        $clr->r, $clr->g, $clr->b);    
        imagefilledrectangle($img, 
                            $rect->x1, $rect->y1, $rect->x2, $rect->y2, 
                            $tmp_clr);
                  
        $pts[0]['x'] = $rect->x1;
        $pts[0]['y'] = $rect->y1;
                            
                                      
        $dh = $this->threed_width * asin ( THREED_ANGLE );
        $dw = $this->threed_width * acos ( THREED_ANGLE );
        
        //-- HIGHLIGHT
        $values = array(
            $rect->x1,  $rect->y1,
            $rect->x1+$dw,  $rect->y1-$dh,
            $rect->x2+$dw,  $rect->y1-$dh,
            $rect->x2,  $rect->y1,
            );
        
        $pts[1]['x'] = $rect->x1+$dw;
        $pts[1]['y'] = $rect->y1-$dh;
        $pts[2]['x'] = $rect->x2+$dw;
        $pts[2]['y'] = $rect->y1-$dh;
            
        $clr = $this->hilite;       
        $tmp_clr = ImageColorAllocate($img, 
                        $clr->r, $clr->g, $clr->b);
        imagefilledpolygon($img, $values, 4, $tmp_clr);

        
        //-- SHADOW
        $values = array(            
            $rect->x2,  $rect->y1,
            $rect->x2+$dw,  $rect->y1-$dh,
            $rect->x2+$dw,  $rect->y2-$dh,    
            $rect->x2,  $rect->y2,
                    
            );
        
        $clr = $this->shadow;       
        $tmp_clr = ImageColorAllocate($img, 
                        $clr->r, $clr->g, $clr->b);
        imagefilledpolygon($img, $values, 4, $tmp_clr);
        

        $pts[3]['x'] = $rect->x2+$dw;
        $pts[3]['y'] = $rect->y2-$dh;
        $pts[4]['x'] = $rect->x2;
        $pts[4]['y'] = $rect->y2;
        $pts[5]['x'] = $rect->x1;
        $pts[5]['y'] = $rect->y2;
        
        $this->shapePoints = $pts;
    
    }
    
    function getRectangle() {
        $x1 = $this->x - $this->width/2;
        $x2 = $this->x + $this->width/2;
        
        $y1 = $this->y - $this->height;
        $y2 = $this->y;
        
        return new Rectangle ( $x1,$y1,$x2,$y2,$color);        
    }
    
    function getShapePoly () {
        return $this->shapePoints;
    }
    
    /**
     * Calculate colors for shadow and highlight
     */
    function getHiLoColors() {
        $r = $this->color->r;
        $g = $this->color->g;
        $b = $this->color->b;
        
        $rsh = $r * (1 - THREED_SHADOW);       
        $gsh = $g * (1 - THREED_SHADOW);       
        $bsh = $b * (1 - THREED_SHADOW);
               
        $rhi = $r * (1 + THREED_HILITE);       
        $ghi = $g * (1 + THREED_HILITE);       
        $bhi = $b * (1 + THREED_HILITE);       
        
        $this->shadow = new Color( $rsh, $gsh, $bsh);
        $this->hilite = new Color( $rhi, $ghi, $bhi);
    }
    
    
    function printme () {
        logit ( $this ) ;
    }
    
}
?>