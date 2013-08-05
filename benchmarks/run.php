#!/usr/bin/env php
<?php

//very light library for this benchmark
require_once dirname(__FILE__) . '/lib.php';

define('MAX_PASSES', 100);

if ($argc != 3) {
    print("Usage: {$argv[0]} <benchmark> <passes>\n\n");
    exit(1);
}

$pwd = dirname(__FILE__);

$benchmarkName = escapeshellarg($argv[1]);
$passes = (int) $argv[2];
$benchmarkFile = "$pwd/benchmark-{$argv[1]}.php";

if (!file_exists($benchmarkFile)) {
    print("No such benchmark {$argv[1]} exists (file: $benchmarkFile).\n\n");
    exit(2);
}

if ($passes < 1 || $passes > MAX_PASSES) {
    print("You cannot run more than " . MAX_PASSES . " passes.");
    exit(3);
}

$csv = new Bench_Data;
$csv->setHeader(array('Pass #', 'Total elapsed', 'Total tests run', 'Average tests/sec'));

for ($i = 1; $i <= $passes; $i++) {
    $data = include $benchmarkFile;
    $data = array_merge(array($i), $data);
    $csv->addRow($data);
}

echo $csv;