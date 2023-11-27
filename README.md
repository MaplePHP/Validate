# MaplePHP - Validation
Validate inputs. Open the file "Inp.php" for a lot more validations.

### Initiation
You will always initiate instace with the static method **_val** followed by a value you want to validate.

```php
use Validate\Inp;

// Validate option 1
$inp = new Inp("Lorem ipsum dolor");
var_dump($inp->length(1, 200)); // true

// Validate option 2
$valid = Inp::value("Lorem ipsum dolor")->length(1, 200);
var_dump($valid); // true
```

### Check string length is more than or equal to 1
```php
Inp::value("Lorem ipsum dolor")->length(1);
```
### Check string length is more/equal than 1 and less/equal than 160
```php
Inp::value("Lorem ipsum dolor")->length(1, 160);
```
### Check if is valid email
```php
Inp::value("john@gmail.com")->email();
```
### Check if is valid phone
Will allow only numbers and some characters like ("-", "+" and " ").
```php
Inp::value("+46709676040")->phone();
```
### Validate Swedish social number (personnummer)
```php
Inp::value("198808213412")->socialNumber();
```
### Validate Swedish organisation number
```php
Inp::value("197511043412")->orgNumber();
```
### Validate credit card number
```php
Inp::value("1616523623422334")->creditcard();
```
### Validate VAT number
```php
Inp::value("SE8272267913")->vatNumber();
```
### Check if is a color hex code
```php
Inp::value("#000000")->hex();
```
### Check date and date format
```php
Inp::value("2022/02/13 14:15")->date("Y/m/d H:i");
// The date argument is the expected date format (will also take time)
```
### Check date, date format and is between a range
```php
Inp::value("2022/02/13 - 2022/02/26")->dateRange("Y/m/d"); 
// The dateRange argument is the expected date format (will also take time)
```
### Check if persons is at least 18 years old or older.
```php
Inp::value("1988-05-22")->age("18");
```
### Check if is a valid domain name
```php
Inp::value("example.com")->domain();
```
### Check if is a valid URL (http/https is required)
```php
Inp::value("https://example.com/page")->url();
```
### Check if is a valid DNS
Will check compare result against DNS server and match A, AAAA and MX
```php
Inp::value("example.com")->dns();
```
Open the file for a lot more validations.
