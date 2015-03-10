<?php
/*
*
*  Copyright (C) Picktek. Ltd. http://www.picktek.com
*  Distributed under GNU GPL license v.2
*
*/
class RectangleLabel {
    
    var $width, $height;
    var $border, $background, $textColor;
    var $text;
    var $font;
    var $alpha = 60;
    
    function RectangleLabel ($width, $height, $text) {
        $this->font = 4;

        $this->width = $width;
        if ($height == -1)
            $this->height = imagefontheight($this->font) + 4;
        else 
            $this->height = $height;
        $this->text = $text;
        
        $this->background = new Background ();
        $this->border = new Border ();
        $this->textColor = new Color ("000000");
        
    }
    
    
    function render () {
        $img = imagecreatetruecolor ($this->width, $this->height);
        
        $this->background->render ($img, $this->width, $this->height);
        $this->border->render ($img, $this->width, $this->height);
        
        $clr = imagecolorallocate ($img, $this->textColor->get ("r"),  $this->textColor->get ("g"),  $this->textColor->get ("b"));
        
        imagestringcentered ($img, $this->font, ($this->height - imagefontheight($this->font))/2, $this->text, $clr);
        
        return $img;
    }
}

?>