<?php
    session_start();
    include_once dirname(__FILE__) .'/include/php/default.php';
    $IData = array(
        'id' => $_REQUEST['id'],
        'pw' => md5($_REQUEST['pw']),
    );

    $rData = loginCheck($IData);

    if($rData){
        echo "<script>alert('".($rData[0]['user_name'])."님 반갑습니다.');</script>";
        $_SESSION['userid'] = $rData[0]['user_idx'];
        echo "<script>location.replace('list.html');</script>";
        exit;
    }
    else{
        echo "<script>alert('로그인 정보를 확인해주세요.');</script>";
        echo "<script>history.back();</script>";
        exit;
    }

?>