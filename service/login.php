<?php
require(dirname(__DIR__) .'/common/init.php');

if (isset($_POST['content'])) {

  $result = [
    'message' => null,
    'success' => false
  ];

  try {
    $obj = json_decode($_POST['content']);
  } catch (Exception $e) {
    $result['message'] = '資料格式有誤，無法解析JSON.';
    outputJSON($result);
  }

  $mobile_phone = hasKeyExists('mobile_phone', $obj);

  if (!checkValues([$mobile_phone])) {
    $result['message'] = '資料不得為空';
    outputJSON($result);
  }

  $db = new Database();

  $table        = USER_ACCOUNT;
  $column       = "*";
  $whereClause  = "mobile_phone = '{$mobile_phone}'";

  $sql = "SELECT {$column} FROM {$table} WHERE {$whereClause}";
  $rs  = $db->getOne($sql);

  if (count($rs) == 1) {
    $result['success']  = true;
    $result['message']  = '登入成功';
  } else {
    $result['message']  = '登入失敗';
  }

  outputJSON($result);
}
