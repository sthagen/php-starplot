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
?>