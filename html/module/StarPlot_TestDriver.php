<?php
function test_main_StarPlot_TestDriver() {
    session_start();
    require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_CircleGeometry.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/module/StarPlot_AxisMaps.php');
    $neededNumberOfAxisMax = 16; 
    $nullStrRepr = 'NULL';
    $randomeValue0 = floatval(rand(0,1000))/1000.0;
    if(rand(1,10) > 5) {
        $randomeValue0 = $nullStrRepr;
    }
    $randomeValue1 = floatval(rand(0,1000))/1000.0;
    if(rand(1,9) > 6) {
        $randomeValue1 = $nullStrRepr;
    }
    $infoQueue = array();
    $title = 'Using: '.$_SERVER['PHP_SELF'];
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
                            'AXIS_NAME' => 'Dim',
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
                            'AXIS_NAME' => 'DimFold',
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
        /*$someAxisMaps[0] = $axisDefaultMap;
        $someAxisMaps[0]['AXIS_INDEX'] = 0;
        $someAxisMaps[0]['AXIS_VALUE'] = $randomeValue0;
        $someAxisMaps[1] = $axisDefaultMap;
        $someAxisMaps[1]['AXIS_INDEX'] = 1;
        $someAxisMaps[1]['AXIS_NAME'] = 'Dim2';
        $someAxisMaps[1]['AXIS_VALUE'] = $randomeValue1;
        $someAxisMaps[2] = $axisDefaultMapFolded;
        $someAxisMaps[2]['AXIS_INDEX'] = 2;*/
        //DEBUG echo '<pre>DefaultUsed:'."\n".print_r($someAxisMaps,True).'</pre>';
        $_POST['AXIS_SPEC_ROWS'] = '0;D1F;FOLDED;;0.8;1;;;-0.1;dB
1;D2F;FOLDED;;0.8;1;;;NULL;1
2;D3F;FOLDED;;0.8;1;;;0.8;V
3;D4F;FOLDED;;0.8;1;;;1.1;dB
4;D5F;FOLDED;;0.8;1;;;1.2;dB
5;D6F;FOLDED;;0.8;1;;;1.5;dB
6;D7F;FOLDED;;0.8;1;;;1.6;dB
7;D8L;LINEAR;;0.8;1;;;0.75;#
8;D9F;FOLDED;;0.8;1;;;0.8;dB
9;D10L;LINEAR;;0.8;1;;;0.7;ms
10;D11L;LINEAR;;0.8;1;;;0.8;kbit/s
11;D12L;LINEAR;;0.8;1;;;NULL;s';
        $infoQueue[] = 'Default used, since no input given.';
    }
    //else {
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
    //}
    $neededNumberOfAxis = count($someAxisMaps);
    $segmentAngleMapNCW = StarPlot_SegmentAngleMap($neededNumberOfAxis);
    $segmentAngleMapICW = StarPlot_TransformAngleMap_NCW_ICW($segmentAngleMapNCW);
    $jobKey = md5(implode('_',array_keys($segmentAngleMapNCW)).'xxx');
    $_SESSION[$jobKey] = array();
    $_SESSION[$jobKey]['SEG_ANG_MAP_ICW'] = $segmentAngleMapICW;
    $_SESSION[$jobKey]['SEG_ANG_MAP_NCW'] = $segmentAngleMapNCW;
    $_SESSION['JOB_KEY'] = $jobKey;
    $_SESSION[$jobKey]['AXIS_MAPS'] = $someAxisMaps;
    
    $normalizedInputDataRows = array();
    foreach($someAxisMaps as $x => $data) {
        $axisValues = array_values($data);
        $normalizedInputDataRows[] = implode(";",$axisValues);
    }
    //DEBUG echo '<pre>ReAssembledRows:'."\n".print_r($normalizedInputDataRows,True).'</pre>';
    $normalizedInputDataString = implode("\n",$normalizedInputDataRows);
    //DEBUG echo '<pre>ReAssembledNormalizedInput:'."\n".print_r($normalizedInputDataString,True).'</pre>';
    echo '<a href=<div style="clear:none;"><a href="/module/StarPlot_CircleGeometry_Data.php?JOB_KEY='.$jobKey.'&amp;FORMAT=PNG" target="StarPlotPNG" ';
    echo 'title="Select image to access maximal resolution bitmap in format PNG.">';
    echo '<img src="/module/StarPlot_CircleGeometry_Data.php?JOB_KEY='.$jobKey.'&amp;FORMAT=PNG&amp;NOW='.time().'" ';
    echo 'style="float:right;width:25%;border:0px;" alt="This is a 25% Preview. Select image to access maximal resolution bitmap in format PNG." /></a>'."\n";
    echo '<br /><span style="float:right;">Format: <a href="/module/StarPlot_CircleGeometry_Data.php?JOB_KEY='.$jobKey.'&amp;FORMAT=JPG" ';
    echo 'target="DataStarPlot" title="Plot in Format=JPG">JPG</a>'."\n";
    echo ' | <a href="/module/StarPlot_CircleGeometry_Data.php?JOB_KEY='.$jobKey.'&amp;FORMAT=PNG" ';
    echo 'target="DataStarPlot" title="Plot in Format=PNG">PNG</a>'."\n";
    echo ', a 25%-Preview:';
    echo '</span>';
    echo '</div>';
    echo '<h2>AxisSpecTest:</h2>'."\n";
    echo '<div><form style="display:inline;" method="post" action="'.$_SERVER['PHP_SELF'].'">'."\n";
    echo '<textarea style="font-sice:small;" cols="70" rows="16" name="AXIS_SPEC_ROWS">'.$normalizedInputDataString.'</textarea>'."\n";
    echo '<br /><input type="submit" name="Subme" value="Parse and Plot" />'."\n";
    echo ' or [<a href="'.$_SERVER['PHP_SELF'].'">RESET</a>] to some (randomized) default to get started.'."\n";
    echo '</form>'.'</div>'."\n";
    //echo 'Implicit Keys: '.implode(';',$axisDefaultKeys).'<br />'."\n";
    echo '<pre>';
    echo 'Testing Module: '.$_SERVER['PHP_SELF']."\n";
    $infoQueue[] = 'TestInput: AXIS_SPEC_ROWS=<pre>'."\n".$axisValuesRowsReqString.'</pre>'."\n";
    echo '  TestOutput[0]: '.'$someAxisMaps=';
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
if (basename($_SERVER['PHP_SELF']) == 'StarPlot_TestDriver.php') test_main_StarPlot_TestDriver();
?>