# Fuse - Validation
Validate inputs. Open the file "Inp.php" for a lot more validations.

### Initiation
You will always initiate instace with the static method **_val** followed by a value you want to validate.
```
use Validate\Inp;
Inp::_values("VALUE")->[VALIDATE_METHOD]();
```
### Check string length is more than or equal to 1
```
Inp::_values("Lorem ipsum dolor")->length(1);
```
### Check string length is more/equal than 1 and less/equal than 160
```
Inp::_values("Lorem ipsum dolor")->length(1, 160);
```
### check if is valid email
```
Inp::_values("john@gmail.com")->email();
```
### check if is valid phone
Will allow only numbers and some characters like (”-”, ”+” and ” ”).
```
Inp::_values("+46709676040")->phone();
```
### Validate Swedish social number (personnummer)
```
Inp::_values("198808213412")->socialNumber();
```
### Validate Swedish organisation number
```
Inp::_values("197511043412")->orgNumber();
```
### Validate credit card number
```
Inp::_values("1616523623422334")->creditcard();
```
### Validate VAT number
```
Inp::_values("SE8272267913")->vatNumber();
```
### Check if is a color hex code
```
Inp::_values("#000000")->hex();
```
### Check date and date format
```
Inp::_values("2022/02/13 14:15")->date(”Y/m/d H:i”);
```
### Check date, date format and is between a range
```
Inp::_values("2022/02/13 - 2022/02/26")->date(”Y/m/d”);
```
### Check if persons is at least 18 years old or more.
```
Inp::_values("2001/05/22")->age(”18”);
```
### Check if is a valid domain name
```
Inp::_values("example.com")->domain();
```
### Check if is a valid URL (http/https is required)
```
Inp::_values("https://example.com/page")->url();
```
### Check if is a valid DNS
Will check compare result against DNS server and match A, AAAA and MX
```
Inp::_values("example.com")->dns();
```
Open the file for a lot more validations.
