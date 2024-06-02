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

$unit->add("Checking data type", function($inst) {

    $inst->add("Lorem ipsum dolor", [
        "string" => [],
        "length" => [1,200]

    ])->add(1221, [
        "int" => []

    ])->add("Lorem", [
        "string" => [],
        "length" => function($valid) {
            return $valid->length(1, 50);
        }
    ], "The length is not correct!");

});

$unit->execute();

