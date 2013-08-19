<?php
require(dirname(__DIR__) .'/common/init.php');

if (isset($_POST['content'])) {
    $obj        = json_decode($_POST['content']);

    $result       = [
        'message' => null,
        'success' => false
    ];

    $mobile_phone = hasKeyExists('mobile_phone', $obj);
    $name         = hasKeyExists('name', $obj);

    if (!checkValues([$mobile_phone])) {
        $result['message']  = $MSG['empty_data_fails'];
        $result['success']  = false;

        outputJSON($result);
    }

    $table = DB_NAME . '.' .USER_ACCOUNT;

    // create account
    $arrayField = [];
    $arrayField = [
        'mobile_phone'  => $mobile_phone,
        'name'          => $name
    ];

    $db = new Database();
    $dbResult = $db->insert($table, $arrayField);

    if ( $dbResult ) {
        $result['message']  = $MSG['add_data_success'];
        $result['success']  = true;
    } else {
        $result['message']  = $MSG['add_data_fails'];
        $result['success']  = false;
    }

    outputJSON($result);
}
