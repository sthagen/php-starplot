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
function StarPlot_valueFoldedFromLimitMax($value, $max) {
    // Testdata: (-6, -1, 2, 5, 10) or (0, 5, 8, 11, 16) // FIXME COPY
    // yield 2 * 2 - (-1) = 5 or 2 * 8 - 5 = 11 // FIXME COPY
    return 2.0 * $max - $value; // explicit $max - ( $value - $max )
}
function test_main_StarPlot_AxisMaps() {
    session_start();
    $neededNumberOfAxisMax = 16; 
    $nullStrRepr = 'NULL';
    $infoQueue = array();
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
                            'AXIS_INDEX' => '',
                            'AXIS_NAME' => 'Dimension',
                            'AXIS_TYPE' => 'LINEAR',
                            'AXIS_MIN' => 0.00,
                            'AXIS_LIMIT' => 0.80,
                            'AXIS_MAX' => 1.00,
                            'AXIS_LIMIT_FOLDED' => False,
                            'AXIS_MIN_FOLDED' => False,
                            'AXIS_VALUE' => $nullStrRepr,
                            'AXIS_UNIT' => '1'
                            );
    $axisDefaultMapFolded = array(
                            'AXIS_INDEX' => '',
                            'AXIS_NAME' => 'DimensionFolded',
                            'AXIS_TYPE' => 'FOLDED',
                            'AXIS_MIN' => 0.00,
                            'AXIS_LIMIT' => 0.80,
                            'AXIS_MAX' => 1.00,
                            'AXIS_LIMIT_FOLDED' => False,
                            'AXIS_MIN_FOLDED' => False,
                            'AXIS_VALUE' => $nullStrRepr,
                            'AXIS_UNIT' => 'dB'
                            );
    $axisDefaultKeys = array_keys($axisDefaultMap);
    $axisDefaultValues = array_values($axisDefaultMap);
    $axisValuesRows = array();
    $someAxisMaps = array();
    $axisValuesRowsReqString = '';
    $hasIndexCollision = False;
    $hasIndexOrderMismatch = False;
    if(!isset($_POST['AXIS_SPEC_ROWS'])) {
        $someAxisMaps[0] = $axisDefaultMap;
        $someAxisMaps[0]['AXIS_INDEX'] = 0;
        $someAxisMaps[1] = $axisDefaultMap;
        $someAxisMaps[1]['AXIS_INDEX'] = 1;
        $someAxisMaps[1]['AXIS_NAME'] = 'Dimension2';
        $someAxisMaps[2] = $axisDefaultMapFolded;
        $someAxisMaps[2]['AXIS_INDEX'] = 2;
        //DEBUG echo '<pre>DefaultUsed:'."\n".print_r($someAxisMaps,True).'</pre>';
        $infoQueue[] = 'Default used, since no input given.';
    }
    else {
        $axisValuesRowsReqString = htmlentities($_POST['AXIS_SPEC_ROWS']);
        $axisValuesRowsReq = explode("\n",$axisValuesRowsReqString);
        $nAxisRowsReq = count($axisValuesRowsReq);
        foreach(array_slice($axisValuesRowsReq,0,$neededNumberOfAxisMax) as $n => $rowString) {
            $axisValues = $axisDefaultValues;
            $axisValuesCand = explode(';',$rowString);
            if (count($axisValues) == count($axisValuesCand)) {
                foreach($axisValuesCand as $i => $v) {
                    if ($v != '') {
                        $axisValues[$i] = $v;
                    }
                }
            }
            $axisMap = array_combine($axisDefaultKeys,$axisValues);
            $numericAxisTypes = array('LINEAR','FOLDED');
            if($axisMap['AXIS_INDEX'] == '') {
                $axisMap['AXIS_INDEX'] = $n;
            }
            else {
                $indexCand = strval($axisMap['AXIS_INDEX']);
                $iCFC = strval(intval($indexCand));
                $axisMap['AXIS_INDEX'] = intval($iCFC);
                if ($indexCand !== $iCFC) {
                    $infoQueue[] = 'NOK \''.$indexCand.'\' index requested, accepted as \''.$iCFC.'\'';
                }
                else {
                    $infoQueue[] = ' OK \''.$indexCand.'\' index requested, accepted as \''.$iCFC.'\'';
                }
            }
            if (in_array($axisMap['AXIS_TYPE'], $numericAxisTypes)) {
                $axisMap['AXIS_MIN'] = StarPlot_minFromLimitMax($axisMap['AXIS_LIMIT'], $axisMap['AXIS_MAX']);
                if($axisMap['AXIS_VALUE'] != $nullStrRepr and !is_numeric($axisMap['AXIS_VALUE'])) {
                    $axisMap['AXIS_VALUE'] = $nullStrRepr;
                }
            }
            if ($axisMap['AXIS_TYPE'] == 'FOLDED') {
                $axisMap['AXIS_LIMIT_FOLDED'] = StarPlot_limitFoldedFromLimitMax($axisMap['AXIS_LIMIT'], $axisMap['AXIS_MAX']);
                $axisMap['AXIS_MIN_FOLDED'] = StarPlot_minFoldedFromLimitMax($axisMap['AXIS_LIMIT'], $axisMap['AXIS_MAX']);
            }
            $axisValues = array_values($axisMap);
            $someAxisMaps[] = $axisMap;
        }
        $nAxisRows = count($someAxisMaps);
        if($nAxisRowsReq > $nAxisRows) {
            $infoQueue[] = $nAxisRowsReq.' dimensions requested, but only '.$nAxisRows.' accepted. Maximum is '.$neededNumberOfAxisMax;
        }
        $bestEffortReOrderMap = array();
        $collectIndexCandList = array();
        foreach($someAxisMaps as $x => $data) {
            $indexCand = $data['AXIS_INDEX'];
            $iCFC = strval(intval($indexCand));
            $collectIndexCandList[] = $iCFC;
            if(!is_numeric($indexCand) or $iCFC != $indexCand or $indexCand < 0 or $indexCand >= $nAxisRows) {
                $hasIndexCollision = True;
                $conflictReason = 'NO_INTEGER';
                if ($iCFC != $indexCand) {
                    $conflictReason = 'DC_INTEGER';
                }
                if ($indexCand < 0) {
                    $conflictReason = 'LT_ZERO';
                }
                elseif ($indexCand >= $nAxisRows) {
                    $conflictReason = 'GT_NROW';
                }
                $infoQueue[] = 'Conflicting index rules. Failing IndexCand is '.$indexCand.', reason is '.$conflictReason;
            }
            if($indexCand != $x) {
                $hasIndexOrderMismatch = True;
                $infoQueue[] = 'Index positions not ordered. Misplaced IndexCand is '.$indexCand.', found at '.$x;
            }
            $bestEffortReOrderMap[$indexCand] = $data;
        }
        $collectIndexCandSet = array_unique($collectIndexCandList);
        if(count($collectIndexCandSet) != count($collectIndexCandList)) {
            $hasIndexCollision = True;
            $histo = array_count_values($collectIndexCandList);
            $blameList = array();
            foreach($histo as $xx => $nn) {
                if($nn != 1) {
                    $blameList[] = $xx;
                }
            }
            sort($blameList);
            $infoQueue[] = 'Conflicting index positions. Failing IndexCand/s is/are ['.implode(', ',$blameList).'], reason is '.'NONUNIQUE_INDEX';    
        }
        if(!$hasIndexCollision and $hasIndexOrderMismatch) {
            ksort($bestEffortReOrderMap);
            $someAxisMaps = array();
            foreach($bestEffortReOrderMap as $k => $data) {
                $someAxisMaps[] = $data;
            }
        }
    }
    $normalizedInputDataRows = array();
    foreach($someAxisMaps as $x => $data) {
        $axisValues = array_values($data);
        $normalizedInputDataRows[] = implode(";",$axisValues);
    }
    //DEBUG echo '<pre>ReAssembledRows:'."\n".print_r($normalizedInputDataRows,True).'</pre>';
    $normalizedInputDataString = implode("\n",$normalizedInputDataRows);
    //DEBUG echo '<pre>ReAssembledNormalizedInput:'."\n".print_r($normalizedInputDataString,True).'</pre>';
    echo 'AxisSpecTest: '."\n";
    echo '<form style="display:inline;" method="post" action="'.$_SERVER['PHP_SELF'].'">'."\n";
    echo '<textarea style="font-sice:small;" cols="80" rows="16" name="AXIS_SPEC_ROWS">'.$normalizedInputDataString.'</textarea>'."\n";
    echo '<input type="submit" name="Subme" value="parse" />'."\n";
    echo '</form>'.'<br />'."\n";
    echo '[<a href="'.$_SERVER['PHP_SELF'].'">RESET</a>] to some default to get started.<br />'."\n";
    //echo 'Implicit Keys: '.implode(';',$axisDefaultKeys).'<br />'."\n";
    echo '<pre>';
    echo 'Testing Module: '.$_SERVER['PHP_SELF']."\n";
    $infoQueue[] = 'TestInput: AXIS_SPEC_ROWS=<pre>'."\n".$axisValuesRowsReqString.'</pre>'."\n";
    echo '  TestOutput[0]:'."\n";
    echo '$someAxisMaps='."\n";
    //DEBUG echo print_r($someAxisMaps,True);
    echo '</pre>';
    echo '<table style="width:75%;"><tr><th>Laufd.Nr.</th><th>Name</th><th>Type</th><th>Min</th><th>Limit</th><th>Max</th><th>LimitFolded</th><th>MinFolded</th><th>Value</th><th>Unit</th></tr>'."\n";
    foreach($someAxisMaps as $i => $data) {
        $displayRow = array_values($data);
        echo '<tr><td>';
        echo implode('</td><td>',$displayRow);
        echo '</td></tr>';
    }
    echo '</table>'."\n";
    if($infoQueue) {
        echo '<h2>Info:</h2>';
        echo '<ul><li>';
        echo implode('</li><li>',$infoQueue);
        echo '</li></ul>';
    }
    echo '</body>'."\n";
    echo '</html>'."\n";
    return True;
}
if (basename($_SERVER['PHP_SELF']) == 'StarPlot_AxisMaps.php') test_main_StarPlot_AxisMaps();
?>