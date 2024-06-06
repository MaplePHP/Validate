#!/usr/bin/env php
<?php
/**
 * This is how a template test file should look like but
 * when used in MaplePHP framework you can skip the "bash code" at top and the "autoload file"!
 */
use MaplePHP\Unitary\Unit;


// If you add true to Unit it will run in quite mode
// and only report if it finds any errors!
$unit = new Unit();

// Add a title to your tests (not required)
$unit->addTitle("Testing MaplePHP validation library!");

$unit->add("Validating values", function($inst) {

    $inst->add("TestValue", [
        "isString" => []
    ], "Is not a string");

    $inst->add("600", [
        "isInt" => []
    ], "Is not int");

    $inst->add("600.33", [
        "isFloat" => []
    ], "Is not float");

    $inst->add(true, [
        "isBool" => []
    ], "Is not bool");

    $inst->add("yes", [
        "isBoolVal" => []
    ], "Is not bool");

});

$unit->execute();

