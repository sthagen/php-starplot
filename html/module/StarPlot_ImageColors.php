<?php
function StarPlot_ImageColorAllocateString(&$image, $colorString=False) {
    // heuristics to allocate colors in image
    $cStrMap = array(
                    'WHITE' => array(0xFF,0xFF,0xFF),
                    'RED' => array(0xFF,0x00,0x00),
                    'GREEN' => array(0x00,0xFF,0x00),
                    'BLUE' => array(0x00,0x00,0xFF),
                    'BLACK' => array(0x00,0x00,0x00),
                    );
    // return white if unsure
    $r = $cStrMap['WHITE'][0];
    $g = $cStrMap['WHITE'][1];
    $b = $cStrMap['WHITE'][2];
    if(!$colorString or strlen($colorString) > 50) {
        return imagecolorallocate($image, $r, $g, $b);
    }
    $cStr = strtoupper($colorString);
    if (!array_key_exists($cStr,$cStrMap)
        and !(strlen($cStr) == 7 and substr($cStr,0,1) == '#')
        ) {
        return imagecolorallocate($image, $r, $g, $b);
    }
    if (array_key_exists($cStr,$cStrMap)) {
        error_log('ICA_CStr='.$colorString.'_Triple='.implode('_',$cStrMap[$cStr]),0);
        return imagecolorallocate($image, $cStrMap[$cStr][0], $cStrMap[$cStr][1], $cStrMap[$cStr][2]);
    }
    if (strlen($cStr) == 7 and substr($cStr,0,1) == '#') {
        // hex color triple, split and deliver ...
        $r = hexdec('0x'.substr($cStr,1,2));
        $g = hexdec('0x'.substr($cStr,3,2));
        $b = hexdec('0x'.substr($cStr,5,2));
        error_log('ICA_CStr='.$colorString.'_Triple='.$r.'_'.$g.'_'.$b,0);
    }
    return imagecolorallocate($image, $r, $g, $b);
}
function StarPlot_antiAlias(&$image) {
    // poor mans antialias resample interpolated FIXME
    // ATTENTION side effect, destroy old image (referenced)!
    $percent = 0.5;
    $widthCanvas = imagesx($image);
    $heightCanvas = imagesy($image);
    $new_width = $widthCanvas * $percent;
    $new_height = $heightCanvas * $percent;

    // Resample
    $imageOut = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($imageOut, $image, 0, 0, 0, 0, $new_width, $new_height, $widthCanvas, $heightCanvas);

    imagedestroy($image);
    return $imageOut;
}
function StarPlot_SetImgColorStyled(&$image, $fColor=False, $fN=False, $bColor=False, $bN=False) {
    // dash pattern fN x fColor, then bN x bColor
    // argh this crufty code sets dash pattern for some php/gd-primitives ...
    // when paintong with special color IMG_COLOR_STYLED
    // one can use IMG_COLOR_TRANSPARENT constant to add a transparent pixel
    $color1 = imagecolorallocate($image, 0x00, 0x00, 0x00);
    $n1 = 1;
    $color2 = imagecolorallocate($image, 0x00, 0x00, 0x00);
    $n2 = 1;
    if ($fColor) {
        $color1 = $fColor;
        if (is_int($fN) and $fN < 100) {
            $n1 = $fN;
        }
        if ($bColor) {
            $color2 = $bColor;
            if (is_int($bN) and $bN < 100) {
                $n2 = $bN;
            }
        }
    }
    $styleDef = array();
    $styleDef = array_pad($styleDef,$n1,$color1);
    $styleDef = array_pad($styleDef,$n1+$n2,$color2);
    imagesetstyle($image, $styleDef);    
    return True;
}
?>