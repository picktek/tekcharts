<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
  
define('THREED_HEIGHT',    35);    

class  ThreeDArc {
    
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
    var $threed_height;
    var $start, $end;
    
    var $color, $shadow, $hilite;  
    
    function ThreeDArc( $x, $y, $width, $height, $threed_height = THREED_HEIGHT, $start, $end, $color) {
        
        $this->x = $x;
        $this->y = $y;
        $this->height=$height;
        $this->width=$width;
        $this->color = $color;  
        $this->threed_height = $threed_height;
        $this->start = $start;
        $this->end = $end;

        $this->getHiLoColors();
    }
    
    
    function render ( $img ) {
        //-- The body
        $clr = $this->color;       
        $sclr = $this->shadow;       
        $hclr = $this->hilite;
        $main_clr = ImageColorAllocate($img, $clr->r, $clr->g, $clr->b);    
        $hilite_clr = ImageColorAllocate($img, $hclr->r, $hclr->g, $hclr->b);    
        $shadow_clr = ImageColorAllocate($img, $sclr->r, $sclr->g, $sclr->b);    
                            
        for ($i = $this->y+$this->threed_height; $i > $this->y; $i--) {
            imagefilledarc($img, $this->x, $i, $this->width, $this->height, $this->start, $this->end, $shadow_clr, IMG_ARC_ROUNDED);
        }
        
        imagefilledarc ($img, $this->x, $this->y, $this->width, $this->height, $this->start, $this->end, $main_clr, IMG_ARC_ROUNDED);
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