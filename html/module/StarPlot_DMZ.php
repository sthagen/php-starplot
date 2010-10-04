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
?>