<?php
/**
 * @file cvalidate.api.php
 */

/**
 * Validating name format.
 * ex. if input is "Albert Liu" or "Liu, Albert"
 * it will returnt a array like:
 * array("Albert", "Liu")
 */
function cvalidate_name($str) {
  $str = _cvalidate_filter($str, 'trim');
  if (empty($str)) {
    return FALSE;
  }
  if (preg_match("/[a-zA-Z]/", $str)) { // check for english name
    if (preg_match("/[,]/", $str)) { // has comma will be reverse
      // $name = array_reverse(preg_split("/[\s,]+/", $str));
      $name = explode(',', $str);	
    }
    else { // has space
      $name = array_reverse(preg_split("/[\s,]+/", $str));
    }
    
  }
  else { // check for chinese name
    $l_name = mb_substr($str, -2, 2, 'UTF-8');
    $f_name = preg_split('/'.$l_name.'/', $str);
    $name[] = $f_name[0];
    $name[] = $l_name;
  }
  if (count($name) > 2) {
    return FALSE;
  }
  foreach ($name as $key => $value) {
    $name[$key] = trim($value);
  }
  return $name;
}

/**
 * Validating email format.
 * @see http://php.net/manual/en/function.checkdnsrr.php
 * ex: cvalidate_email('dreamerhyde@gmail.com', TRUE));
 * if $checkDNS is "TRUE", it will online to check this email.
 */
function cvalidate_email($email, $checkDNS = FALSE) {
  $email = _cvalidate_filter($email);
  if (empty($email)) {
    return FALSE;
  }
  $valid = (
  function_exists('filter_var') 
  and filter_var($email, FILTER_VALIDATE_EMAIL)) || 
  (strlen($email) <= 320 
    and @preg_match_all(
    '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?))'. 
    '{255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?))'.
    '{65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|'.
    '(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))'.
    '(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|'.
    '(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|'.
    '(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})'.
    '(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126})'.'{1,}'.
    '(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|'.
    '(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|'.
    '(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::'.
    '(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|'.
    '(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|'.
    '(?:(?!(?:.*[a-f0-9]:){5,})'.'(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::'.
    '(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|'.
    '(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|'.
    '(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD',
      $email)
  );
  if($valid) {
    if( $checkDNS && ($domain = end(explode('@',$email, 2))) ) {
      return checkdnsrr($domain . '.', 'MX');
    }
    return $email;
  }
  return FALSE;
}

/**
 * Validating birthday format.
 */
function cvalidate_birthday($str) {
  $str = _cvalidate_filter($str);
  if (empty($str)) {
    return FALSE;
  }
  if (!strtotime($str)) {
    return FALSE;
  }
  $bir = date("Y-m-d", strtotime($str));
  return $bir;
}

/**
 * Validating mobile number.
 */
function cvalidate_mobile($str) {
  $str = _cvalidate_filter($str);
  if (empty($str)) {
    return FALSE;
  }
  if (preg_match("/[-]/", $str)) { // check for english name
    $str = str_replace('-', '', $str);
  }
  if (preg_match("/[+886]/", $str)) {
    $str = str_replace('+886', '0', $str);
  }
  if (strlen($str) != 10) {
    return FALSE;
  }
  $phone = substr($str, 1, 9);
  $phone = '+886-' . $phone;
  return $phone;
}

/**
 * Validating telephone number.
 * @see http://countrycode.org/taiwan
 */
function cvalidate_telephone($str) {
  $str = _cvalidate_filter($str);
  if (empty($str)) {
    return FALSE;
  }
  if (preg_match("/\-/", $str)) { // check for english name
    $str = str_replace('-', '', $str);
  }
  if (preg_match("/\+886/", $str)) {
    $str = str_replace('+886', '0', $str);
  }
  $len = strlen($str);
  if ($len < 9 || $len > 10) {
    return FALSE;
  }
  $phone = substr($str, 1, 9);
  $phone = '+886-' . $phone;
  return $phone;
}


/**
 * Validating personal identity.
 * @see http://home.csjh.tcc.edu.tw/phpbbinf/viewtopic.php?p=22564&sid=4b3146725041db9dcc43efe4cc821aae
 */
function cvalidate_pid($pid) {
  $pid = strtoupper(_cvalidate_filter($pid));
  if (empty($pid)) {
    return FALSE;
  }
  $ereg_pattern = "^[A-Z]{1}[12]{1}[[:digit:]]{8}$";
  if (!ereg($ereg_pattern, $pid)) {
    return false;
  }
  $wd_str = "BAKJHGFEDCNMLVUTSRQPZWYX0000OI";
  $d1 = strpos($wd_str, $pid[0]) % 10;
  $sum = 0;
  for($ii = 1; $ii < 9; $ii++) {
    $sum += (int)$pid[$ii]*(9-$ii);
  }
  $sum += $d1 + (int)$pid[9];
  if ($sum%10 != 0) {
    return false;
  }
  return $pid;
}

/**
 * Talk to Google Maps and return a json of address. 
 * @see https://developers.google.com/maps/documentation/geocoding/#GeocodingResponses
 */
function cvalidate_address($full_address) {
  $full_address = _cvalidate_filter($full_address);
  if (empty($full_address)) {
    return FALSE;
  }
  $address = urlencode($full_address);
  $json = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=true&language=zh-TW');
  $r = json_decode($json)->results;
  if (count($r) > 1) {
    return FALSE;
  }
  $data = $r[0]->address_components;
  $returns['city'] = $data['4']->long_name;
  $returns['region'] = $data['3']->long_name;

  // if count zip 5 will separate to 3 + 2
  if (preg_match("/^[0-9]{5}/", $full_address)) {
    $returns['zip']['0'] = substr($full_address, 0, 3);
    $returns['zip']['1'] = substr($full_address, 3, 2);
  }
  else {
    $returns['zip'] = $data['6']->long_name;
  }
  $returns['street'] = $data['1']->long_name;
  foreach ($returns as $item) {
    if (empty($item)) {
      return FALSE;
    }
  }
  return $returns;
}

/**
 * Remove space.
 * @param $str
 *   input string
 * @param $op
 *   all  - strip all space
 *   trim - trim left and right space
 */
function _cvalidate_filter($str, $op = 'all') {
  if (empty($str)) {
    return FALSE;
  }
  switch ($op) {
    case 'all':
      $str = str_replace(' ', '', $str);
      $str = str_replace('　', '', $str);
      break;
    case 'trim':
      $str = trim($str);
      break;
    default:
      $str = str_replace(' ', '', $str);
      $str = str_replace('　', '', $str);
      break;
  }
  return $str;
}
