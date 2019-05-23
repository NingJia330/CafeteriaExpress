<?php
	//helper functions for validating user input
/*
	check if value has a value
	first trim white space
	then check if both:
	the variable is set
	it is not equal to empty string (note ===)
*/
function has_presence($value) {
	$trimmed_value = trim($value);
	if(!isset($trimmed_value))
		return false;
	if($trimmed_value === "")
		return false;
	return true;
}

/*
 check for a specific length
 pass in array of options in associative array
 options: exact, max, min
 has_length($first_name, ['exact' => 20])
 has_length($first_name, ['min' => 5, 'max' => 100])
*/
function has_length($value, $options=[]) {
	if(isset($options['max']) && (strlen($value) > (int)$options['max'])) {
		return false;
	}
	if(isset($options['min']) && (strlen($value) < (int)$options['min'])) {
		return false;
	}
	if(isset($options['exact']) && (strlen($value) != (int)$options['exact'])) {
		return false;
	}
	return true;
}
/*
check against a regular expression

 (Use \A and \Z, not ^ and $ which allow line returns.) 
 
 Example:
 has_format_matching('1234', '/\d{4}/') is true
 has_format_matching('12345', '/\d{4}/') is also true
 has_format_matching('12345', '/\A\d{4}\Z/') is false
*/
function has_format_matching($value, $regex='//') {
	return preg_match($regex, $value);
}

// * validate value is inclused in a set
function has_inclusion_in($value, $set=[]) {
  return in_array($value, $set);
}

// * validate value is excluded from a set
function has_exclusion_from($value, $set=[]) {
  return !in_array($value, $set);
}

?>
