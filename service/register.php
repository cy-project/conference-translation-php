<?php
require_once(dirname((dirname(__FILE__ ))) .'/common/init.php');

if (isset($_POST['content'])) {
  $obj        = json_decode($_POST['content']);

  $result       = [
    'message' => null,
    'success' => false
  ];

  $mobile_phone = keyExists('mobile_phone', $obj);
  $email        = keyExists('email'       , $obj);
  $password     = keyExists('password'    , $obj);
  $name         = keyExists('name'        , $obj);

  if ( !checkValues([$mobile_phone, $email, $password, $name]) ) {
    $result['message']  = $MSG['empty_data_fails'];
    $result['success']  = false;

    jsonOutput($result);
  }

  $table = SYS_DBNAME . '.' .USER_ACCOUNT;

  $arrayField = [];
  $arrayField = [
    'mobile_phone'  => $mobile_phone,
    'email'         => $email,
    'password'      => $password,
    'name'          => $name
  ];

  $db = new DB();
  $dbResult = $db->dbInsert( $table, $arrayField );

  if ( $dbResult ) {
    $result['message']  = $MSG['add_data_success'];
    $result['success']  = true;
  } else {
    $result['message']  = $MSG['add_data_fails'];
    $result['success']  = false;
  }

  jsonOutput($result);
}
