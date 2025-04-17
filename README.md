# MaplePHP - Validation

MaplePHP - Validation is a lightweight and powerful PHP library designed to simplify the validation of various data inputs. Whether you're verifying if a value is a valid email or phone number, ensuring string lengths, or performing more advanced checks like credit card numbers and dates, MaplePHP - Validation offers a comprehensive and intuitive approach. With its wide range of built-in validators and simple syntax, it makes handling complex validation tasks easier, leading to cleaner and more reliable code.

---

## Installation

Install the library via Composer:

```bash
composer require maplephp/validate
```

---

## Getting Started

You can validate values by instantiating the `Inp` class. There are two ways to do this:

```php
use MaplePHP\Validate\Inp;

// Option 1: Create an instance
$inp = new Inp("Lorem ipsum dolor");
var_dump($inp->length(1, 200)); // true

// Option 2: Use the static method for cleaner syntax
$valid = Inp::value("Lorem ipsum dolor")->length(1, 200);
var_dump($valid); // true
```

---

## Validating Nested Data

You can traverse nested arrays or objects and validate specific values using dot notation:

```php
$inp = new Inp([
  "user" => [
    "name" => "John Doe",
    "email" => "john.doe@gmail.com",
  ]
]);

$valid = $inp->eq("user.name")->length(1, 200);

var_dump($valid); // true
```

> 💡 You can also use `validateInData()` for more dynamic validations:
```php
$valid = $inp->validateInData("user.name", "length", [1, 200]);
```

---

## Using the Validation Pool

The `ValidatePool` class allows you to chain multiple validations on a single value and check the overall result:

```php
use MaplePHP\Validate\ValidatePool;

$validPool = new ValidatePool("john.doe@gmail.com");

$validPool->isEmail()
    ->length(1, 200)
    ->endsWith(".com");

$isValid = $validPool->isValid();
// $hasError = $validPool->hasError();

var_dump($isValid); // true
```

> 🧠 `ValidatePool` is useful when you want to collect and evaluate multiple validation rules at once.


## Validations

### Required field
```php
Inp::value("Lorem ipsum dolor")->isRequired();
```

### Check if there is any value (even if it's 0)
```php
Inp::value(0)->hasValue();
```

### Check string length (min, max)
- **Min only**:
```php
Inp::value("Lorem ipsum dolor")->length(1);
```
- **Min and Max**:
```php
Inp::value("Lorem ipsum dolor")->length(1, 160);
```

### Check if string has an exact length
```php
Inp::value("Lorem ipsum dolor")->isLengthEqualTo(10);
```

### Check if value equals exactly to or not equals another value
- **Equals**: Strict data type validation check if equals to expected value
```php
Inp::value("Lorem ipsum dolor")->isEqualTo("Lorem ipsum dolor");
```

- **Loosely Equals**: Flexible data type validation check if loosely equals to expected value
```php
Inp::value("Lorem ipsum dolor")->isLooselyEqualTo("Lorem ipsum dolor");
```

- **Not equals**: Strict data type validation check if not equals to expected value
```php
Inp::value("Lorem ipsum dolor")->isNotEqualTo("Lorem ipsum");
```

- **Loosely Not equals**: Flexible data type validation check if loosely not equals to expected value
```php
Inp::value("Lorem ipsum dolor")->isLooselyNotEqualTo("Lorem ipsum");
```

- **More than**:
```php
Inp::value(200)->isMoreThan(100);
```

- **Less than**:
```php
Inp::value(100)->isLessThan(200);
```

- **Contains**:
```php
Inp::value("Lorem ipsum dolor")->contains("ipsum");
```

- **Starts with**:
```php
Inp::value("Lorem ipsum dolor")->startsWith("Lorem");
```

- **Ends with**:
```php
Inp::value("Lorem ipsum dolor")->endsWith("dolor");
```

### Validate if it's a valid email
```php
Inp::value("john@gmail.com")->isEmail();
```

### Validate if it's a valid phone number
Allows numbers and special characters ("-", "+", " ").
```php
Inp::value("+46709676040")->isPhone();
```

### Validate Swedish personal number (personnel)
```php
Inp::value("198808213412")->isSocialNumber();
```

### Validate Swedish organization number
```php
Inp::value("197511043412")->isOrgNumber();
```

### Validate credit card number
```php
Inp::value("1616523623422334")->isCreditCard();
```

### Validate VAT number
```php
Inp::value("SE8272267913")->isVatNumber();
```

### Check if value is a valid float
```php
Inp::value(3.1415)->isFloat();
```

### Check if value is a valid integer
```php
Inp::value(42)->isInt();
```

### Check if value is a valid number (numeric)
```php
Inp::value(42)->isNumber();
```

### Check if value is positive or negative
- **Positive**:
```php
Inp::value(20)->isPositive();
```
- **Negative**:
```php
Inp::value(-20)->isNegative();
```

### Check if value is a valid version number
```php
// True === validate as a semantic Versioning, e.g. 1.0.0
Inp::value("1.0.0")->isValidVersion(true);
```

### Compare version with another version
```php
Inp::value("1.0.0")->versionCompare("2.0.0", '>=');
```

### Validate password (lossy or strict)
- **Lossy password (minimum character set)**:
```php
Inp::value("password123")->isLossyPassword(8);
```
- **Strict password** (requires at least one lowercase, uppercase, digit, and special character):
```php
Inp::value("Password#123!")->isStrictPassword(8);
```

### Validate if value is string and contains only A-Z
- **Both cases**:
```php
Inp::value("HelloWorld")->atoZ();
```
- **Lowercase only**:
```php
Inp::value("helloworld")->lowerAtoZ();
```
- **Uppercase only**:
```php
Inp::value("HELLOWORLD")->upperAtoZ();
```

### Check if it's a valid hex color code
```php
Inp::value("#000000")->hex();
```

### Check if it's a valid date
As default you can validate against a date format like this "Y-m-d"
```php
Inp::value("2022-02-13")->isDate();
```
Custom date validation  
```php
Inp::value("2022/02/13 14:15")->isDate(Y/m/d H:i);
```

### Check if it's a valid date and time
```php
Inp::value("2022-02-13 14:15:58")->isDateWithTime();
```

### Check if it's a valid time
Validate hour and minutes
```php
Inp::value("14:15")->isTime();
```
Validate hour, minutes and seconds
```php
Inp::value("14:15:58")->isTime(true);
```

### Check if someone is at least a certain age
```php
Inp::value("1988-05-22")->isAge(18);
```

### Check if it's a valid domain name
```php
Inp::value("example.com")->isDomain();
```

### Check if it's a valid URL (http/https is required)
```php
Inp::value("https://example.com/page")->isUrl();
```

### Check if it's a valid DNS entry
```php
Inp::value("example.com")->isDns();
```

### Validate file and directory properties
- **Check if it's a valid file**:
```php
Inp::value("/path/to/file.txt")->isFile();
```
- **Check if it's a directory**:
```php
Inp::value("/path/to/directory")->isDir();
```
- **Check if it's writable**:
```php
Inp::value("/path/to/file.txt")->isWritable();
```
- **Check if it's readable**:
```php
Inp::value("/path/to/file.txt")->isReadable();
```

### Validate ZIP code (with custom length)
```php
Inp::value("12345")->isZip(5);
```

### Validate if value matches a pattern (regex)
```php
Inp::value("abc")->pregMatch("a-zA-Z");
```

## Validate Arrays

### Check if is an array
```php
Inp::value(["Apple", "Orange", "Lemon"])->isArray();
```

### Check if array is empty
```php
Inp::value(["Apple", "Orange", "Lemon"])->isArrayEmpty();
```

### Strict data type validation check if value exists in given array
```php
Inp::value(["Apple", "Orange", "Lemon"])->isInArray();
```

### Flexible data type validation check if value exists in given array
```php
Inp::value(["Apple", "Orange", "Lemon"])->isLooselyInArray();
```

### Strict data type validation check if key exists in array
```php
Inp::value(["Apple", "Orange", "Lemon"])->keyExists();
```

### Check if all items in array is truthy
```php
Inp::value(["1", true, "Lemon"])->itemsAreTruthy();
```

### Check if truthy item exist in array
```php
Inp::value(["1", false, "Lemon"])->hasTruthyItem();
```

### Check if array count is equal to length
```php
Inp::value(["Apple", "Orange", "Lemon"])->isCountEqualTo(3);
```

### Check if array count is more than the length
```php
Inp::value(["Apple", "Orange", "Lemon"])->isCountMoreThan(1);
```

### Check if array count is less than the length
```php
Inp::value(["Apple", "Orange", "Lemon"])->isCountLessThan(4);
```

### Check if value is a valid float
```php
Inp::value("Lorem ipsum dolor")->isString();
```

## Validate types

### Check if value is a valid float
```php
Inp::value("Lorem ipsum dolor")->isString();
```
### Check if value is a valid float
```php
Inp::value(3.1415)->isFloat();
```
### Check if value is a valid integer
```php
Inp::value(42)->isInt();
```
- **Is Boolean**:
```php
Inp::value(true)->isBool();
```
- **Is Boolean-like value** (e.g., "yes", "no", "1", "0"):
```php
Inp::value("yes")->isBoolVal();
```
- **Array**:
```php
Inp::value([1, 2, 3])->isArray();
```
- **Object**:
```php
Inp::value($obj)->isObject();
```
- **Resource**:
```php
Inp::value($resource)->isResource();
```

- **Json**:
```php
Inp::value($jsonStr)->isJson();
```

- **HTML Document**:
```php
Inp::value($jsonStr)->isFullHtml();
```


## HTTP status code validation

#### Strict data type validation check if value is a valid HTTP status code
```php
Inp::value(403)->isHttpStatusCode();
```

#### Strict data type validation check if value is HTTP 200 OK
```php
Inp::value(200)->isHttp200();
```

#### Strict data type validation check if value is a 2xx success HTTP code
```php
Inp::value(210)->isHttpSuccess();
```

#### Strict data type validation check if value is a 4xx client error HTTP code
```php
Inp::value(403)->isHttpClientError();
```

#### Strict data type validation check if value is a 5xx server error HTTP code
```php
Inp::value(500)->isHttpServerError();
```


### Validate using multiple methods (one or all must match)
- **Validate if one method passes**:
```php
Inp::value("12345")->oneOf(['isInt' => []]);
```
- **Validate if all methods pass**:
```php
Inp::value("12345")->allOf(['isInt' => [], 'length' => [5]]);
```