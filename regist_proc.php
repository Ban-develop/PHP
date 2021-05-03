<?php
    session_start();
    include_once dirname(__FILE__) .'/include/php/default.php';
    $IData = array(
        'id' => $_REQUEST['id'],
        'pw' => md5($_REQUEST['pw']),
        'name' => $_REQUEST['name'],
        'email' => $_REQUEST['mail'],
        'reg_date' => date("Y-m-d H:i:s")
    );

    
    $rData = registUser($IData);

    if($rData){
        echo "<script>alert('로그인 후 서비스를 이용하세요.');</script>";
        echo "<script>location.replace('login.html');</script>";
        exit;
    }
    else{
        echo "<script>alert(' 정보를 확인해주세요.');</script>";
        exit;
    }

?>