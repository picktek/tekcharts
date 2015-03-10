<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
require_once ("chartDatasetClass.php");

/**
 * Draw string using GD fonts centered
 *
 * @param Image $img
 * image to draw on
 * @param int $font
 * @param int $cy
 * y coordinate
 * @param String $text
 * @param int $color
 * GD color
 */
function imagestringcentered ($img, $font, $cy, $text, $color) {
    while (strlen ($text) * imagefontwidth ($font) > imagesx ($img)) {
        if ($font > 1) { $font--; }
        else { break; }
    } 
    
    imagestring($img, $font, imagesx ($img) / 2 - strlen ($text) * imagefontwidth ($font) / 2, $cy, $text, $color);
}    

    


/*
|------------------------------------------------------------
| This function fixes an issue with imagettftext when
| printing some fonts at small point sizes(Like Myriad Pro)
|
| Author: Luke Scott
|------------------------------------------------------------
*/

/**
* The text size used before it's resized.
*/

define( 'DRAW_TTF_BASE', 72);

/**
* Draws TTF/OTF text on the destination image with best quality.
* The built in function imagettftext freaks out with small point
* size on some fonts, commonly OTF. Also fixes a position bug
* with imagettftext using imagettfbbox. If you just want the text
* pass a null value to 'Destination Image Resource' instead.
*
* @param    resource    Destination Image Resource
* @param    int            Point Size (GD2), Pixel Size (GD1)
* @param    int            X Position (Destination)
* @param    int            Y Position (Destination)
* @param    int            Font Color - Red (0-255)
* @param    int            Font Color - Green (0-255)
* @param    int            Font Color - Blue (0-255)
* @param    string        TTF/OTF Path
* @param    string        Text to Print
* @param    float         Angle
* @return    null
*/

function drawttftext( &$des_img, $size, $posX=0, $posY=0, $colorR, $colorG, $colorB, $font='', $text='', $angle = 0)
{
    //-----------------------------------------
    // Establish a base size to create text
    //-----------------------------------------
   
    if( ! is_int( DRAW_TTF_BASE ) )
    {
        define( 'DRAW_TTF_BASE', 72);
    }
   
    if( $size >= DRAW_TTF_BASE )
    {
        define( 'DRAW_TTF_BASE', $size * 2 );
    }
   
    //-----------------------------------------
    // Simulate text and get data.
    // Get absolute X, Y, Width, and Height
    //-----------------------------------------
   
    $text_data = imagettfbbox( DRAW_TTF_BASE, $angle, $font, $text );
    $posX_font = min($text_data[0], $text_data[6]) * -1;
    $posY_font = min($text_data[5], $text_data[7]) * -1;
    $height = max($text_data[1], $text_data[3]) - min($text_data[5], $text_data[7]);
    $width = max($text_data[2], $text_data[4]) - min($text_data[0], $text_data[6]);
   
    //-----------------------------------------
    // Create blank translucent image
    //-----------------------------------------
   
    $im = imagecreatetruecolor( $width, $height );
    imagealphablending( $im, false );
    $trans = imagecolorallocatealpha( $im, 0, 0, 0, 127 );
    imagefilledrectangle( $im, 0, 0, $width, $height, $trans );
    imagealphablending( $im, true );
   
    //-----------------------------------------
    // Draw text onto the blank image
    //-----------------------------------------
   
    $m_color = imagecolorallocate( $im, $colorR, $colorG, $colorB );
    imagettftext( $im, DRAW_TTF_BASE, $angle, $posX_font, $posY_font, $m_color, $font, $text );
    imagealphablending( $im, false );
   
    //-----------------------------------------
    // Calculate ratio and size of sized text
    //-----------------------------------------
   
    $size_ratio = $size / DRAW_TTF_BASE;
    $new_width = round($width * $size_ratio);
    $new_height = round($height * $size_ratio);
   
    //-----------------------------------------
    // Resize text. Can't use resampled direct
    //-----------------------------------------

    $rimg = imagecreatetruecolor( $new_width, $new_height );
    $bkg = imagecolorallocate($rimg, 0, 0, 0);
    imagecolortransparent($rimg, $bkg);
    imagealphablending($rimg, false);   
    imagecopyresampled($rimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    if( $des_img != NULL )
    {
        //-----------------------------------------
        // Copy resized text to origoinal image
        //-----------------------------------------
       
        imagealphablending($des_img, true);
        imagecopy( $des_img, $rimg, $posX, $posY, 0, 0, $new_width, $new_height );
        imagealphablending($des_img, false);
        imagedestroy( $im );
        imagedestroy( $rimg );
    }
    else
    {
        //-----------------------------------------
        // Just return the resized image
        //-----------------------------------------
       
        $des_img = $rimg;
        imagedestroy( $im );
    }
}

/**
 * Truetype font string width
 *
 * @param string $font
 * font file
 * @param int $size
 * font size
 * @param string $text
 * @param float $angle
 * angle of font rotation
 */
function ttffontwidth ($font, $size, $text, $angle = 0) {
    $text_data = imagettfbbox ($size, $angle, $font, $text );
    $height = max($text_data[1], $text_data[3]) - min($text_data[5], $text_data[7]);
    $width = max($text_data[2], $text_data[4]) - min($text_data[0], $text_data[6]);
}

/**
 * Truetype font string height
 *
 * @param string $font
 * font file
 * @param int $size
 * font size
 * @param string $text
 * @param float $angle
 * angle of font rotation
 */
function ttffontheight ($font, $size, $text, $angle = 0) {
    $text_data = imagettfbbox ($size, $angle, $font, $text );
    $height = max($text_data[1], $text_data[3]) - min($text_data[5], $text_data[7]);
    $width = max($text_data[2], $text_data[4]) - min($text_data[0], $text_data[6]);
}



// debug function
function print_class ($o) {
    echo "<pre>";
    print_r ($o);
    echo "</pre>";
}

/**
 * @author Ulrich Mierendorff
 * email: ulrich.mierendorff@gmx.net
 *
 * @param unknown_type $img
 * @param unknown_type $cx
 * @param unknown_type $cy
 * @param unknown_type $w
 * @param unknown_type $h
 * @param unknown_type $start
 * @param unknown_type $stop
 * @param unknown_type $color
 * 
 * NOT USED ATM
 */
function imageSmoothArc( &$img, $cx, $cy, $w, $h, $start, $stop, $color) {
    // Written from scratch by Ulrich Mierendorff, 06/2006
    $fillColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 0 );
    $w /= 2;
    $h /= 2;
    $cdx = $w * cos(M_PI/4);
    $cdy = $h * sin(M_PI/4);
    
    $xStart = $w * cos($start);
    $yStart = $h * sin($start);
    $xStop = $w * cos(min(M_PI,$stop));
    $yStop = $h * sin(min(M_PI,$stop));
    if ( $start < M_PI/2 ) {
        $yy = 0;
        for ( $x = 0; $x <= $xStart; $x += 1 ) {
            if ( $x < $xStop ) {
                $y1 = $x/$xStop*$yStop;
            } else {
                $y1 = $h * sqrt( 1 - pow( $x,2 ) / pow( $w,2 ) );
            }
            $y2 = $x/$xStart*$yStart;
            $d1 = $y1 - floor($y1);
            $d2 = $y2 - floor($y2);
            $y1 = floor($y1);
            $y2 = floor($y2);
            imageLine($img, $cx + $x, $cy - $y1, $cx + $x, $cy - $y2, $fillColor);
            $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 100 - $d1*100 );
            imageSetPixel($img, $cx + $x, $cy - $y1 - 1, $diffColor);
            $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], $d2*100 );
            imageSetPixel($img, $cx + $x, $cy - $y2 + 1, $diffColor);
            for ( $yy; $yy <= $y1; $yy += 1 ) {
                if ( $yy < $yStart ) {
                    $x1 = $yy/$yStart*$xStart;
                } else {
                    $x1 = $w * sqrt( 1 - pow( $yy,2 ) / pow( $h,2 ) );
                }
                $d1 = $x1 - floor($x1);
                $x1 = floor($x1);
                $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 100 - $d1*100 );
                imageSetPixel($img, $cx + $x1 + 1, $cy - $yy, $diffColor);
                if ($stop < M_PI/2) {
                    $x2 = $yy/$yStop*$xStop;
                    $d2 = $x2 - floor($x2);
                    $x2 = floor($x2);
                    $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], $d2*100 );
                    imageSetPixel($img, $cx + $x2, $cy - $yy, $diffColor);
                }
            }
        }
    }
    if ( $start < M_PI && $stop > M_PI/2 ) {
        $yy = 0;
        for ( $x = 0; $x >= $xStop; $x -= 1 ) {
            if ( $x > $xStart ) {
                $y1 = $x/$xStart*$yStart;
            } else {
                $y1 = $h * sqrt( 1 - pow( $x,2 ) / pow( $w,2 ) );
            }
            $y2 = $x/$xStop*$yStop;
            $d1 = $y1 - floor($y1);
            $d2 = $y2 - floor($y2);
            $y1 = floor($y1);
            $y2 = floor($y2);
            imageLine($img, $cx + $x, $cy - $y1, $cx + $x, $cy - $y2, $fillColor);
            $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 100 - $d1*100 );
            imageSetPixel($img, $cx + $x, $cy - $y1 - 1, $diffColor);
            $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], $d2*100 );
            imageSetPixel($img, $cx + $x, $cy - $y2 + 1, $diffColor);
            for ( $yy; $yy <= $y1; $yy += 1 ) {
                if ( $yy < $yStop ) {
                    $x1 = -$yy/$yStop*$xStop;
                } else {
                    $x1 = $w * sqrt( 1 - pow( $yy,2 ) / pow( $h,2 ) );
                }
                $d1 = $x1 - floor($x1);
                $x1 = floor($x1);
                $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 100 - $d1*100 );
                imageSetPixel($img, $cx - $x1 - 1, $cy - $yy, $diffColor);
                if ( $start > M_PI/2 ) {
                    $x2 = $yy/$yStart*$xStart;
                    $d2 = $x2 - floor($x2);
                    $x2 = floor($x2);
                    $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], $d2*100 );
                    imageSetPixel($img, $cx + $x2, $cy - $yy, $diffColor);
                }
            }
        }
    }
    
    $xStart = $w * cos(max(M_PI,$start));
    $yStart = $h * sin(max(M_PI,$start));
    $xStop = $w * cos($stop);
    $yStop = $h * sin($stop);
    if ( $start < 3*M_PI/2 && $stop > M_PI) {
        $yy = 0;
        for ( $x = 0; $x >= $xStart; $x -= 1 ) {
            if ( $x > $xStop) {
                $y1 = $x/$xStop*$yStop;
            } else {
                $y1 = -$h * sqrt( 1 - pow( $x,2 ) / pow( $w,2 ) );
            }
            $y2 = $x/$xStart*$yStart;
            $d1 = $y1 - floor($y1);
            $d2 = $y2 - floor($y2);
            $y1 = floor($y1);
            $y2 = floor($y2);
            imageLine($img, $cx + $x, $cy - $y1, $cx + $x, $cy - $y2, $fillColor);
            $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], $d1*100 );
            imageSetPixel($img, $cx + $x, $cy - $y1 + 1, $diffColor);
            $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 100 - $d2*100 );
            imageSetPixel($img, $cx + $x, $cy - $y2 - 1, $diffColor);
            for ( $yy; $yy >= $y1; $yy -= 1 ) {
                if ( $yy > $yStart ) {
                    $x1 = -$yy/$yStart*$xStart;
                } else {
                    $x1 = $w * sqrt( 1 - pow( $yy,2 ) / pow( $h,2 ) );
                }
                $d1 = $x1 - floor($x1);
                $x1 = floor($x1);
                $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 100 - $d1*100 );
                imageSetPixel($img, $cx - $x1 - 1, $cy - $yy, $diffColor);
                if ($stop < 3*M_PI/2) {
                    $x2 = $yy/$yStop*$xStop;
                    $d2 = $x2 - floor($x2);
                    $x2 = floor($x2);
                    $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 100 - $d2*100 );
                    imageSetPixel($img, $cx + $x2 + 1, $cy - $yy, $diffColor);
                }
            }
        }
        
    }
    if ( $start < 2*M_PI && $stop > 3*M_PI/2 ) {
        $yy = 0;
        for ( $x = 0; $x <= $xStop; $x += 1 ) {
            if ( $x < $xStart )  {
                $y1 = $x/$xStart*$yStart;
            } else {
                $y1 = -$h * sqrt( 1 - pow( $x,2 ) / pow( $w,2 ) );
            }
            $y2 = $x/$xStop*$yStop;
            $d1 = $y1 - floor($y1);
            $d2 = $y2 - floor($y2);
            $y1 = floor($y1);
            $y2 = floor($y2);
            imageLine($img, $cx + $x, $cy - $y1, $cx + $x, $cy - $y2, $fillColor);
            $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], $d1*100 );
            imageSetPixel($img, $cx + $x, $cy - $y1 + 1, $diffColor);
            $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 100 - $d2*100 );
            imageSetPixel($img, $cx + $x, $cy - $y2 - 1, $diffColor);
            for ( $yy; $yy >= $y1; $yy -= 1 ) {
                if ( $yy > $yStop ) {
                    $x1 = $yy/$yStop*$xStop;
                } else {
                    $x1 = $w * sqrt( 1 - pow( $yy,2 ) / pow( $h,2 ) );
                }
                $d1 = $x1 - floor($x1);
                $x1 = floor($x1);
                $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], 100 - $d1*100 );
                imageSetPixel($img, $cx + $x1 + 1, $cy - $yy, $diffColor);
                if ( $start > 3*M_PI/2 ) {
                    $x2 = $yy/$yStart*$xStart;
                    $d2 = $x2 - floor($x2);
                    $x2 = floor($x2);
                    $diffColor = imageColorExactAlpha( $img, $color[0], $color[1], $color[2], $d2*100 );
                    imageSetPixel($img, $cx + $x2 , $cy - $yy, $diffColor);
                }
            }
        }
    }
}

/*
Third-party image rotation utility
Not used atm
*/
function imageRotateBicubic ($src_img, $angle, $bicubic=false) {
 
   // convert degrees to radians
   $angle = $angle + 180;
   $angle = deg2rad($angle);
 
   $src_x = imagesx($src_img);
   $src_y = imagesy($src_img);
 
   $center_x = floor($src_x/2);
   $center_y = floor($src_y/2);

   $cosangle = cos($angle);
   $sinangle = sin($angle);

   $corners=array(array(0,0), array($src_x,0), array($src_x,$src_y), array(0,$src_y));

   foreach($corners as $key=>$value) {
     $value[0]-=$center_x;        //Translate coords to center for rotation
     $value[1]-=$center_y;
     $temp=array();
     $temp[0]=$value[0]*$cosangle+$value[1]*$sinangle;
     $temp[1]=$value[1]*$cosangle-$value[0]*$sinangle;
     $corners[$key]=$temp;   
   }
  
   $min_x=1000000000000000;
   $max_x=-1000000000000000;
   $min_y=1000000000000000;
   $max_y=-1000000000000000;
  
   foreach($corners as $key => $value) {
     if($value[0]<$min_x)
       $min_x=$value[0];
     if($value[0]>$max_x)
       $max_x=$value[0];
  
     if($value[1]<$min_y)
       $min_y=$value[1];
     if($value[1]>$max_y)
       $max_y=$value[1];
   }

   $rotate_width=round($max_x-$min_x);
   $rotate_height=round($max_y-$min_y);

   $rotate=imagecreatetruecolor($rotate_width,$rotate_height);
   imagealphablending($rotate, false);
   imagesavealpha($rotate, true);

   //Reset center to center of our image
   $newcenter_x = ($rotate_width)/2;
   $newcenter_y = ($rotate_height)/2;

   for ($y = 0; $y < ($rotate_height); $y++) {
     for ($x = 0; $x < ($rotate_width); $x++) {
       // rotate...
       $old_x = round((($newcenter_x-$x) * $cosangle + ($newcenter_y-$y) * $sinangle))
         + $center_x;
       $old_y = round((($newcenter_y-$y) * $cosangle - ($newcenter_x-$x) * $sinangle))
         + $center_y;
     
       if ( $old_x >= 0 && $old_x < $src_x
             && $old_y >= 0 && $old_y < $src_y ) {

           $color = imagecolorat($src_img, $old_x, $old_y);
       } else {
         // this line sets the background colour
         $color = imagecolorallocatealpha($src_img, 255, 255, 255, 127);
       }
       imagesetpixel($rotate, $x, $y, $color);
     }
   }
  
  return($rotate);
}


    


?>
