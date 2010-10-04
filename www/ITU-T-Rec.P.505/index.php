<?php
    $title = 'Star Plot - Graphically representing small multi-variate data sets';
    $h1 = 'Star Plot - <span style="font-size:medium;">Graphically representing small multi-variate data sets</span>';
    $h2 = 'Example configuration inspired by ITU-T-Rec.P.505 <span style="font-size:medium;color:gray;">(1<sup>st</sup> prototype only)</span>';
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
    $page .= '<h1>'.$h1.'</h1>'."\n";
    $page .= '<h3>'.$h2.'</h3>'."\n";
    $data = implode(',',array(1.0,0.2,0.7,0.5,0.85,0.82,0.98,'NULL',0.97,0.1));
    $data = implode(',',array( 5,      3.2,       2.5,    -1,     46,     '2b',   'not ok',      -10,       -10,             0));
    $h1 = 'Note: Only surrogate data and axes for now'; //urlencode('My Testdata');
    $targetURL = '/ITU-T-Rec.P.505.php';
    $pageForm = '<div style="font-size:x-small;margin-top:0px;margin-right:2px;float:left;clear:both;">';
    $pageForm .= '<fieldset style="width:600px;">';
    $pageForm .= '<legend><span style="margin-bottom:0px;font-size:large;color:red;font-family:sans-serif;">Configure Plot</span></legend>';
    $pageForm .= '<form name="TestData" action="'.$targetURL.'" target="_plot" method="post">';
    $textAreaCols = 46;
    $h1Width = 40;
    $pageForm .= '<span style="font-size:medium;vertical-align:top;">Title: </span>';
    $pageForm .= '<input type="text" name="H1" size="'.$h1Width.'" value="'.$h1.'" /> (<span style="color:black;">placed on top of circle/above</span>)';
    $pageForm .= '<br /><span style="font-size:medium;vertical-align:top;">Data: </span>';
    if (strlen($data)) {
        $pageForm .= '<textarea  style="font-size:xx-small;" cols="'.$textAreaCols.'" rows="4" name="DP">'.$data.'</textarea>';
    }
    else {
        $pageForm .= '<textarea  style="font-size:xx-small;" cols="'.$textAreaCols.'" rows="4" name="DP">'.implode(',',$data).'</textarea>';        
    }
    $pageForm .= '<span style="color:black;vertical-align:top;"> (Now: only 10 entries accepted.) </span>';
    $pageForm .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $pageForm .= '(<input type="checkbox" name="ANLA" checked="checked" /> <span style="color:red;">separate axis name labels from plot</span>)';
    $pageForm .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $pageForm .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $pageForm .= '&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="Testlauf" value="Generate StarPlot" />&nbsp;&nbsp;&nbsp;&nbsp;';
  
    $pageForm .= '</form></fieldset></div>';
    $page .= $pageForm;
    $page .= '<div style="clear:both;float:left;margin-left:2px;"><em>Note: The generated plot will open in a separate browser window.</em></div>';
    $startURL = 'Intro'; //$_SERVER["SERVER_NAME"];
    $page .= '<div style="clear:both;float:right;margin-right:10%;">Back to <a href="/">'.$startURL.'</a></div>';
    $page .= '</body></html>';
    echo $page;
?>