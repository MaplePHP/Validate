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

You can validate values by instantiating the `Validator` class. There are two ways to do this:

```php
use MaplePHP\Validate\Validator;

// Option 1: Create an instance
$inp = new Validator("Lorem ipsum dolor");
var_dump($inp->length(1, 200)); // true

// Option 2: Use the static method for cleaner syntax
$valid = Validator::value("Lorem ipsum dolor")->length(1, 200);
var_dump($valid); // true
```

---

## Validating Nested Data

You can traverse nested arrays or objects and validate specific values using dot notation:

```php
$inp = new Validator([
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

## Using the Chain validations

The `ValidationChain` class allows you to chain multiple validations on a single value and check the overall result:

```php
use MaplePHP\Validate\ValidationChain;

$validPool = new ValidationChain("john.doe@gmail.com");

$validPool->isEmail()
    ->length(1, 200)
    ->endsWith(".com");

$isValid = $validPool->isValid();
// $hasError = $validPool->hasError();

var_dump($isValid); // true
```

> 🧠 `ValidationChain` is useful when you want to collect and evaluate multiple validation rules at once.


## Validations

### Required field
```php
Validator::value("Lorem ipsum dolor")->isRequired();
```

### Check if there is any value (even if it's 0)
```php
Validator::value(0)->hasValue();
```

### Check string length (min, max)
- **Min only**:
```php
Validator::value("Lorem ipsum dolor")->length(1);
```
- **Min and Max**:
```php
Validator::value("Lorem ipsum dolor")->length(1, 160);
```

### Check if string has an exact length
```php
Validator::value("Lorem ipsum dolor")->isLengthEqualTo(10);
```

### Check if value equals exactly to or not equals another value
- **Equals**: Strict data type validation check if equals to expected value
```php
Validator::value("Lorem ipsum dolor")->isEqualTo("Lorem ipsum dolor");
```

- **Loosely Equals**: Flexible data type validation check if loosely equals to expected value
```php
Validator::value("Lorem ipsum dolor")->isLooselyEqualTo("Lorem ipsum dolor");
```

- **Not equals**: Strict data type validation check if not equals to expected value
```php
Validator::value("Lorem ipsum dolor")->isNotEqualTo("Lorem ipsum");
```

- **Loosely Not equals**: Flexible data type validation check if loosely not equals to expected value
```php
Validator::value("Lorem ipsum dolor")->isLooselyNotEqualTo("Lorem ipsum");
```

- **More than**:
```php
Validator::value(200)->isMoreThan(100);
```

- **Less than**:
```php
Validator::value(100)->isLessThan(200);
```

- **Contains**:
```php
Validator::value("Lorem ipsum dolor")->contains("ipsum");
```

- **Starts with**:
```php
Validator::value("Lorem ipsum dolor")->startsWith("Lorem");
```

- **Ends with**:
```php
Validator::value("Lorem ipsum dolor")->endsWith("dolor");
```

### Validate if it's a valid email
```php
Validator::value("john@gmail.com")->isEmail();
```

### Validate if it's a valid phone number
Allows numbers and special characters ("-", "+", " ").
```php
Validator::value("+46709676040")->isPhone();
```

### Validate Swedish personal number (personnel)
```php
Validator::value("198808213412")->isSocialNumber();
```

### Validate Swedish organization number
```php
Validator::value("197511043412")->isOrgNumber();
```

### Validate credit card number
```php
Validator::value("1616523623422334")->isCreditCard();
```

### Validate VAT number
```php
Validator::value("SE8272267913")->isVatNumber();
```

### Check if value is a valid float
```php
Validator::value(3.1415)->isFloat();
```

### Check if value is a valid integer
```php
Validator::value(42)->isInt();
```

### Check if value is a valid number (numeric)
```php
Validator::value(42)->isNumber();
```

### Check if value is positive or negative
- **Positive**:
```php
Validator::value(20)->isPositive();
```
- **Negative**:
```php
Validator::value(-20)->isNegative();
```

### Check if value is a valid version number
```php
// True === validate as a semantic Versioning, e.g. 1.0.0
Validator::value("1.0.0")->isValidVersion(true);
```

### Compare version with another version
```php
Validator::value("1.0.0")->versionCompare("2.0.0", '>=');
```

### Validate password (lossy or strict)
- **Lossy password (minimum character set)**:
```php
Validator::value("password123")->isLossyPassword(8);
```
- **Strict password** (requires at least one lowercase, uppercase, digit, and special character):
```php
Validator::value("Password#123!")->isStrictPassword(8);
```

### Validate if value is string and contains only A-Z
- **Both cases**:
```php
Validator::value("HelloWorld")->atoZ();
```
- **Lowercase only**:
```php
Validator::value("helloworld")->lowerAtoZ();
```
- **Uppercase only**:
```php
Validator::value("HELLOWORLD")->upperAtoZ();
```

### Check if it's a valid hex color code
```php
Validator::value("#000000")->hex();
```

### Check if it's a valid date
As default you can validate against a date format like this "Y-m-d"
```php
Validator::value("2022-02-13")->isDate();
```
Custom date validation  
```php
Validator::value("2022/02/13 14:15")->isDate(Y/m/d H:i);
```

### Check if it's a valid date and time
```php
Validator::value("2022-02-13 14:15:58")->isDateWithTime();
```

### Check if it's a valid time
Validate hour and minutes
```php
Validator::value("14:15")->isTime();
```
Validate hour, minutes and seconds
```php
Validator::value("14:15:58")->isTime(true);
```

### Check if someone is at least a certain age
```php
Validator::value("1988-05-22")->isAge(18);
```

### Check if it's a valid domain name
```php
Validator::value("example.com")->isDomain();
```

### Check if it's a valid URL (http/https is required)
```php
Validator::value("https://example.com/page")->isUrl();
```

### Check if it's a valid DNS entry
```php
Validator::value("example.com")->isDns();
```

### Validate file and directory properties
- **Check if it's a valid file**:
```php
Validator::value("/path/to/file.txt")->isFile();
```
- **Check if it's a directory**:
```php
Validator::value("/path/to/directory")->isDir();
```
- **Check if it's writable**:
```php
Validator::value("/path/to/file.txt")->isWritable();
```
- **Check if it's readable**:
```php
Validator::value("/path/to/file.txt")->isReadable();
```

### Validate ZIP code (with custom length)
```php
Validator::value("12345")->isZip(5);
```

### Validate if value matches a pattern (regex)
```php
Validator::value("abc")->pregMatch("a-zA-Z");
```

## Validate Arrays

### Check if is an array
```php
Validator::value(["Apple", "Orange", "Lemon"])->isArray();
```

### Check if array is empty
```php
Validator::value(["Apple", "Orange", "Lemon"])->isArrayEmpty();
```

### Strict data type validation check if value exists in given array
```php
Validator::value(["Apple", "Orange", "Lemon"])->isInArray();
```

### Flexible data type validation check if value exists in given array
```php
Validator::value(["Apple", "Orange", "Lemon"])->isLooselyInArray();
```

### Strict data type validation check if key exists in array
```php
Validator::value(["Apple", "Orange", "Lemon"])->keyExists();
```

### Check if all items in array is truthy
```php
Validator::value(["1", true, "Lemon"])->itemsAreTruthy();
```

### Check if truthy item exist in array
```php
Validator::value(["1", false, "Lemon"])->hasTruthyItem();
```

### Check if array count is equal to length
```php
Validator::value(["Apple", "Orange", "Lemon"])->isCountEqualTo(3);
```

### Check if array count is more than the length
```php
Validator::value(["Apple", "Orange", "Lemon"])->isCountMoreThan(1);
```

### Check if array count is less than the length
```php
Validator::value(["Apple", "Orange", "Lemon"])->isCountLessThan(4);
```

### Check if value is a valid float
```php
Validator::value("Lorem ipsum dolor")->isString();
```

## Validate types

### Check if value is a valid float
```php
Validator::value("Lorem ipsum dolor")->isString();
```
### Check if value is a valid float
```php
Validator::value(3.1415)->isFloat();
```
### Check if value is a valid integer
```php
Validator::value(42)->isInt();
```
- **Is Boolean**:
```php
Validator::value(true)->isBool();
```
- **Is Boolean-like value** (e.g., "yes", "no", "1", "0"):
```php
Validator::value("yes")->isBoolVal();
```
- **Array**:
```php
Validator::value([1, 2, 3])->isArray();
```
- **Object**:
```php
Validator::value($obj)->isObject();
```
- **Resource**:
```php
Validator::value($resource)->isResource();
```

- **Json**:
```php
Validator::value($jsonStr)->isJson();
```

- **HTML Document**:
```php
Validator::value($jsonStr)->isFullHtml();
```


## HTTP status code validation

#### Strict data type validation check if value is a valid HTTP status code
```php
Validator::value(403)->isHttpStatusCode();
```

#### Strict data type validation check if value is HTTP 200 OK
```php
Validator::value(200)->isHttp200();
```

#### Strict data type validation check if value is a 2xx success HTTP code
```php
Validator::value(210)->isHttpSuccess();
```

#### Strict data type validation check if value is a 4xx client error HTTP code
```php
Validator::value(403)->isHttpClientError();
```

#### Strict data type validation check if value is a 5xx server error HTTP code
```php
Validator::value(500)->isHttpServerError();
```


### Validate using multiple methods (one or all must match)
- **Validate if one method passes**:
```php
Validator::value("12345")->oneOf(['isInt' => []]);
```
- **Validate if all methods pass**:
```php
Validator::value("12345")->allOf(['isInt' => [], 'length' => [5]]);
```