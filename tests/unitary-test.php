#!/usr/bin/env php
<?php
/**
 * This is how a template test file should look like but
 * when used in MaplePHP framework you can skip the "bash code" at top and the "autoload file"!
 */
use MaplePHP\Unitary\Unit;
use MaplePHP\Validate\Inp;




// If you add true to Unit it will run in quite mode
// and only report if it finds any errors!
$unit = new Unit();

// Add a title to your tests (not required)
$unit->addTitle("Testing MaplePHP validation library!");

$unit->add("Validating values", callback: function($inst) {

    $strVal = Inp::value("TestStringValue");
    $testStrValidates = ["isString", "required", "hasValue"];

    foreach ($testStrValidates as $validate) {
        $inst->add($strVal->{$validate}(), [
            "equal" => [true],
        ], "Expect {$validate} to be true");
    }
    
    $inst->add(Inp::value("8808218329")->socialNumber(), [
        "equal" => [false],
    ], "Expect socialNumber to be false");


    $inst->add(Inp::value("4030000010001234")->creditCard(), [
        "equal" => [true],
    ], "Expect creditCard to be true");

    $inst->add(Inp::value("john.doe-gmail.com")->email(), [
        "equal" => [false],
    ], "Expect creditCard to be false");

    $inst->add(Inp::value("Hello world!")->findInString("world"), [
        "equal" => [true],
    ], "Expect findInString to be true");

    $inst->add(Inp::value("+46 (0) 702-83 27 12")->phone(), [
        "equal" => [true],
    ], "Expect phone to be true");

    $inst->add(Inp::value("252522")->zip(5), [
        "equal" => [true],
    ], "Expect zip to be true");

    $testDataTypeValidations = ['isString', 'isInt', 'isFloat', 'isArray', 'isObject', 'isBool'];
    $inst->add(Inp::value("Is string")->isString(), [
        "equal" => [true],
    ], "Expect isString to be true");

    $inst->add(Inp::value(122)->isInt(), [
        "equal" => [true],
    ], "Expect isInt to be true");

    $inst->add(Inp::value(22.12)->isFloat(), [
        "equal" => [true],
    ], "Expect isFloat to be true");

    $inst->add(Inp::value([1, 2, 3])->isArray(), [
        "equal" => [true],
    ], "Expect isArray to be true");

    $inst->add(Inp::value(new stdClass())->isObject(), [
        "equal" => [true],
    ], "Expect isObject to be true");

    $inst->add(Inp::value(false)->isBool(), [
        "equal" => [true],
    ], "Expect isBool to be true");

    $inst->add(Inp::value("222.33")->number(), [
        "equal" => [true],
    ], "Expect number to be true");

    $inst->add(Inp::value(100)->positive(), [
        "equal" => [true],
    ], "Expect positive to be true");

    $inst->add(Inp::value(-100)->negative(), [
        "equal" => [true],
    ], "Expect negative to be true");

    $inst->add(Inp::value(10)->min(10), [
        "equal" => [true],
    ], "Expect min to be true");

    $inst->add(Inp::value(10)->max(10), [
        "equal" => [true],
    ], "Expect max to be true");

    $inst->add(Inp::value("Lorem ipsum")->length(1, 11), [
        "equal" => [true],
    ], "Expect length to be true");

    $inst->add(Inp::value("22222")->equalLength(5), [
        "equal" => [true],
    ], "Expect equalLength to be true");

    $inst->add(Inp::value("hello")->equal("hello"), [
        "equal" => [true],
    ], "Expect equal to be true");

    $inst->add(Inp::value("world")->notEqual("hello"), [
        "equal" => [true],
    ], "Expect notEqual to be true");

    $inst->add(Inp::value("1.2.3")->validVersion(true), [
        "equal" => [true],
    ], "Expect validVersion to be true");

    $inst->add(Inp::value("1.2.0")->versionCompare("1.2.0"), [
        "equal" => [true],
    ], "Expect versionCompare to be true");

    $inst->add(Inp::value("MyStrongPass")->lossyPassword(), [
        "equal" => [true],
    ], "Expect lossyPassword to be true");

    $inst->add(Inp::value("My@StrongPass12")->strictPassword(), [
        "equal" => [true],
    ], "Expect strictPassword to be true");

    $inst->add(Inp::value("HelloWorld")->atoZ(), [
        "equal" => [true],
    ], "Expect atoZ to be true");

    $inst->add(Inp::value("welloworld")->lowerAtoZ(), [
        "equal" => [true],
    ], "Expect lowerAtoZ to be true");

    $inst->add(Inp::value("HELLOWORLD")->upperAtoZ(), [
        "equal" => [true],
    ], "Expect upperAtoZ to be true");

    $inst->add(Inp::value("#F1F1F1")->hex(), [
        "equal" => [true],
    ], "Expect hex to be true");

    $inst->add(Inp::value("1922-03-01")->date(), [
        "equal" => [true],
    ], "Expect date to be true");

    $inst->add(Inp::value("1988-08-21")->age(36), [
        "equal" => [true],
    ], "Expect age to be true");

    $inst->add(Inp::value("example.se")->domain(), [
        "equal" => [true],
    ], "Expect domain to be true");

    $inst->add(Inp::value("https://example.se")->url(), [
        "equal" => [true],
    ], "Expect url to be true");

    $inst->add(Inp::value("examplethatwillfail.se")->dns(), [
        "equal" => [false],
    ], "Expect dns to be false");

    $inst->add(Inp::value("Lorem ipsum")->oneOf([
        "length" => [120, 200],
        "isString" => []
    ]), [
        "equal" => [true],
    ], "Expect oneOf to be true");

    $inst->add(Inp::value("Lorem ipsum")->allOf([
        "length" => [1, 200],
        "isString" => []
    ]), [
        "equal" => [true],
    ], "Expect allOf to be true");

    $inst->add(Inp::value("required")->required(), [
        "equal" => [true],
    ], "Expect required to be true");

    $inst->add(Inp::value("required")->required(), [
        "equal" => [true],
    ], "Expect required to be true");

    $inst->add(Inp::value("required")->required(), [
        "equal" => [true],
    ], "Expect required to be true");

    $inst->add(Inp::value("required")->required(), [
        "equal" => [true],
    ], "Expect required to be true");

});

$unit->execute();
