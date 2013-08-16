<?php
require_once(dirname(dirname(__FILE__)) .'/common/init.php');

if (isset($_POST['content'])) {

  $result = [
    'message' => null,
    'success' => false
  ];

  try {
    $obj = json_decode($_POST['content']);
  } catch (Exception $e) {
    $result['message'] = '資料格式有誤，無法解析JSON.';
    jsonOutput($result);
  }

  $mobile_phone = keyExists('mobile_phone', $obj);
  $email        = keyExists('email'       , $obj);
  $password     = keyExists('password'    , $obj);

  $password     = $password == null ? null : dbEncrypt($password);

  if ( !checkValues([$mobile_phone, $email, $password]) ) {
    $result['message'] = '資料不得為空';
    jsonOutput($result);
  }

  $db = new DB();

  $table        = USER_ACCOUNT;
  $column       = "*";
  $whereClause  = "mobile_phone = '{$mobile_phone}' AND email = '{$email}' AND password = '{$password}'";

  $sql = "SELECT {$column} FROM {$table} WHERE {$whereClause}";
  $rs  = $db->dbGetOne($sql);

  if ( count($rs) == 1 ) {
    $result['success']  = true;
    $result['message']  = '登入成功';
  } else {
    $result['message']  = '登入失敗';
  }

  jsonOutput($result);
}
