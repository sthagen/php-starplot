<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_AxisMaps.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_CircleGeometry.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_DMZ.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_ImageColors.php');
function test_main_StarPlot_CircleGeometry_Data() {
    session_start();
    $nullStrRepr = 'NULL';
    $knownFormats = array('PNG','JPG');
    $jobKey = False; // 8cdbd7800d060827f77d4dd2c245c6fd
    if(isset($_GET['JOB_KEY'])) {
        $jobKeyCand = htmlentities($_GET['JOB_KEY']);
        if ( strlen($jobKeyCand) == 32 and isset($_SESSION[$jobKeyCand])) {
            $jobKey = $jobKeyCand;
        }
    }
    $bitmapFormat = 'PNG';
    if(isset($_GET['FORMAT'])) {
        $bitmapFormatCand = strtoupper(htmlentities($_GET['FORMAT']));
        if ( strlen($bitmapFormatCand) == 3 and in_array($bitmapFormatCand,$knownFormats)) {
            $bitmapFormat = $bitmapFormatCand;
        }
    }

    // main processing starts here (prototype-only)!
    $widthCanvas = 512*2+128*2;
    $heightCanvas = 512*2;
    $width = $heightCanvas*5/6; // width is greater than height to accomodate the text labels
    $height = $heightCanvas*5/6;
    $centerX = $widthCanvas/2;
    $centerY = $heightCanvas/2;
    // create image
    $image = imagecreatetruecolor($widthCanvas, $heightCanvas);
    
    //imageantialias($image, True);
    // allocate some solors
    $white    = StarPlot_ImageColorAllocateString($image, 'WHITE');
    $gray     = StarPlot_ImageColorAllocateString($image, '#C0C0C0');
    $darkgray = StarPlot_ImageColorAllocateString($image, '#505050');
    $navy     = StarPlot_ImageColorAllocateString($image, '#000080');
    $darknavy = StarPlot_ImageColorAllocateString($image, '#000050)');
    $red      = StarPlot_ImageColorAllocateString($image, '#DD0000');
    $darkred  = StarPlot_ImageColorAllocateString($image, '#900000)');
    $lightgreen    = StarPlot_ImageColorAllocateString($image,'GREEN');
    $green    = StarPlot_ImageColorAllocateString($image, '#008800');
    $blue = StarPlot_ImageColorAllocateString($image, 'BLUE');
    $black    = StarPlot_ImageColorAllocateString($image,'BLACK');
    $yellowgreen    = StarPlot_ImageColorAllocateString($image, '#77FF20');
    $yellow    = StarPlot_ImageColorAllocateString($image, '#FFFF20');

    imagefilledrectangle($image, 0, 0, $widthCanvas, $heightCanvas, $white);

    // Add the bottom layer.
    $equiPartFactor = 1.0/M_SQRT2;
    $radiusOuter = $height;
    $radiusInner = $radiusOuter*$equiPartFactor;
    $areaInner = $radiusInner*$radiusInner*M_PI;
    $areaOuter = $radiusOuter*$radiusOuter*M_PI - $areaInner;
    StarPlot_canvasCircleAreaLowsTargetUpper($image,$centerX,$centerY,$height*0.0,$height*$equiPartFactor,$height,$black,$red,$white);
    StarPlot_SetImgColorStyled($image, $darkgray, 5, $gray, 5);
    $segmentAngleMapICW = $_SESSION[$jobKey]['SEG_ANG_MAP_ICW'];
    $nSectors = count($segmentAngleMapICW);
    $axisMaps = $_SESSION[$jobKey]['AXIS_MAPS'];
    foreach($segmentAngleMapICW as $i => $data) {
        list($angleStart, $angleStop, $angleMid) = $data;
        $v = $axisMaps[$i]['AXIS_VALUE'];
        error_log('$i,$v='.$i.','.$v,0);
        $c = $yellow;
        if($v == $nullStrRepr) {
            $c = $gray;
        }
        else {
            $aType = $axisMaps[$i]['AXIS_TYPE'];
            if($aType == 'LINEAR') {
                $vMin = $axisMaps[$i]['AXIS_MIN'];
                $vMax = $axisMaps[$i]['AXIS_MAX'];
                $v = min($v,$vMax); // FIXME code needs audit
                $v = max($v,$vMin); // FIXME code needs audit
            }
            elseif($aType == 'FOLDED') {
                $vMin = $axisMaps[$i]['AXIS_MIN'];
                $vMinFolded = $axisMaps[$i]['AXIS_MIN_FOLDED'];
                $vMax = $axisMaps[$i]['AXIS_MAX'];
                //$v = min($v,$vMax); // FIXME code needs audit
                $v = min($v,$vMinFolded); // FIXME code needs audit
                $v = max($v,$vMin); // FIXME code needs audit
                if($v > $vMax) {
                    $v = StarPlot_valueFoldedFromLimitMax($v, $vMax);
                }
            }
            if ($v >= $equiPartFactor-0.10) {
                $c = $yellowgreen;
                if ($v >= $equiPartFactor) {
                    $c = $lightgreen;
                    if ($v >= $equiPartFactor+0.05) {
                        $c = $green;
                    }
                }
            }
        }
        $w = $radiusInner;
        $h = $radiusInner;
        if($v != 'NULL') {
            $w = $width*$v;
            $h = $height*$v;
        }
        imagefilledarc($image, $centerX, $centerY, $w, $h, $angleStart, $angleStop, $c, IMG_ARC_PIE);
        if($nSectors >1) {
            imagefilledarc($image, $centerX, $centerY, $w, $h, $angleStart, $angleStop, $black, IMG_ARC_NOFILL|IMG_ARC_EDGED);
        }
    }

    imagesetthickness($image, 1);
    //imagesetstyle($image, $styleDef);
    StarPlot_SetImgColorStyled($image, $darkgray, 5, $gray, 5);
    
    imagearc($image, $centerX, $centerY, $radiusOuter, $radiusOuter, 0, 360, IMG_COLOR_STYLED);
    
    /* Draw a dashed line, 5 black pixels, 5 white pixels */
    //$style = array($black, $black, $black, $black, $black, $black, $black, $black, $black, $black, $gray, $gray, $gray, $gray, $gray);
    //imagesetstyle($image, $style);
    StarPlot_SetImgColorStyled($image, $black, 10, $gray, 5);
    imagesetthickness($image, 1);
    
    imagearc($image, $centerX, $centerY, $radiusInner, $radiusInner, 0, 360, IMG_COLOR_STYLED);
    
    imagesetthickness($image, 1);

    $fontName = $_SERVER['DOCUMENT_ROOT'].'/arial.ttf'; // FIXME FontFile should NOT be in web space!
    $textAngle = 0;
    $fontSizePts = 10*2;
    $fontSizePtsV = 8*2;
    $axisNameSpaceSep = True; // FIXME this might be optional parameter
    
    foreach($segmentAngleMapICW as $i => $data) {
        $radius = $width/2;
        $angle = $data[2];
        list($pos_xf,$pos_yf) = StarPlot_XYPointFromRadiusAngle($radius,
                                                                $angle,
                                                                $centerX,
                                                                $centerY
                                                                );
        $axisName = $axisMaps[$i]['AXIS_NAME'];
        $axisUnit = rtrim($axisMaps[$i]['AXIS_UNIT']);
        $axisNameDisplay = $axisName.'['.$axisUnit.']';
        list($dx,$dy) = StarPlot_axisNameCircleAdjust($angle, $fontSizePts,
                                                      $textAngle, $fontName,
                                                      $axisNameDisplay, $axisNameSpaceSep);
        $pos_xft = $pos_xf + $dx;
        $pos_yft = $pos_yf + $dy;
        $reportMe = 'ang='.$angle.'_X='.$pos_xf.'_Y='.$pos_yf.'_nam='.$axisNameDisplay.'_i='.$i;
        error_log($reportMe,0);
        imagettftext($image, $fontSizePts, $textAngle, $pos_xft, $pos_yft,
                     $black, $fontName, $axisNameDisplay
                     );
        imageline($image, $centerX, $centerY, $pos_xf, $pos_yf, IMG_COLOR_STYLED);

        // FIXME first hack to draw value lables
        $valueName = $axisMaps[$i]['AXIS_MAX']; // FIXME code needs audit
        list($pos_xf,$pos_yf) = StarPlot_XYPointFromRadiusAngle($radius-$fontSizePtsV*2,
                                                                $angle,
                                                                $centerX,
                                                                $centerY
                                                                );
        list($dx,$dy) = StarPlot_axisNameCircleAdjust($angle, $fontSizePtsV,
                                                      $textAngle, $fontName,
                                                      $valueName, $axisNameSpaceSep);
        $pos_xft = $pos_xf + $dx;
        $pos_yft = $pos_yf + $dy;    
        $reportMe = 'ang='.$angle.'_X='.$pos_xf.'_Y='.$pos_yf.'_nam='.$valueName.'_i='.$i;
        error_log($reportMe,0);
        imagettftext($image, $fontSizePtsV, $textAngle, $pos_xft, $pos_yft,
                     $black, $fontName, $valueName
                     );
        // ditto
        $valueName = $axisMaps[$i]['AXIS_LIMIT']; // FIXME this is from old parallel vectors version
        $aType = $axisMaps[$i]['AXIS_TYPE'];
        if($aType == 'FOLDED') {
            $valueName .= '/'.$axisMaps[$i]['AXIS_LIMIT_FOLDED']; // FIXME code needs audit
        }
        list($pos_xf,$pos_yf) = StarPlot_XYPointFromRadiusAngle($radius*$equiPartFactor-$fontSizePtsV*2,
                                                                $angle,
                                                                $centerX,
                                                                $centerY );
        list($dx,$dy) = StarPlot_axisNameCircleAdjust($angle, $fontSizePtsV,
                                                      $textAngle, $fontName,
                                                      $valueName, $axisNameSpaceSep);
        $pos_xft = $pos_xf + $dx;
        $pos_yft = $pos_yf + $dy;    
         $reportMe = 'ang='.$angle.'_X='.$pos_xf.'_Y='.$pos_yf.'_nam='.$valueName.'_i='.$i;
        error_log($reportMe,0);
        imagettftext($image, $fontSizePtsV, $textAngle, $pos_xft, $pos_yft,
                     $black, $fontName, $valueName
                     );    
    }
    
    $imageOut = StarPlot_antiAlias($image);
    if ($bitmapFormat == 'JPG') {
        header('Content-type: image/jpeg');
        imagejpeg($imageOut, NULL, 95);
    }
    else {
        header('Content-type: image/png');
        imagepng($imageOut);
    }
    imagedestroy($imageOut);
    return True;
}
if (basename($_SERVER['PHP_SELF']) == 'StarPlot_CircleGeometry_Data.php') test_main_StarPlot_CircleGeometry_Data();
?>