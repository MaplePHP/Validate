#!/usr/bin/env php
<?php
/**
 * This is how a template test file should look like but
 * when used in MaplePHP framework you can skip the "bash code" at top and the "autoload file"!
 */

use MaplePHP\Unitary\TestCase;
use MaplePHP\Unitary\Unit;
use MaplePHP\Validate\ValidationChain;
use MaplePHP\Validate\Validator;

$unit = new Unit();
$unit->group("MaplePHP input validate test", function(TestCase $case) {

    $strVal = Validator::value("TestStringValue");
    $testStrValidates = ["isString", "required", "hasValue"];

    foreach ($testStrValidates as $validate) {
        $case->add($strVal->{$validate}(), [
            "equal" => [true],
        ], "Expect {$validate} to be true");
    }

    $case->add(Validator::value("8808218329")->socialNumber(), [
        "equal" => [false],
    ], "Expect socialNumber to be false");


    $case->add(Validator::value("#CCC")->isHexColor(), [
        "equal" => [true],
    ], "Expect isHexColor to be true");

    $case->add(Validator::value("#F1F1F1")->isHexColor(), [
        "equal" => [true],
    ], "Expect isHexColor to be true");


    $case->add(Validator::value("4030000010001234")->creditCard(), [
        "equal" => [true],
    ], "Expect creditCard to be true");

    $case->add(Validator::value("john.doe-gmail.com")->email(), [
        "equal" => [false],
    ], "Expect creditCard to be false");

    $case->add(Validator::value("Hello world!")->findInString("world"), [
        "equal" => [true],
    ], "Expect findInString to be true");

    $case->add(Validator::value("+46 (0) 702-83 27 12")->phone(), [
        "equal" => [true],
    ], "Expect phone to be true");

    $case->add(Validator::value("252522")->zip(5), [
        "equal" => [true],
    ], "Expect zip to be true");

    $testDataTypeValidations = ['isString', 'isInt', 'isFloat', 'isArray', 'isObject', 'isBool'];
    $case->add(Validator::value("Is string")->isString(), [
        "equal" => [true],
    ], "Expect isString to be true");

    $case->add(Validator::value(true)->isInt(), [
        "equal" => [true],
    ], "Expect isInt to be true");

    $case->add(Validator::value(22.12)->isFloat(), [
        "equal" => [true],
    ], "Expect isFloat to be true");

    $case->add(Validator::value([1, 2, 3])->isArray(), [
        "equal" => [true],
    ], "Expect isArray to be true");

    $case->add(Validator::value(new stdClass())->isObject(), [
        "equal" => [true],
    ], "Expect isObject to be true");

    $case->add(Validator::value(false)->isBool(), [
        "equal" => [true],
    ], "Expect isBool to be true");

    $case->add(Validator::value("222.33")->number(), [
        "equal" => [true],
    ], "Expect number to be true");

    $case->add(Validator::value(100)->positive(), [
        "equal" => [true],
    ], "Expect positive to be true");

    $case->add(Validator::value(-100)->negative(), [
        "equal" => [true],
    ], "Expect negative to be true");

    $case->add(Validator::value(10)->min(10), [
        "equal" => [true],
    ], "Expect min to be true");

    $case->add(Validator::value(10)->max(10), [
        "equal" => [true],
    ], "Expect max to be true");

    $case->add(Validator::value("Lorem ipsum")->length(1, 11), [
        "equal" => [true],
    ], "Expect length to be true");

    $case->add(Validator::value("22222")->equalLength(5), [
        "equal" => [true],
    ], "Expect equalLength to be true");

    $case->add(Validator::value("hello")->equal("hello"), [
        "equal" => [true],
    ], "Expect equal to be true");

    $case->add(Validator::value("world")->notEqual("hello"), [
        "equal" => [true],
    ], "Expect notEqual to be true");

    $case->add(Validator::value("1.2.3")->validVersion(true), [
        "equal" => [true],
    ], "Expect validVersion to be true");

    $case->add(Validator::value("1.2.0")->versionCompare("1.2.0"), [
        "equal" => [true],
    ], "Expect versionCompare to be true");

    $case->add(Validator::value("MyStrongPass")->lossyPassword(), [
        "equal" => [true],
    ], "Expect lossyPassword to be true");

    $case->add(Validator::value("My@StrongPass12")->strictPassword(), [
        "equal" => [true],
    ], "Expect strictPassword to be true");

    $case->add(Validator::value("HelloWorld")->atoZ(), [
        "equal" => [true],
    ], "Expect atoZ to be true");

    $case->add(Validator::value("welloworld")->lowerAtoZ(), [
        "equal" => [true],
    ], "Expect lowerAtoZ to be true");

    $case->add(Validator::value("HELLOWORLD")->upperAtoZ(), [
        "equal" => [true],
    ], "Expect upperAtoZ to be true");

    $case->add(Validator::value("#F1F1F1")->hex(), [
        "equal" => [true],
    ], "Expect hex to be true");

    $case->add(Validator::value("1922-03-01")->date(), [
        "equal" => [true],
    ], "Expect date to be true");

    $case->add(Validator::value("1988-08-21")->age(18), [
        "equal" => [true],
    ], "Expect age to be true");

    $case->add(Validator::value("example.se")->domain(), [
        "equal" => [true],
    ], "Expect domain to be true");

    $case->add(Validator::value("https://example.se")->url(), [
        "equal" => [true],
    ], "Expect url to be true");


    $case->add(Validator::value("daniel@creativearmy.se")->isDeliverableEmail(), [
        "equal" => [true],
    ], "isDeliverableEmail failed");

    $case->add(Validator::value("daniel@creativearmy.se")->dns()->isMxRecord(), [
        "equal" => [true],
    ], "isMxRecord failed");

    $case->add(Validator::value("examplethatwillfail.se")->dns()->isAddressRecord(), [
        "equal" => [false],
    ], "Expect dns to be false");

    $case->add(Validator::value("Lorem ipsum")->oneOf([
        "length" => [120, 200],
        "isString" => []
    ]), [
        "equal" => [true],
    ], "Expect oneOf to be true");

    $case->add(Validator::value("Lorem ipsum")->allOf([
        "length" => [1, 200],
        "isString" => []
    ]), [
        "equal" => [true],
    ], "Expect allOf to be true");

    $case->add(Validator::value("required")->required(), [
        "equal" => [true],
    ], "Expect required to be true");

    $case->add(Validator::value("required")->required(), [
        "equal" => [true],
    ], "Expect required to be true");

    $case->add(Validator::value("required")->required(), [
        "equal" => [true],
    ], "Expect required to be true");

    $case->add(Validator::value("required")->required(), [
        "equal" => [true],
    ], "Expect required to be true");


    $validPool = new ValidationChain("john.doe@gmail.com");

    $validPool->isEmail()
        ->length(1, 16)
        ->isEmail()
        ->notIsPhone()
        ->endsWith(".net");


    $case->validate($validPool->isValid(), function(ValidationChain $inst) {
        $inst->isFalse();
    });

    $case->validate($validPool->hasError(), function(ValidationChain $inst) {
        $inst->isTrue();
    });

    $case->validate(count($validPool->getFailedValidations()), function(ValidationChain $inst) {
        $inst->isEqualTo(2);
    });


    $case->validate(Validator::value("GET")->isRequestMethod(), function(ValidationChain $inst) {
        $inst->istrue();
    });

    $case->validate(Validator::value("POST")->isRequestMethod(), function(ValidationChain $inst) {
        $inst->istrue();
    });

    $case->validate(Validator::value("PUT")->isRequestMethod(), function(ValidationChain $inst) {
        $inst->istrue();
    });

    $case->validate(Validator::value("DELETE")->isRequestMethod(), function(ValidationChain $inst) {
        $inst->istrue();
    });

    $case->validate(Validator::value("PATCH")->isRequestMethod(), function(ValidationChain $inst) {
        $inst->istrue();
    });

    $case->validate(Validator::value("HEAD")->isRequestMethod(), function(ValidationChain $inst) {
        $inst->istrue();
    });

    $case->validate(Validator::value("OPTIONS")->isRequestMethod(), function(ValidationChain $inst) {
        $inst->istrue();
    });

    $case->validate(Validator::value("options")->isRequestMethod(), function(ValidationChain $inst) {
        $inst->istrue();
    });


    //echo $inst->listAllProxyMethods(Validator::class, isolateClass: true);
    //echo $inst->listAllProxyMethods(Validator::class, "not", isolateClass: true);
});
