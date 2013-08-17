<?php

/**
  keyExists 判斷陣列中是否有此欄位，如果有則回傳內容

  @key  欄位名稱

  @obj  陣列

  return String
 */

function hasKeyExists($key, $obj) {
    $result = null;

    if (array_key_exists($key, $obj)) {
        $result = $obj->$key;
    }

    return $result;
}

/**
  dbEncrypt 加密要存入資料庫之資料

  @data 要加密的資料

  return String
 */
function dbEncrypt($data) {
    return SHA1($data .SYS_DBSALT);
}

/**
  checkValues 判斷陣列所有資料是否有值

  @values     陣列

  return  bool
 */
function checkValues($values) {
    $len = count($values);
    for ($i = 0; $i < $len; $i++) {
        if (!isset($values[$i]) || $values[$i] == null || empty($values[$i])) {
            return false;
        }
    }

    return true;
}

/**
  jsonOutput 回傳json資料並結束

  @result   陣列

  return jsonObject
 */

function outputJSON($result) {
    echo json_encode($result);
    exit();
}
