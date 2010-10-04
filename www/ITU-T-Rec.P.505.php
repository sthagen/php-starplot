<?php
function StarPlot_dmz($key) {
    // we are open to public, so filter all vars through dmz()
    $rules = array();
    $rules['DEFAULT'] = array('TYPE'=>'TEXT','QUOTED'=>True,'LENGTH'=>4096);
    $rules['H1'] = array('TYPE'=>'TEXT','QUOTED'=>True,'LENGTH'=>50);
    $rules['DP'] = array('TYPE'=>'ARRAY','ENTRIES'=>12);
    $rules['AN'] = array('TYPE'=>'ARRAY','ENTRIES'=>12);
    $rules['ANLA'] = array('TYPE'=>'BOOLEAN','LENGTH'=>5); // AxisNameLabelAdjust
    $value = False;
    if (array_key_exists($key,$_GET)) {
        // development only! Production is POST.
        $value = htmlspecialchars($_GET[$key]);
        error_log('SP.dmz_GET_'.$key,0);
    }
    elseif(array_key_exists($key,$_POST)) {
        $value = htmlspecialchars($_POST[$key]);
        error_log('SP.dmz_POST_'.$key,0);
    }
    else {
        return False;
    }
    if (array_key_exists($key,$rules)) {
        $rulesOfKey = $rules[$key];
        if(array_key_exists('LENGTH',$rulesOfKey)){
            $vLength = strlen($value);
            if($vLength > $rulesOfKey['LENGTH']) {
                error_log('SP.dmz_RULES_'.$key.'_INVALID_LENGTH_'.$vLength,0);
                if ($vLength < 2*$rulesOfKey['LENGTH']) {
                    error_log('SP.dmz_RULES_'.$key.'_DETAILS_VALUE_'.$value,0);
                }
                return False;
            }
            $value = str_replace('&amp;auml;','&auml;',$value);
            $value = str_replace('&amp;Auml;','&Auml;',$value);
            $value = str_replace('&amp;ouml;','&ouml;',$value);
            $value = str_replace('&amp;Ouml;','&Ouml;',$value);
            $value = str_replace('&amp;uuml;','&uuml;',$value);
            $value = str_replace('&amp;Uuml;','&Uuml;',$value);
            $value = str_replace('&amp;szlig;','&szlig;',$value);
            $value = html_entity_decode($value);
            error_log('SP.dmz_RULES_'.$key.'_VALUE_'.$value,0);
        }
        if(array_key_exists('ENTRIES',$rulesOfKey)){
            // DEFAULT sanity checking
            $vLength = strlen($value);
            $rulesOfKeyDef = $rules['DEFAULT'];
            if($vLength > $rulesOfKeyDef['LENGTH']) {
                error_log('SP.dmz_RULES_'.$key.'_INVALID_LENGTH_'.$vLength,0);
                return False;
            }
            $vStructured = explode(',',$value);
            $vEntries = count($vStructured);
            if($vEntries > $rulesOfKey['ENTRIES']) {
                error_log('SP.dmz_RULES_'.$key.'_INVALID_ENTRIES_'.$vEntries,0);
                if ($vEntries < 2*$rulesOfKey['ENTRIES']) {
                    error_log('SP.dmz_RULES_'.$key.'_DETAILS_VALUE_'.$value,0);
                }
                return False;
            }
            foreach($vStructured as $i => $v) {
                $vTrimmed = trim($v);
                if(strlen($vTrimmed)) {
                    if (strtoupper($vTrimmed) == 'NA'
                        or strtoupper($vTrimmed) == 'NUL'
                        or strtoupper($vTrimmed) == 'NULL') {
                        $vTrimmed = 'NULL';
                    }
                    $vStructured[$i] = $vTrimmed;
                }
                else {
                    unset($vStructured[$i]);
                }
            }
            //foreach($vStructured as $i => $v) {
            //    if(!is_numeric($v)) {
            //        $vStructured[$i] = 'NULL';
            //        error_log('SP.dmz_RULES_'.$key.'_NULLED_ENTRY_'.$v,0);
            //    }
            //    else {
            //        if ($v < 0.00) {
            //            error_log('SP.dmz_RULES_'.$key.'_ZEROED_ENTRY_'.$v,0);
            //            $vStructured[$i] = 0.00;
            //        }
            //        elseif ($v > 1.00) {
            //            error_log('SP.dmz_RULES_'.$key.'_ONED_ENTRY_'.$v,0);
            //            $vStructured[$i] = 1.00;
            //        }
            //    }
            //}
            if($vEntries > 10) {
                // FIXME this is a HACK
                $vStructured = array_slice($vStructured,0,10);
                error_log('SP.dmz_RULES_'.$key.'_CUT_ENTRIES_'.strval($vEntries-10),0);
            }
            if($vEntries < 10) {
                // FIXME this is a HACK
                $vStructured += array_fill($vEntries,10-$vEntries,'NULL');
                error_log('SP.dmz_RULES_'.$key.'_PAD_ENTRIES_'.strval(10-$vEntries),0);
            }
            $value = $vStructured; // FIXME Hooray, Type-Change.
            error_log('SP.dmz_RULES_'.$key.'_VALUE_'.implode(',',$value),0);
        }

    }
    else {
        // DEFAULT sanity checking
        $rulesOfKey = $rules['DEFAULT'];
        if(strlen($value) > $rulesOfKey['LENGTH']) {
            return False;
        }
    }
    return $value;
}
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
function StarPlot_ExactTopOfCenter($angle) {
    // True, if $angle leads to point exactly top of center
    if ($angle == 270) {
        return True;
    }
    return False;
}
function StarPlot_ExactBottomOfCenter($angle) {
    // True, if $angle leads to point exactly bottom of center
    if ($angle == 90) {
        return True;
    }
    return False;
}
function StarPlot_RightBottomOfCenter($angle) {
    // True, if $angle leads to point right bottom of center
    if ($angle < 90) {
        return True;
    }
    return False;
}
function StarPlot_RightTopOfCenter($angle) {
    // True, if $angle leads to point right top of center
    if ($angle > 270) {
        return True;
    }
    return False;
}
function StarPlot_LeftBottomOfCenter($angle) {
    // True, if $angle leads to point left bottom of center
    if ($angle > 90 and $angle < 180) {
        return True;
    }
    return False;
}
function StarPlot_LeftTopOfCenter($angle) {
    // True, if $angle leads to point left top of center
    if ($angle >= 180 and $angle < 270) {
        return True;
    }
    return False;
}

function StarPlot_OctantOfAngle($angle) {
    // derive octant in (N,NW,W,SW,S,SE,E,NE) counterclockwise from angle
    if (StarPlot_ExactTopOfCenter($angle)) {
        return 'N';
    }
    elseif (StarPlot_ExactBottomOfCenter($angle)) {
        return 'S';        
    }
    elseif (StarPlot_RightBottomOfCenter($angle)) {
        if($angle == 0) {
            return 'E';            
        }
        else {
            return 'SE';
        }
    }
    elseif (StarPlot_LeftTopOfCenter($angle)) {
        if($angle == 180) {
            return 'W';            
        }
        else {
            return 'NW';
        }
    }
    elseif (StarPlot_LeftBottomOfCenter($angle)) {
        return 'SW';        
    }
    elseif (StarPlot_RightTopOfCenter($angle)) {
        return 'NE';
    }
    error_log('OctantOfAngle_X_Ang='.$angle,0);
    return 'X';
}
function StarPlot_axisNameCircleAdjust($angle, $fontSizePts, $textAngle, $fontName, $axisName, $axisNameSpaceSep = True) {
    // calculate axisName-Placements from bbox and angle
    $bbox = imagettfbbox($fontSizePts, $textAngle, $fontName, $axisName);
    // 6,7	upper left corners X,Y-Pos and 4,5	upper right corners X,Y-Pos
    // 0,1	lower left corners X,Y-Pos and 2,3	lower right corners X,Y-Pos

    $textW = abs($bbox[2] - $bbox[0]);
    $textH = abs($bbox[7] - $bbox[1]);

    $sepX = 0;
    $sepY = 0;
    if($axisNameSpaceSep) {
        $sepX = 5;
        $sepY = 5;
    }
    $dxPixel = 0;
    $dyPixel = 0;
    $octant = StarPlot_OctantOfAngle($angle);
    if ($octant == 'SE') {
        $dxPixel = 0 + $sepX;
        $dyPixel = +$textH + $sepY;
    }
    elseif ($octant == 'NE') {
        $dxPixel = 0 + $sepX;
        $dyPixel = 0 - $sepY;
    }
    elseif ($octant == 'SW') {
        $dxPixel = -$textW - $sepX;
        $dyPixel = +$textH + $sepY;
    }
    elseif ($octant == 'NW') {
        $dxPixel = -$textW - $sepX;
        $dyPixel = 0 - $sepY;
    }
    elseif ($octant == 'S') {
        $dxPixel = -$textW/2;
        $dyPixel = +$textH + 2*$sepY;
    }
    elseif ($octant == 'N') {
        // exactly on top of center
        $dxPixel = -$textW/2;
        $dyPixel = 0 - 2*$sepY;
    }
    elseif ($octant == 'W') {
        $dxPixel = -$textW - $sepX;
        $dyPixel = +$textH/2 + 0;
    }
    elseif ($octant == 'E') {
        $dxPixel = +$textW + $sepX;
        $dyPixel = +$textH/2 + 0;
    }
    // Note: Unmatched octant returns (0,0)
    return array($dxPixel,$dyPixel);
}
function StarPlot_canvasCircleAreaLowsTargetUpper(&$image, $cX, $cY, $rL, $rT, $rU, $cL, $cT, $cU) {
    // paint up to three concentric circles in image around (cX,cY)
    // radii rL, rT, rU with colors cL, cT, cU (for Lower, Target, Upper)
    imagefilledarc($image, $cX, $cY, $rU, $rU, 0, 360, $cU, IMG_ARC_PIE);
    imagefilledarc($image, $cX, $cY, $rT, $rT, 0, 360, $cT, IMG_ARC_PIE);
    imagefilledarc($image, $cX, $cY, $rL, $rL, 0, 360, $cL, IMG_ARC_PIE);
    return;
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
function StarPlot_minFromLimitMax($limit, $max) {
    // Testdata: (-6, -1, 2, 5, 10) or (0, 5, 8, 11, 16)
    // yield (8*(-1) - 5*2) / 3 = -6 or (8*5 - 5*8) / 3 = 0
    // a ---- b -- c and bc/ac = 3/8 => 8c - 8b = 3c - 3a => a = (8b - 5c)/3
    return (8.0*$limit - 5.0*$max) / 3.0; 
}
function StarPlot_limitFoldedFromLimitMax($limit, $max) {
    // Testdata: (-6, -1, 2, 5, 10) or (0, 5, 8, 11, 16)
    // yield 2 * 2 - (-1) = 5 or 2 * 8 - 5 = 11
    return 2.0 * $max - $limit; // explicit $max + ( $max - $limit )
}
function StarPlot_minFoldedFromLimitMax($limit, $max) {
    // Testdata: (-6, -1, 2, 5, 10) or (0, 5, 8, 11, 16)
    // yield (11*2 - 8*(-1)) / 3 = 10 or (11*8 - 8*5) / 3 = 16
    // a ---- b -- c ------- e and bc/ce = 3/8 => 8c - 8b = 3e - 3c => a = (11c - 8b)/3
    return (11.0*$max - 8.0*$limit) / 3.0; 
}
function StarPlot_XYPointFromRadiusAngle($radius, $angle, $cX = 0, $cY = 0) {
    // return point in (x,y) = (radius,angle/[deg]) possibly shifted (cY,cY)
    $x = $cX + cos(deg2rad($angle)) * $radius; 
    $y = $cY + sin(deg2rad($angle)) * $radius;
    return array($x, $y);
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
