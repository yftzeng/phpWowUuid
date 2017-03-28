<?php

include __DIR__.'/bootstrap.php';

use \Wow\Util\Uuid as Uuid;

echo Uuid::v1();
echo "\n";
echo Uuid::v4();
echo "\n";
echo Uuid::snowflake(1, 1);
echo "\n";
