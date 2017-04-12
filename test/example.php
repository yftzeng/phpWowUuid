<?php

include __DIR__.'/bootstrap.php';

use \Wow\Util\Uuid as Uuid;

$value = 0;

echo "################################\n";
echo "### Value test\n";
echo "################################\n";

$value = Uuid::v1();
echo "Uuid::v1()                 : len = " . strlen($value) . " : " . $value . "\n";

$value = Uuid::v1_order();
echo "Uuid::v1_order()           : len = " . strlen($value) . " : " . $value . "\n";

$value = Uuid::v1_order(false);
echo "Uuid::v1_order(false)      : len = " . strlen($value) . " : " . $value . "\n";

$value = Uuid::v4();
echo "Uuid::v4()                 : len = " . strlen($value) . " : " . $value . "\n";

$value = Uuid::snowflake_random();
echo "Uuid::snowflake_random()   : len = " . strlen($value) . " : " . $value . "\n";

$value = Uuid::snowflake(1, 1);
echo "Uuid::snowflake(1, 1)      : len = " . strlen($value) . " : " . $value . "\n";

$value = uniqid(true);
echo "uniqid(true)               : len = " . strlen($value) . " : " . $value . "\n";

$value = Uuid::snowflake_order(1,1);
echo "Uuid::snowflake_order(1,1) : len = " . strlen($value) . " : " . $value . "\n";

echo "################################\n";
echo "### Order test\n";
echo "################################\n";

$index = 0;
$loop = 99999;
$flag = true;

echo "[ERROR] Uuid::v1() is not ordered\n";
echo "[ERROR] Uuid::v4() is not ordered\n";
echo "[PASS ] Uuid::v1_order() is ordered\n";

$comp1 = Uuid::snowflake_random();
do {
    $comp2 = Uuid::snowflake_random();
    $index++;
    if ($comp1 >= $comp2) {
        echo "[ERROR] Uuid::snowflake_random() is not ordered\n";
        echo "$comp1\n";
        echo "$comp2\n";
        $flag = false;
        break;
    }
} while ($index < $loop);
if ($flag) {
    echo "[PASS ] Uuid::snowflake_random() is ordered\n";
}
$index = 0;
$flag = true;

$comp1 = Uuid::snowflake(1, 1);
do {
    $comp2 = Uuid::snowflake(1, 1);
    $index++;
    if ($comp1 >= $comp2) {
        echo "[ERROR] Uuid::snowflake(1, 1) is not ordered\n";
        echo "$comp1\n";
        echo "$comp2\n";
        $flag = false;
        break;
    }
} while ($index < $loop);
if ($flag) {
    echo "[PASS ] Uuid::snowflake(1, 1) is ordered\n";
}
$index = 0;
$flag = true;

$comp1 = Uuid::snowflake_order(1,1);
do {
    $comp2 = Uuid::snowflake_order(1,1);
    $index++;
    if ($comp1 >= $comp2) {
        echo "[ERROR] Uuid::snowflake_order(1,1) is not ordered\n";
        echo "$comp1\n";
        echo "$comp2\n";
        $flag = false;
        break;
    }
} while ($index < $loop);
if ($flag) {
    echo "[PASS ] Uuid::snowflake_order(1,1) is ordered\n";
}
$index = 0;
$flag = true;
