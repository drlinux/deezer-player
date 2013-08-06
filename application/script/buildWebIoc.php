<?php
require_once '../conf/environement.php';

$yuiJarFilePath = realpath(LIBRARY_PATH . '/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar');
$builder = new \Ioc\Builder(\Ioc\Manager::getClientContainerInstance(), $yuiJarFilePath, TMP_PATH);

$outputFilePath = WEB_PATH . '/build/model.js';
$builder->generateClientModelCombinedJsFile($outputFilePath);

echo file_get_contents($outputFilePath) . PHP_EOL;







function human_filesize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

echo str_pad('', 80, '-') . PHP_EOL;
echo 'Filesize : ' . human_filesize(filesize($outputFilePath)) . PHP_EOL;
echo str_pad('', 80, '-') . PHP_EOL;
