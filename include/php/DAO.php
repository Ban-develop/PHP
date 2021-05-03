<?php

    //회원 관련 DB QUERY
    function loginCheck($aData){
        $sQuery = array(
            'table' => DB_USER_TABLE,
            'select' => array(
            ),
            'where' => array(
                'user_id' => $aData['id'],
                'user_pw' => $aData['pw']
            ),
            );

        $rData = execSQL($sQuery);

        return $rData;
    }

    function registUser($aData){
        $sQuery = array(
            'table' => DB_USER_TABLE,
            'insert' => array(
                'user_id' => $aData['id'],
                'user_pw' => $aData['pw'],
                'user_name' => $aData['name'],
                'user_email' => $aData['email'],
                'created_at' => $aData['reg_date'],
            ),
            );

        $rData = execSQL($sQuery);

        return $rData;
    }

    //게시판 관련 DB QUERY
    function lists()
    {
        $sQuery = array(
            'table' => DB_BLOG_TABLE,
            'select' => array(
            ),
        );

        $rData = execSQL($sQuery);

        return $rData;

    }


    function view($aData)
    {
        $sQuery = array(
            'table' => DB_BLOG_TABLE,
            'select' => array(
            ),
            'where' => array(
                'seq' => $aData['seq']
            )
        );

        $rData = execSQL($sQuery);

        return $rData;

    }


    function save($aData){
        $sQuery = array(
            'table' => DB_BLOG_TABLE,
            'insert' => array(
                'subject' => $aData['subject'],
                'contents' => $aData['contents'],
                'reg_name' => $aData['reg_name'],
                'reg_pw' => $aData['reg_pw'],
                'reg_date' => $aData['reg_date'],
            )
        );

        $rData = execSQL($sQuery);
        return $rData;
    }

    function delete($aData){

        $dQuery = "DELETE FROM " . DB_BLOG_TABLE . " WHERE seq = '{$aData['seq']}'";
        $dData = execSqlUpdate($dQuery);

    }

?>