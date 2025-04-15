# MaplePHP - Validation
MaplePHP - Validation is a lightweight and powerful PHP library designed to simplify the validation of various data inputs. Whether you're verifying if a value is a valid email or phone number, ensuring string lengths, or performing more advanced checks like credit card numbers and dates, MaplePHP - Validation offers a comprehensive and intuitive approach. With its wide range of built-in validators and simple syntax, it makes handling complex validation tasks easier, leading to cleaner and more reliable code.

## Installation
```
composer require maplephp/validate
```

## Initiation
You will always initiate an instance with the static method **_val** followed by a value you want to validate.

```php
use MaplePHP\Validate\Inp;

// Validate option 1
$inp = new Inp("Lorem ipsum dolor");
var_dump($inp->length(1, 200)); // true

// Validate option 2
$valid = Inp::value("Lorem ipsum dolor")->length(1, 200);
var_dump($valid); // true
```

## Travers
It is possible to traverse validate items inside arrays and objects. 
```php
$inp = new Inp([
  "user" => [
    "name" => "John Doe",
    "email" => "john.doe@gmail.com",
  ]
]);
$valid = $inp->traverse("user.name")->length(1, 200);
// This below is the same as above but can be used for other purposes
// $valid = $inp->validateInData("user.name", "length", [1, 200]);

var_dump($valid); // true
```


## Validations

### Required field
```php
Inp::value("Lorem ipsum dolor")->required();
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

### Check if value equals or not equals another value
- **Equals**:
```php
Inp::value("Lorem ipsum dolor")->isEqualTo("Lorem ipsum dolor");
```
- **Not equals**:
```php
Inp::value("Lorem ipsum dolor")->isNotEqualTo("Lorem ipsum");
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
Inp::value("john@gmail.com")->email();
```

### Validate if it's a valid phone number
Allows numbers and special characters ("-", "+", " ").
```php
Inp::value("+46709676040")->phone();
```

### Validate Swedish personal number (personnummer)
```php
Inp::value("198808213412")->socialNumber();
```

### Validate Swedish organization number
```php
Inp::value("197511043412")->orgNumber();
```

### Validate credit card number
```php
Inp::value("1616523623422334")->creditCard();
```

### Validate VAT number
```php
Inp::value("SE8272267913")->vatNumber();
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
Inp::value("password123")->lossyPassword(8);
```
- **Strict password** (requires at least one lowercase, uppercase, digit, and special character):
```php
Inp::value("Password#123!")->strictPassword(8);
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
```php
Inp::value("2022-02-13")->date("Y-m-d");
```

### Check if it's a valid date and time
```php
Inp::value("2022-02-13 14:15")->dateTime("Y-m-d H:i");
```

### Check if it's a valid time
```php
Inp::value("14:15")->time("H:i");
```

### Check if someone is at least a certain age
```php
Inp::value("1988-05-22")->age(18);
```

### Check if it's a valid domain name
```php
Inp::value("example.com")->domain();
```

### Check if it's a valid URL (http/https is required)
```php
Inp::value("https://example.com/page")->url();
```

### Check if it's a valid DNS entry
```php
Inp::value("example.com")->dns();
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
Inp::value("12345")->zip(5);
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


### Validate using multiple methods (one or all must match)
- **Validate if one method passes**:
```php
Inp::value("12345")->oneOf(['isInt' => []]);
```
- **Validate if all methods pass**:
```php
Inp::value("12345")->allOf(['isInt' => [], 'length' => [5]]);
```