<?php
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
function StarPlot_XYPointFromRadiusAngle($radius, $angle, $cX = 0, $cY = 0) {
    // return point in (x,y) = (radius,angle/[deg]) possibly shifted (cY,cY)
    $x = $cX + cos(deg2rad($angle)) * $radius; 
    $y = $cY + sin(deg2rad($angle)) * $radius;
    return array($x, $y);
}
function StarPlot_SegmentAngleMap($neededNumberOfAxis) {
    // return map of clockwise allocated angles for the needed axis segments
    // from imagefilledarc: 0 degrees is located at the three-oclock position,
    // ... and the arc is drawn clockwise.
    // but we want one $angleMid allways point to 12 oclock, the rest
    // ... determined by $neededNumberOfAxis
    // and remember, we want the axis to point at 12 oclock, not start-stop
    // so n = 1 gives (0,360,360) in theory 180 full circle to 180
    // n = 2 gives [(270,90,360), (90,270,180)]
    // n = 3 gives [(300,60,360), (60,180,120), (180,300,240)]
    $segmentAngleMap = array();
    $closureGuard = 0;
    $norm = 360;
    if($neededNumberOfAxis == 1) {
        $segmentAngleMap[0] = array($closureGuard,$norm,$norm);       
        return $segmentAngleMap;
        // early exit for cornercase
    }
    $anglePerSect = $norm/$neededNumberOfAxis;
    $signedCorrectAxisShift = -$anglePerSect/2;
    foreach(range(0,$neededNumberOfAxis-1) as $i) {
        $angleStart = fmod(($i*$anglePerSect + $signedCorrectAxisShift + $norm), $norm);
        $angleStop = fmod(($angleStart + $anglePerSect + $norm), $norm);
        $angleMid = ($angleStop + $angleStart ) / 2.0;
        if($angleStop < $angleStart and $angleStop == $closureGuard) {
            $angleStop = $norm;
        }
        if($angleStop < $angleStart) {
            $angleMid = ($angleStop + $norm + $angleStart ) / 2.0;
        }
        $segmentAngleMap[$i] = array($angleStart, $angleStop, $angleMid);
    }
    return $segmentAngleMap;
}
function StarPlot_TransformAngleMap_NCW_ICW($segmentAngleMapNCW) {
    // return map of adjusted angles for imagefilledarcClockWise from nativeCW
    // from imagefilledarc: 0 degrees is located at the three-oclock position,
    // ... and the arc is drawn clockwise.
    // but we want one $angleMid allways point to 12 oclock, the rest
    // ... determined by $neededNumberOfAxis
    // so transform maps 360 deg and 0 deg to 270 deg and 90 deg to 0 deg,
    // ... i.e. d=-90 modulo 360
    $segmentAngleMapICW = array();
    $closureGuard = 0;
    $norm = 360;
    $signedShiftDegrees = -90;
    $myEps = 0.0; //0.0000000000001; // one zero more and 12+03 oclock for n=1
    if(count(array_keys($segmentAngleMapNCW)) == 1) {
        $segmentAngleMapICW[0] = array(270+$myEps,270-$myEps,270);        
        return $segmentAngleMapICW;
        // early exit for cornercase
    }
    foreach($segmentAngleMapNCW as $i => $data) {
        list($angleStart, $angleStop, $angleMid) = $data;
        $angleStart = fmod(($angleStart + $signedShiftDegrees + $norm), $norm);
        $angleStop = fmod(($angleStop + $signedShiftDegrees + $norm), $norm);
        if($angleStop < $angleStart and $angleStop == $closureGuard) {
            $angleStop = $norm;
        }
        $angleMid = ($angleStop + $angleStart ) / 2.0;
        if($angleStop < $angleStart) {
            $angleMid = ($angleStop + $norm + $angleStart ) / 2.0;
        }
        $segmentAngleMapICW[$i] = array($angleStart, $angleStop, $angleMid);
    }
    return $segmentAngleMapICW;    
}
function StarPlot_TransformAngleMap_ICW_NCW($segmentAngleMapICW) {
    // return map of canonicalized angles for nativeClockWise from imagefilledarcCW
    // from imagefilledarc: 0 degrees is located at the three-oclock position,
    // ... and the arc is drawn clockwise.
    // but we want one $angleMid allways point to 12 oclock, the rest
    // ... determined by $neededNumberOfAxis
    // so transform maps 360 deg and 0 deg to 270 deg and 90 deg to 0 deg,
    // ... i.e. d=-90 modulo 360
    $segmentAngleMapNCW = array();
    $closureGuard = 0;
    $norm = 360;
    $signedShiftDegrees = +90;
    // $myEps = 0.0000001;
    if(count(array_keys($segmentAngleMapICW)) == 1) {
        $segmentAngleMapNCW[0] = array($closureGuard,$norm,$norm);         
        return $segmentAngleMapNCW;
        // early exit for cornercase
    }
    foreach($segmentAngleMapICW as $i => $data) {
        list($angleStart, $angleStop, $angleMid) = $data;
        $angleStart = fmod(($angleStart + $signedShiftDegrees + $norm), $norm);
        $angleStop = fmod(($angleStop + $signedShiftDegrees + $norm), $norm);
        if($angleStop < $angleStart and $angleStop == $closureGuard) {
            $angleStop = $norm;
        }
        $angleMid = ($angleStop + $angleStart ) / 2.0;
        if($angleStop < $angleStart) {
            $angleMid = ($angleStop + $norm + $angleStart ) / 2.0;
        }
        $segmentAngleMapNCW[$i] = array($angleStart, $angleStop, $angleMid);
    }
    return $segmentAngleMapNCW;    
}
function test_main_StarPlot_CircleGeometry() {
    session_start();
    $neededNumberOfAxisMax = 16;
    $title = 'Testing Module: '.$_SERVER['PHP_SELF'];
    $page = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
    $page .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">'."\n";
    $page .= '<head>'."\n";
    $page .= '<link rel="shortcut icon" href="/starplot-favicon.ico" />'."\n";
    $page .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
    $page .= '<title>'.$title.'</title>'."\n";
    $pageCSS = '<link rel="stylesheet" type="text/css" media="screen" href="/css/starplotde_v1_screen.css" />'."\n";
    $page .= $pageCSS;
    $page .= '</head>';
    $page .= '<body>';
    echo $page;
    $xFormatBitfield = imagetypes();
    $formatMap = array(IMG_GIF => 'GIF', IMG_JPG => 'JPG', IMG_PNG => 'PNG');
    echo 'DisplayformatTypes: ';
    foreach($formatMap as $aFormat => $aFormatName) {
        if($xFormatBitfield & $aFormat) {
            if ($aFormatName == 'GIF') {
                echo '<span style="color:#999999;">';
                echo ' '.$aFormatName;
                echo '</span>';
            }
            else {
                echo ' '.$aFormatName;
            }
        }
    }
    echo '<br />';
    $neededNumberOfAxis = $neededNumberOfAxisMax;
    if(isset($_GET['NAXIS'])) {
        $nAxisCand = intval(htmlentities($_GET['NAXIS']));
        if ( 0 < $nAxisCand and $nAxisCand <= $neededNumberOfAxis ) {
            $neededNumberOfAxis = $nAxisCand;
        }
    }
    $segmentAngleMap = StarPlot_SegmentAngleMap($neededNumberOfAxis);
    $segmentAngleMapICW = StarPlot_TransformAngleMap_NCW_ICW($segmentAngleMap);
    $segmentAngleMapNCW = StarPlot_TransformAngleMap_ICW_NCW($segmentAngleMapICW);
    $jobKey = md5(implode('_',array_keys($segmentAngleMapNCW)));
    $_SESSION[$jobKey] = array();
    $_SESSION[$jobKey]['SEG_ANG_MAP_ICW'] = $segmentAngleMapICW;
    $_SESSION[$jobKey]['SEG_ANG_MAP_NCW'] = $segmentAngleMapNCW;
    $_SESSION['JOB_KEY'] = $jobKey;
    echo 'Dimensionstest: '."\n";
    foreach(range(-1,$neededNumberOfAxisMax+1) as $i) {
        echo '<a href="/module/StarPlot_CircleGeometry.php?NAXIS='.$i.'" title="Test Request with '.$i.' dimensions.">'.$i.'</a> '; 
    }
    echo '<span style="float:right;">Format JPG: <a href="/module/StarPlot_CircleGeometry_Plain.php?JOB_KEY='.$jobKey.'&amp;FORMAT=JPG" target="PlainStarPlot" title="Plot in Format=JPG">JPEG.'.$jobKey.'</a></span>'."\n";
    echo '<br /><span style="float:right;">Format PNG: <a href="/module/StarPlot_CircleGeometry_Plain.php?JOB_KEY='.$jobKey.'&amp;FORMAT=PNG" target="PlainStarPlot" title="Plot in FOrmat=JPG">PNG.'.$jobKey.'</a></span>'."\n";
    echo '<br /><img src="/module/StarPlot_CircleGeometry_Plain.php?JOB_KEY='.$jobKey.'&amp;FORMAT=PNG&amp;NOW='.time().'" style="float:right;" alt="Format=PNG" />'."\n";
    echo '<pre>';
    echo 'Testing Module: '.$_SERVER['PHP_SELF']."\n";
    echo '  TestInput: NAXIS='.$neededNumberOfAxis."\n";
    echo '  TestOutput[0]:'."\n";
    echo '$segmentAngleMap='."\n";
    echo '</pre>';
    echo '<table style="width:30%;"><tr><th>Laufd.Nr.</th><th>Start/[deg]</th><th>Stop/[deg]</th><th>Achse/[deg]</th></tr>'."\n";
    foreach($segmentAngleMap as $i => $data) {
        list($angleStart, $angleStop, $angleMid) = $data;
        $displayRow = array($i,$angleStart, $angleStop, $angleMid);
        echo '<tr><td>';
        echo implode('</td><td>',$displayRow);
        echo '</td></tr>';
    }
    echo '</table>'."\n";
    echo '<pre>';
    echo '  TestOutput[1]:'."\n";
    echo '$segmentAngleMapICW='."\n";
    echo '</pre>';
    echo '<table style="width:30%;"><tr><th>Laufd.Nr.</th><th>Start/[deg]</th><th>Stop/[deg]</th><th>Achse/[deg]</th></tr>'."\n";
    foreach($segmentAngleMapICW as $i => $data) {
        list($angleStart, $angleStop, $angleMid) = $data;
        $displayRow = array($i,$angleStart, $angleStop, $angleMid);
        echo '<tr><td>';
        echo implode('</td><td>',$displayRow);
        echo '</td></tr>';
    }
    echo '</table>'."\n";
    echo '<pre>';
    echo '  TestOutput[2]:'."\n";
    echo '$segmentAngleMapNCW='."\n";
    echo '</pre>';
    echo '<table style="width:30%;"><tr><th>Laufd.Nr.</th><th>Start/[deg]</th><th>Stop/[deg]</th><th>Achse/[deg]</th></tr>'."\n";
    foreach($segmentAngleMapNCW as $i => $data) {
        list($angleStart, $angleStop, $angleMid) = $data;
        $displayRow = array($i,$angleStart, $angleStop, $angleMid);
        echo '<tr><td>';
        echo implode('</td><td>',$displayRow);
        echo '</td></tr>';
    }
    echo '</table>'."\n";
    echo '</body>'."\n";
    echo '</html>'."\n";
    return True;
}
if (basename($_SERVER['PHP_SELF']) == 'StarPlot_CircleGeometry.php') test_main_StarPlot_CircleGeometry();
?>