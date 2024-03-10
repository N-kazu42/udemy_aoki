<?php
$contactFile = '.contact.dat';

// ファイルまるごと読み込み
$fileContents = file_get_contents($contactFile);

echo $fileContents;

