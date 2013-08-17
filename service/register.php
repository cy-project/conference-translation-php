<?php
require(dirname(__DIR__) .'/common/init.php');

if (isset($_POST['content'])) {
  $obj        = json_decode($_POST['content']);

  $result       = [
    'message' => null,
    'success' => false
  ];

  $mobile_phone = hasKeyExists('mobile_phone', $obj);
  $email        = hasKeyExists('email'       , $obj);
  $password     = hasKeyExists('password'    , $obj);
  $name         = hasKeyExists('name'        , $obj);

  if (!checkValues([$mobile_phone, $email, $password, $name])) {
    $result['message']  = $MSG['empty_data_fails'];
    $result['success']  = false;

    outputJSON($result);
  }

  $table = SYS_DBNAME . '.' .USER_ACCOUNT;
  
  // create account
  $arrayField = [];
  $arrayField = [
    'mobile_phone'  => $mobile_phone,
    'email'         => $email,
    'password'      => $password,
    'name'          => $name
  ];

  $db = new database();
  $dbResult = $db->insert( $table, $arrayField );

  if ( $dbResult ) {
    $result['message']  = $MSG['add_data_success'];
    $result['success']  = true;
  } else {
    $result['message']  = $MSG['add_data_fails'];
    $result['success']  = false;
  }

  outputJSON($result);
}
