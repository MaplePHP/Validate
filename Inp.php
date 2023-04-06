<?php 
/**
 * @Package: 	PHP Fuse - Input data for validation
 * @Author: 	Daniel Ronkainen
 * @Licence: 	The MIT License (MIT), Copyright © Daniel Ronkainen
 				Don't delete this comment, its part of the license.
 * @Version: 	1.0.0
 */

namespace PHPFuse\Validate;

class Inp {

	private $_value;
	private $_length;
	private $_dateTime;
	private $_luhn;

	/**
	 * Start instance
	 * @param  ALL $value the input value
	 * @return inst(self)
	 */
	static function value($value) {
		$inst = new self();
		$inst->_value = $value;
		$inst->_length = $inst->getLength($value);
		$inst->_dateTime = new \DateTime("now");
		return $inst;
	}

	function getLength($value) {
		$value = utf8_decode($value);
		return strlen($value);
	}

	/**
	 * Access luhn validation class
	 * @return instance (Form\Luhn)
	 */
	function luhn() {
		if(is_null($this->_luhn)) {
			$this->_luhn = new Luhn($this->_value);
		}
		return $this->_luhn;
	}

	/**
	 * Set field required (same as @length(1));
	 * @return bool
	 */
	function required() {
		return $this->length(1);
	}

	/**
	 * Validate Swedish personal numbers
	 * @return bool
	 */
	function socialNumber() {
		return $this->luhn()->personnummer();
	}

	/**
	 * Validate Swedish personal numbers
	 * @return bool
	 */
	function personnummer() {
		return $this->socialNumber();
	}

	/**
	 * Validate Swedish org numbers
	 * @return bool
	 */
	function orgNumber() {
		return $this->luhn()->orgNumber();
	}

	/**
	 * Validate creditcardnumbers (THIS needs to be tested)
	 * @return bool
	 */
	function creditcard() {
		return $this->luhn()->creditcard();
	}

	/**
	 * Validate Swedish vat number
	 * @return bool
	 */
	function vatNumber() {
		return $this->luhn()->vatNumber();
	}

	/**
	 * Validate email
	 * Loosely check if is email. By loosley I mean it will not check if valid DNS. You can check this manually with the method @dns but in most cases this will not be necessary.
	 * @return bool
	 */
	function email() {
		return (bool)(filter_var($this->_value, FILTER_VALIDATE_EMAIL));
	}

	/**
	 * Find in string
	 * @param  string   $match keyword to match agains
	 * @param  int|null $pos   match start positon if you want
	 * @return bool
	 */
	function findInString(string $match, ?int $pos = NULL) {
	
		
		return (bool)((is_null($pos) && strpos($this->_value, $match) !== false) || (!is_null($pos) && strpos($this->_value, $match) === $pos));
	}

	/**
	 * Alternative "Find in string" @findInString()
	 */
	function strpos(string $match, ?int $pos = NULL) {
		$match = (is_null($pos)) ? false : $pos;
		return (bool)((is_null($pos) && strpos($this->_value, $match) !== $pos) || (!is_null($pos) && strpos($this->_value, $match) === $pos));
	}


	/**
	 * Check if is phone
	 * @return bool
	 */
	function phone() {
		$val = str_replace([" ", "-", "—", "–", "(", ")"], ["", "", "", "", "", ""], $this->_value);
		$match = preg_match('/^[0-9]{7,14}+$/', $val);
		$strict = preg_match('/^\+[0-9]{1,2}[0-9]{6,13}$/', $val);
		return (bool)($strict || $match);
	}

	/**
	 * Check if is valid ZIP
	 * @param  int      $a start length
	 * @param  int|null $b end length
	 * @return bool
	 */
	function zip(int $a, int $b = NULL) {
		$this->_value = str_replace([" ", "-", "—", "–"], ["", "", "", ""], $this->_value);
		$this->_length = $this->getLength($this->_value);
		return ($this->int() && $this->length($a, $b));
	}

	/**
	 * Value is number
	 * @return bool
	 */
	function number() {
		return is_numeric($this->_value);
	}

	function numeric() {
		return $this->number();
	}

	function numericVal() {
		return $this->number();
	}

	/**
	 * Value is number positive 20
	 * @return bool
	 */
	function positive() {
		return ((float)$this->_value >= 0);
	}

	/**
	 * Value is number negative -20
	 * @return bool
	 */
	function negative() {
		return ((float)$this->_value < 0);
	}

	/**
	 * Value is minimum float|int value
	 * @return bool
	 */
	function min(float $i) {
		return ((float)$this->_value >= $i);
	}

	/**
	 * Value is minimum float|int value (Same as "@min()" but can be used to add another error message)
	 * @return bool
	 */
	function minAlt(float $i) {
		return $this->min($i);
	}

	/**
	 * Value is maximum float|int value
	 * @return bool
	 */
	function max(float $i) {
		return ((float)$this->_value <= $i);
	}

	/**
	 * Is value float
	 * @return bool
	 */
	function float() {
		return (bool)filter_var($this->_value, FILTER_VALIDATE_FLOAT);
	}

	/**
	 * Is value int
	 * @return bool
	 */
	function int() {
		return (bool)filter_var($this->_value, FILTER_VALIDATE_INT);
	}

	/**
	 * Value string length is more than start ($a) or between start ($a) and end ($b)
	 * @param  int      $a start length
	 * @param  int|null $b end length
	 * @return bool
	 */
	function length(int $a, int $b = NULL) {
		if($this->_length >= $a && (($b === NULL) || $this->_length <= $b)) {
			return true;
		}
		return false;
	}

	/**
	 * Value string length of OTHER field is more than start ($a) or between start ($a) and end ($b)
	 * @param  string   $key 	HTTP Post KEY
	 * @param  int      $a 		start length
	 * @param  int|null $b 		end length
	 * @return bool
	 */
	function hasLength(string $key, int $a, int $b = NULL) {
		$post = ($_POST[$key] ?? 0);
		$continue = (bool)((int)$post === 1);		
		return (bool)(!$continue || $this->length($a, $b));
	}

	/**
	 * Value string length is equal to ($a)
	 * @param  int 	$a 	length
	 * @return bool
	 */
	function equalLength(int $a) {
		if($this->_length === $a) {
			return true;
		}
		return false;
	}

	/**
	 * IF value equals to param
	 * @return bool
	 */
	function equal($str) {
		return (bool)((string)$this->_value === (string)$str);
	}

	/**
	 * IF value equals to param
	 * @return bool
	 */
	function notEqual($str) {

		return (bool)((string)$this->_value !== (string)$str);
	}

	function equals($str) {
		return $this->equal();
	}

	/**
	 * Is value string
	 * @return bool
	 */
	function string() {
		return is_string($this->_value);
	}

	/**
	 * Is value is string and character between a-z or A-Z
	 * @return bool
	 */
	function pregMatch($matchStr) {
		return (bool)preg_match("/^[".$matchStr."]+$/", $this->_value);
	}


	/**
	 * Is value is string and character between a-z or A-Z
	 * @return bool
	 */
	function AtoZ() {
		return (bool)preg_match("/^[a-zA-Z]+$/", $this->_value);
	}

	/**
	 * Is value is string and character between a-z (LOWERCASE)
	 * @return bool
	 */
	function lowerAtoZ() {
		return (bool)preg_match("/^[a-z]+$/", $this->_value);
	}

	/**
	 * Is value is string and character between A-Z (UPPERCASE)
	 * @return bool
	 */
	function upperAtoZ() {
		return (bool)preg_match("/^[A-Z]+$/", $this->_value);
	}


	/**
	 * Is Hex color code string
	 * @return bool
	 */
	function hex() {
		return preg_match('/^#([0-9A-F]{3}){1,2}$/i', $this->_value);
	}

	/**
	 * Is value array
	 * @return bool
	 */
	function array() {
		return is_array($this->_value);
	}

	/**
	 * Is value object
	 * @return bool
	 */
	function object() {
		return is_object($this->_value);
	}

	/**
	 * Is value bool
	 * @return bool
	 */
	function bool() {
		return is_bool($this->_value);
	}

	/**
	 * If value === ([on, off], [yes, no], [1, 0] or [true, false])
	 * @return bool
	 */
	function boolVal() {
		$v = strtolower(trim((string)$this->_value));
		return (bool)($v === "on" || $v === "yes" || $v === "1" || $v === "true");
	}

	/**
	 * Is value between two other values (1-10, a-z, 1988-08-01-1988-08-10)
	 * @param  int|float|string|date $a 10, a, 1988-08-01
	 * @param  int|float|string|date $b 20, z, 1988-08-20
	 * @return bool
	 */
	function between($a, $b) {

		if($this->number()) {
			return ($this->min() && $this->max());

		} elseif(strlen($a) === 1 && strlen($b) === 1) {
			$r = $this->_range(strtolower($a), strtolower($b));
			$l = count($r);
			if($find = array_search((string)$this->_value, $r)) {
				return (bool)(($find+1) <= $l);
			}

		} elseif($this->date() || $this->dateTime()) {
			$date = new \DateTime($this->_value);
			$from = new \DateTime($a);
			$to = new \DateTime($b);
			return ($date >= $from && $date <= $to);
		}

		return false;
	}

	/**
	 * Check if is a date
	 * @param  string $format validate after this date format (default Y-m-d)
	 * @return bool|inst(dateTime)
	 */
	function date($format = "Y-m-d") {
		return \DateTime::createFromFormat($format, $this->_value);
	}


	/**
	 * Check if is a date and time
	 * @param  string  $format  validate after this date format (default Y-m-d H:i)
	 * @return bool|inst(dateTime)
	 */
	function dateTime($format = "Y-m-d H:i") {
		return $this->date($format);
	}

	/**
	 * Check if is a date and time
	 * @param  string  $format  validate after this date format (default Y-m-d H:i)
	 * @return bool|inst(dateTime)
	 */
	function time($format = "H:i") {
		return $this->date($format);
	}

	/**
	 * Check if is a date and a "valid range"
	 * @param  string $format validate after this date format (default Y-m-d H:i)
	 * @return bool / array(T1, T2); T1 = start and T2 = end
	 */
	function dateRange($format = "Y-m-d H:i") {
		$exp = explode(" - ", $this->_value);
		if(count($exp) === 2) {
			$t1 = trim($exp[0]);
			$t2 = trim($exp[1]);

			$v1 = \DateTime::createFromFormat($format, $t1);
			$v2 = \DateTime::createFromFormat($format, $t2);

			return ($v1 && $v2 && ($v1->getTimestamp() <= $v2->getTimestamp())) ? ["t1" => $t1, "t2" => $t2] : false;
		}
		return false;
	}

	/**
	 * Check "minimum" age (value format should be validate date "Y-m-d")
	 * @param  int    $a 18 == user should be atleast 18 years old
	 * @return [type]    [description]
	 */
	function age(int $a) {
		$now = $this->_dateTime->format("Y");
		$dateTime = new \DateTime($this->_value);
		$birth = $dateTime->format("Y");
		$age = (int)($now-$birth);
		return (bool)($age >= (int)$a);
	}

	/**
	 * Check if is valid domain
	 * @param  boolean $flag stricter = true
	 * @return bool
	 */
	function domain($flag = true) {
		$flag = ($flag) ? FILTER_FLAG_HOSTNAME : false;
		return (bool)filter_var((string)$this->_value, FILTER_VALIDATE_DOMAIN, $flag);
	}

	/**
	 * Check if is valid URL (http|https is required)
	 * @return bool
	 */
	function url() {
		$val = (string)$this->_value;
		
		// Only used to pass validation will not change any data
		$val = str_replace(['{{root}}', '{{url}}'], ["https://example.se", "https://example.se/"], $val);
		$val = str_replace(["å", "ä", "ö"], ["a", "a", "o"], strtolower($val));

		return (bool)filter_var($val, FILTER_VALIDATE_URL);
	}

	/**
	 * Check if "Host|domain" has an valid DNS (will check A, AAAA and MX)
	 * @return bool
	 */
	function dns() {
		$host = $this->_value;
		$Aresult = true;
        $variant = (defined('INTL_IDNA_VARIANT_UTS46')) ? INTL_IDNA_VARIANT_UTS46 : INTL_IDNA_VARIANT_2003;
        $host = rtrim(idn_to_ascii($host, IDNA_DEFAULT, $variant), '.') . '.';
        $MXresult = checkdnsrr($host, 'MX');
        if(!$MXresult) $Aresult = checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA');
        return (bool)($MXresult || $Aresult);
	}
	
	/**
	 * Match DNS record by search for TYPE and matching VALUE  
	 * @param  int $type   (DNS_A, DNS_CNAME, DNS_HINFO, DNS_CAA, DNS_MX, DNS_NS, DNS_PTR, DNS_SOA, DNS_TXT, DNS_AAAA, DNS_SRV, DNS_NAPTR, DNS_A6, DNS_ALL or DNS_ANY)
	 * @param  string $value IPv4, IPv6, String, txt
	 * @return false/array
	 */
	function matchDNS(int $type, string $value) {
		
		$host = $this->_value;
        $variant = INTL_IDNA_VARIANT_2003;
        if(defined('INTL_IDNA_VARIANT_UTS46')) $variant = INTL_IDNA_VARIANT_UTS46;
        $host = rtrim(idn_to_ascii($host, IDNA_DEFAULT, $variant), '.') . '.';
      	$Aresult = dns_get_record($host, $type);

      	if(is_array($Aresult) && count($Aresult) > 0) {
      		return $Aresult;
      	}

      	return false;
	}

	/**
	 * Validate multiple. Will return true if "one" matches 
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	function oneOf(array $arr) {
		$valid = false;
		foreach($arr as $k => $v) {
			if(is_array($v)) {
				if(call_user_func_array(['self', 'length'], $v)) $valid = true;
			} else {
				if($this->{$v}()) $valid = true;
			}
		}
		return $valid;
	}

	/**
	 * Validate multiple. Will return true if "all" matches 
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	function allOf(array $arr) {
		$valid = true;
		foreach($arr as $k => $v) {
			if(is_array($v)) {
				if(!call_user_func_array(['self', 'length'], $v)) $valid = false;
			} else {
				if(!$this->{$v}()) $valid = false;
			}
		}
		return $valid;
	}

	function continue(array $arr1, array $arr2) {
		if($this->allOf($arr1)) {
			if(!$this->required()) return true;
			return $this->allOf($arr2);
		}
		return false;
	}

	// For your information: ÅÄÖ will not be in predicted range.
	private function _range($start, $end) {
	    $result = array();
	    list(, $_start, $_end) = unpack("N*", mb_convert_encoding($start . $end, "UTF-32BE", "UTF-8"));
	    $offset = $_start < $_end ? 1 : -1;
	    $current = $_start;
	    while($current != $_end) {
	        $result[] = mb_convert_encoding(pack("N*", $current), "UTF-8", "UTF-32BE");
	        $current += $offset;
	    }
	    $result[] = $end;
	    return $result;
	}

}
