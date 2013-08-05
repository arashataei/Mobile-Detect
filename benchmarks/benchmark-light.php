<?php

require_once dirname(__FILE__) . '/lib.php';
require_once dirname(__FILE__) . '/../Mobile_Detect.php';

$jsonData = Bench_UserAgentData::loadJsonData();

//start yer engines
Bench_Stopwatch::timer('full')->reset();
Bench_Stopwatch::timer('full')->start();

foreach ($jsonData as $userAgent) {
    if (!isset($userAgent['user_agent'])) {
        continue;//skip incomplete user agent data
    }

    $md = new Mobile_Detect(array('HTTP_WAP_CONNECTION' => '1'));
    $md->setUserAgent($userAgent['user_agent']);
    $md->isMobile();
    $md->isTablet();

    Bench_Stopwatch::timer('full')->tick('single test run complete');
}

//wrap it up
Bench_Stopwatch::timer('full')->end();

$elapsed = Bench_Stopwatch::timer('full')->elapsed();
$count = Bench_Stopwatch::timer('full')->tickCount();
$avg = $count/$elapsed;

return array($elapsed, $count, $avg);