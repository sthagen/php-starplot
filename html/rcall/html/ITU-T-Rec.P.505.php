<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_AxisMaps.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_CircleGeometry.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_DMZ.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_ImageColors.php');

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

$axisNameLabelAdjustReq = StarPlot_dmz('ANLA');
if ($axisNameLabelAdjustReq) {
    $axisNameLabelAdjustReq = True;
}
else {
    $axisNameLabelAdjustReq = False;
}
// some values
$values = array( 5,      3.2,       2.5,    -1,     46,     '2b',   'not ok',      -10,       -10,             0);

$valuesReq = StarPlot_dmz('DP');
if($valuesReq) {
    $values = $valuesReq;
}
$axisValue = $values;
// with this prototyping setup 9:'D value' is at 12:00, 0:'SLR' at 01:30, ...
// from imagefilledarc: 0 degrees is located at the three-oclock position, and the arc is drawn clockwise.
$axisIndex = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
$axisNames = array('SLR','MOS SND','MOS RCV','RLR','TCLw','DT type','Live Call','BGNT (far)','BGNT (near)','D value');
$axisType = array('FOLDED','LINEAR','LINEAR','FOLDED','LINEAR','MAPPED','EXTREME','LINEAR','LINEAR','LINEAR');
//              'SLR',      'MOS SND','MOS RCV','RLR','TCLw','DT type','Live Call','BGNT (far)','BGNT (near)','D value'
$axisMin = array(   0,      2,          2,      -6,     20,     '3',   'not ok',   -20,       -20,           -15);
$axisLimit = array( 5,      3.2,       2.5,    -1,     46,     '2b',   False,      -10,       -10,             0);
$axisMax = array(   8,      5.5,       4,       2,      60,     '1',   'ok',       0,          0,              10);
$axisLimitFolded =
            array(  11,     False,      False,   5,     False,   False,  False,     False,      False,          False); // folded axis
$axisUnit = array(  'dB',   'MOS-LQO', 'MOS-LQO', 'dB', 'dB',   '1',    '1',        'dB',       'dB',           'dB');
//              'SLR','     MOS SND','MOS RCV','RLR','TCLw','DT type','Live Call','BGNT (far)','BGNT (near)','D value'

$sampleConfig = array();
$settingDefault = array(
                        'AXIS_INDEX' => False,
                        'AXIS_NAME' => 'Dimension',
                        'AXIS_TYPE' => 'LINEAR',
                        'AXIS_MIN' => 0.00,
                        'AXIS_LIMIT' => 0.80,
                        'AXIS_MAX' => 1.00,
                        'AXIS_LIMIT_FOLDED' => False,
                        'AXIS_MIN_FOLDED' => False,
                        'AXIS_VALUE' => 'NULL',
                        'AXIS_UNIT' => '1'
                        );
//$sampleConfig['P.505/SpeechQuality'] = array_fill_keys($axisNames,$settingDefault);
$sampleConfig['P.505/SpeechQuality'] = array();
$numericAxisTypes = array('LINEAR','FOLDED');
foreach($axisIndex as $i) {
    $sampleConfig['P.505/SpeechQuality'][$i] = $settingDefault;
    $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_INDEX'] = $i;
    $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_NAME'] = $axisNames[$i];
    $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_TYPE'] = $axisType[$i];
    $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_MAX'] = $axisMax[$i];
    $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_LIMIT'] = $axisLimit[$i];
    $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_MIN'] = $axisMin[$i];
    if (in_array($sampleConfig['P.505/SpeechQuality'][$i]['AXIS_TYPE'], $numericAxisTypes)) {
        $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_MIN'] = StarPlot_minFromLimitMax($axisLimit[$i], $axisMax[$i]);
    }
    // default $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_LIMIT_FOLDED'] = False;
    if ($sampleConfig['P.505/SpeechQuality'][$i]['AXIS_TYPE'] == 'FOLDED') {
        $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_LIMIT_FOLDED'] = StarPlot_limitFoldedFromLimitMax($axisLimit[$i], $axisMax[$i]);
    }
    // default $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_MIN_FOLDED'] = False;
    if ($sampleConfig['P.505/SpeechQuality'][$i]['AXIS_TYPE'] == 'FOLDED') {
        $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_MIN_FOLDED'] = StarPlot_minFoldedFromLimitMax($axisLimit[$i], $axisMax[$i]);
    }
    $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_VALUE'] = $axisValue[$i];
    $sampleConfig['P.505/SpeechQuality'][$i]['AXIS_UNIT'] = $axisUnit[$i];
}
error_log(print_r($sampleConfig,True),0);
$axisAngles = array();
imagefilledrectangle($image, 0, 0, $widthCanvas, $heightCanvas, $white);

// Add the bottom layer.
$equiPartFactor = 1.0/M_SQRT2;
$radiusOuter = $height;
$radiusInner = $radiusOuter*$equiPartFactor;
$areaInner = $radiusInner*$radiusInner*M_PI;
$areaOuter = $radiusOuter*$radiusOuter*M_PI - $areaInner;
error_log('EquiPart_Rx='.$radiusOuter.'_Ri='.$radiusInner.'_Ax='.$areaOuter.'_Ai='.$areaInner,0);
StarPlot_canvasCircleAreaLowsTargetUpper($image,$centerX,$centerY,$height*0.0,$height*$equiPartFactor,$height,$black,$red,$white);

/* Draw a dashed line, N darkgray pixels, M gray pixels */
//$styleDef = array($darkgray, $darkgray, $darkgray, $darkgray, $darkgray, $gray, $gray, $gray, $gray, $gray);
//imagesetstyle($image, $styleDef);
StarPlot_SetImgColorStyled($image, $darkgray, 5, $gray, 5);
$nSect = count($values);
$anglePerSect = 360/$nSect;
foreach($values as $i => $v) {
    $c = $yellow;
    if($v == 'NULL') {
        $c = $gray;
    }
    elseif ($v >= $equiPartFactor-0.10) {
        $c = $yellowgreen;
        if ($v >= $equiPartFactor) {
            $c = $lightgreen;
            if ($v >= $equiPartFactor+0.05) {
                $c = $green;
            }
        }
    }
    $w = $radiusInner;
    $h = $radiusInner;
    if($v != 'NULL') {
        $w = $width*$v;
        $h = $height*$v;
    }
    $angStart = $i*$anglePerSect;
    $angStop = $angStart + $anglePerSect;
    $angMid = ($angStop + $angStart ) / 2;
    $j = $i + 2;
    if ($j > $nSect -1) {
        $j -= $nSect;
    }
    $axisAngles[$j] = $angMid;
    imagefilledarc($image, $centerX, $centerY, $w, $h, $angStart, $angStop, $c, IMG_ARC_PIE);
    imagefilledarc($image, $centerX, $centerY, $w, $h, $angStart, $angStop, $black, IMG_ARC_NOFILL|IMG_ARC_EDGED);
    
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

/* Draw a dashed line, 5 black pixels, 5 white pixels */
//$styleDefAxis = array($black, $black, $black, $black, $black, $gray);
//imagesetstyle($image, $styleDefAxis);
StarPlot_SetImgColorStyled($image, $black, 5, $gray, 1);

// The text for H1 to draw
$textH1 = html_entity_decode('Starplots gem&auml;&szlig; ITU-T Rec. P.505(11/2005)');
$textH1Req = StarPlot_dmz('H1');
if($textH1Req) {
    $textH1 = $textH1Req;
}
$fontName = $_SERVER['DOCUMENT_ROOT'].'/arial.ttf'; // FIXME FontFile should NOT be in web space!
$fontSizePts = 18;
$textAngle = 0;
$textX = 50;
$textY = $fontSizePts*1.5;

// First we create the bounding box for the H1
$bbox = imagettfbbox($fontSizePts, $textAngle, $fontName, $textH1);
$textX = $bbox[0] + (imagesx($image) / 2) - ($bbox[4] / 2); // FIXME center Text
imagettftext($image, $fontSizePts, $textAngle, $textX, $textY,
             $black, $fontName, $textH1
             );

$fontSizePts = 10*2;
$fontSizePtsV = 8*2;
$axisNameSpaceSep = $axisNameLabelAdjustReq;

foreach($axisNames as $i => $axisName) {
    $radius = $width/2;
    $angle = $axisAngles[$i];
    list($pos_xf,$pos_yf) = StarPlot_XYPointFromRadiusAngle($radius,
                                                            $angle,
                                                            $centerX,
                                                            $centerY
                                                            );
    list($dx,$dy) = StarPlot_axisNameCircleAdjust($angle, $fontSizePts,
                                                  $textAngle, $fontName,
                                                  $axisName, $axisNameSpaceSep);
    $pos_xft = $pos_xf + $dx;
    $pos_yft = $pos_yf + $dy;
    $reportMe = 'ang='.$angle.'_X='.$pos_xf.'_Y='.$pos_yf.'_nam='.$axisName.'_i='.$i;
    error_log($reportMe,0);
    imagettftext($image, $fontSizePts, $textAngle, $pos_xft, $pos_yft,
                 $black, $fontName, $axisName
                 );
    imageline($image, $centerX, $centerY, $pos_xf, $pos_yf, IMG_COLOR_STYLED);
    // FIXME first hack to draw value lables
    $valueName = $axisMax[$i]; // FIXME this is from old parallel vectors version
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
    $valueName = $axisLimit[$i]; // FIXME this is from old parallel vectors version
    if($axisLimitFolded[$i]) {
        $valueName .= '/'.$axisLimitFolded[$i]; // FIXME ditto from old parallel
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
foreach($axisAngles as $i => $angle) {
    error_log('DEBUG_Octants_Ndx='.$i.'_Ang='.$angle.'_Oct='.StarPlot_OctantOfAngle($angle).'_CHK',0);
}
error_log('OuterRadius='.$radius.'_InnerRadius='.$radius*$equiPartFactor,0);

$imageOut = StarPlot_antiAlias($image);
header('Content-type: image/png');
imagepng($imageOut);
imagedestroy($imageOut);
?>
