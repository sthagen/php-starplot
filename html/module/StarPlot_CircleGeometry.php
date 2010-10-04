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
?>