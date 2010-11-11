<?php
$title = 'Star Plot - Graphically representing small multi-variate data sets';
$h1 = 'Star Plot - <span style="font-size:medium;">Graphically representing small multi-variate data sets</span>';
$pIntro =
    'Multi-variate data may be visualized by star plots.
    The first star plots <strong>[<a href="#vonMayr_1877">vonMayr_1877</a>,
    <a href="#SiegelEtAl_1971">SiegelEtAl_1971</a>,
    <a href="#ChambersEtAl_1983">ChambersEtAl_1983</a>]</strong>
    looked like stars, hence the name. 
    The characteristics of these plots are: irregular polygons with vertices at
    equally spaced intervals whose distances from the center are proportional
    to the value of the corresponding variable.
    For further practical explanations and samples see e.g.
    <strong>[<a href="#NIST_2006">NIST_2006</a>]</strong>.';
$pMain =
    'This is still an option, but its effect on the observer is not invariant
    under ordering transformations, i.e. the extent to which a plotted star
    corner catches the eye, not only depends on its value, but also on the
    values of its direct neighbours, since the edges connecting the corners
    to build a starlike shape meet depending on the neighbours values.
    Consequently, many variants have been proposed and used.
    Some of these variants are implemented on this site. 
    Sometimes the areas of the dividing limiting circle dividing ok and nok
    and the outer rest are equipartitioned.
    Additionally more than two or three colors may be used.
    Star plots may communicate easily complex technical comparisons among
    alternatives to support e.g. dependable commercial management decisions,
    when some conventions are followed.
    As a sample application in the area of speech quality cf.
    <strong>[<a href="#ITUTRecP505_2005">ITUTRecP505_2005</a>]</strong>.';

$references = '<h3>References</h3>';
$references .= '<dl>';

$references .= '<dt><a name="ChambersEtAl_1983"><strong>[ChambersEtAl_1983]</strong></a></dt>';
$references .= '<dd><strong>Chambers, John &amp; Cleveland, William &amp; Kleiner, Beat and Tukey, Paul</strong>. (1983). Graphical Methods for Data Analysis, Wadsworth.</dd>';

$references .= '<dt><a name="ITUTRecP505_2005"><strong>[ITUTRecP505_2005]</strong></a></dt>';
$references .= '<dd><strong>Adler, Klemens P. F., Gierlich, Hans, Pomy, Joachim</strong>(Ed.). ITU-T Recommendation P.505 (2005). One-view visualization of speech quality measurement results. <em><a href="http://www.itu.int/itu-t/studygroups/com12/index.asp" target="_blank">ITU-T SG 12</a></em>.</dd>';

$references .= '<dt><a name="vonMayr_1877"><strong>[vonMayr_1877]</strong></a></dt>';
$references .= '<dd><strong>Mayr, Georg von</strong>. (1877). Die Gesetzm&auml;ssigkeit im Gesellschaftsleben. Oldenbourg. 78</dd>';

$references .= '<dt><a name="NIST_2006"><strong>[NIST_2006]</strong></a></dt>';
$references .= '<dd><strong>Croarkin, Carroll and Tobias, Paul Eds.</strong>. (2006). NIST/SEMATECH e-Handbook of Statistical Methods, URL=<a href="http://www.itl.nist.gov/div898/handbook/" target="_blank">http://www.itl.nist.gov/div898/handbook/</a>. <a href="http://www.itl.nist.gov/div898/handbook/eda/section3/starplot.htm" target="_blank">1.3.3.29. Star Plot</a>.</dd>';

$references .= '<dt><a name="SiegelEtAl_1971"><strong>[SiegelEtAl_1971]</strong></a></dt>';
$references .= '<dd><strong>Siegel, J. H. &amp; Goldwyn, R. M. &amp; Friedman, H. P.</strong>. (1971). Pattern and Process of the Evolution of Human Septic Shock. <em>Surgery</em>. 70. 232-245.</dd>';

$references .= '</dl>';
$h2 = 'Example inspired by ITU-T-Rec.P.505';
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
$page .= '<p>'.$pIntro.'</p>'."\n";
$page .= '<p>'.$pMain.'</p>'."\n";
$page .= '<h2>'.$h2.'</h2>'."\n";
$page .= '<a href="/module/StarPlot_TestDriver.php">';
$page .= '<img style="border:0;" src="/image/ITU-T-Rec.P.505.png" alt="A fictitious speech quality plot" /></a>';
$page .= $references;
$page .= '</body></html>';
echo $page;
?>