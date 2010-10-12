<?php
function StarPlot_canvasCircleAreaLowsTargetUpper(&$image, $cX, $cY, $rL, $rT, $rU, $cL, $cT, $cU) {
    // paint up to three concentric circles in image around (cX,cY)
    // radii rL, rT, rU with colors cL, cT, cU (for Lower, Target, Upper)
    imagefilledarc($image, $cX, $cY, $rU, $rU, 0, 360, $cU, IMG_ARC_PIE);
    imagefilledarc($image, $cX, $cY, $rT, $rT, 0, 360, $cT, IMG_ARC_PIE);
    imagefilledarc($image, $cX, $cY, $rL, $rL, 0, 360, $cL, IMG_ARC_PIE);
    return;
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
function test_main_StarPlot_AxisMaps() {
    session_start();
    $neededNumberOfAxisMax = 1; // single axis test mode
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
    $axisDefaultMap = array(
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
    $axisDefaultKeys = array_keys($axisDefaultMap);
    $axisDefaultValues = array_values($axisDefaultMap);
    $axisValues = $axisDefaultValues;
    if(isset($_GET['AXIS_SPEC'])) {
        $axisValuesCand = explode(';',htmlentities($_GET['AXIS_SPEC']));
        if (count($axisValues) == count($axisValuesCand)) {
            foreach($axisValuesCand as $i => $v) {
                if ($v != '') {
                    $axisValues[$i] = $v;
                }
            }
        }
    }
    $axisMap = array_combine($axisDefaultKeys,$axisValues);
    $numericAxisTypes = array('LINEAR','FOLDED');
    $axisMap['AXIS_INDEX'] = 0;
    if (in_array($axisMap['AXIS_TYPE'], $numericAxisTypes)) {
        $axisMap['AXIS_MIN'] = StarPlot_minFromLimitMax($axisMap['AXIS_LIMIT'], $axisMap['AXIS_MAX']);
    }
    if ($axisMap['AXIS_TYPE'] == 'FOLDED') {
        $axisMap['AXIS_LIMIT_FOLDED'] = StarPlot_limitFoldedFromLimitMax($axisMap['AXIS_LIMIT'], $axisMap['AXIS_MAX']);
        $axisMap['AXIS_MIN_FOLDED'] = StarPlot_minFoldedFromLimitMax($axisMap['AXIS_LIMIT'], $axisMap['AXIS_MAX']);
    }
    $axisValues = array_values($axisMap);
    echo 'AxisSpecTest: '."\n";
    echo '<form style="display:inline;" method="get" action="'.$_SERVER['PHP_SELF'].'">'."\n";
    echo '<input type="text" style="font-sice:small;" size="80" name="AXIS_SPEC" value="'.implode(';',$axisValues).'" />'."\n";
    echo '<input type="submit" name="Subme" value="parse" />'."\n";
    echo '</form>'.'<br />'."\n";
    echo 'Default is: '.implode(';',$axisDefaultValues).' [<a href="'.$_SERVER['PHP_SELF'].'">RESET</a>]<br />'."\n";
    echo 'Implicit Keys: '.implode(';',$axisDefaultKeys).'<br />'."\n";
    echo '<pre>';
    echo 'Testing Module: '.$_SERVER['PHP_SELF']."\n";
    echo '  TestInput: AXIS_SPEC='.htmlentities($_GET['AXIS_SPEC'])."\n";
    echo '  TestOutput[0]:'."\n";
    echo '$segmentAngleMap='."\n";
    echo '</pre>';
    echo '</body>'."\n";
    echo '</html>'."\n";
    return True;
}
if (basename($_SERVER['PHP_SELF']) == 'StarPlot_AxisMaps.php') test_main_StarPlot_AxisMaps();
?>