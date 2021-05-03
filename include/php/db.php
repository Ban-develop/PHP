<?PHP
$oConnection;
$oConnectionINTRA;
function dbConnectNew() {
    global $oConnection;

    $oConnection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

    if ($oConnection) {
        mysqli_select_db ($oConnection, DB_NAME);
    } else {
        error_log(mysqli_error($oConnection));
    }
    mysqli_set_charset($oConnection, "utf8");
}

function execSqlOneRow($sQuery) {
    global $oConnection;
    if(!$oConnection) dbConnectNew();
    //	mysqli_set_charset($oConnection, "utf8");
    if ($oConnection) {
        $oResult = mysqli_query($oConnection, $sQuery);
        if ($oResult) {
            $aRow = mysqli_fetch_assoc($oResult);
            $oResult->close();
        } else {
            $aRow = false;
        }
    } else {
        return false;
    }
    return $aRow;
}

function execSqlOneCol($sQuery) {
    global $oConnection;
    if(!$oConnection) dbConnectNew();
    //	mysqli_set_charset($oConnection, "utf8");
    if ($oConnection) {
        $oResult = mysqli_query($oConnection, $sQuery);

        if($oResult){
            // Cycle through results
            $aRow = $oResult->fetch_array();

            // Free result set
            $oResult->close();
        } else {
            $aRow = false;
        }

//		if ($oResult) {
//			$aRow = mysql_fetch_row($oResult);
//			return $aRow[0];
//		}
    } else {
        return false;
    }
    return $aRow;
}

function execSqlLists($sQuery) {
    global $oConnection;
    if(!$oConnection) dbConnectNew();
    //	mysqli_set_charset($oConnection, "utf8");
    if ($oConnection) {
        $aReturn = array();
        $oResult = mysqli_query($oConnection, $sQuery);
        if ($oResult) {
            $nI = 0;
            while ($aRow = mysqli_fetch_assoc($oResult)) {
                array_push($aReturn,$aRow);
                //$aReturn[$nI] = $aRow;
                //$nI++;
            }
        } else {
            $aReturn = false;
        }
    } else {
        return false;
    }
    return $aReturn;
}

function execSqlUpdate($sQuery, $bReturnResult = false) {
    global $oConnection;
    if(!$oConnection) dbConnectNew();
    //	mysqli_set_charset($oConnection, "utf8");
    if ($oConnection) {
        $aReturn = "";
        //	$oResult = mysqli_query($sQuery);
        $oResult = mysqli_query($oConnection, $sQuery);
        if ($oResult) {
            if ($bReturnResult===true) {
                $aReturn = mysqli_insert_id($oConnection);
                //	return mysql_query("SELECT LAST_INSERT_ID()");
            } else {
                $aReturn = true;
            }
        } else {
            $aReturn = false;
        }
    } else {
        $aReturn = false;
    }
    return $aReturn;
}
/**
 * insertSQL을 만들어서 삽입
 *
 * @param	Stirng DB명
 * @param	Array 삽입할 key와 값들
 */
function BuildInsertSQL(&$aQuery) {
    global $oConnection;
    if(!$oConnection) dbConnectNew();
    //	$fields = array_map('mysqli_real_escape_string', array_values($aQuery['insert']));
    $fields = array_map(array($oConnection, 'real_escape_string'), array_values($aQuery['insert']));
    $keys = array_keys($aQuery['insert']);
    $table = $aQuery['table'];
    $sQuery = 'INSERT INTO `' . $table . '` (`' . implode('`,`', $keys) . '`) VALUES (\'' . implode('\',\'', $fields) . '\')';
    return $sQuery;
}

function BuildUpdateSQL(&$aQuery) {
    global $oConnection;
    if(!$oConnection) dbConnectNew();
    $fields = $aQuery['update'];
    $wheres = $aQuery['where'];
    $table = $aQuery['table'];
    $keys = array();
    $wehreKeys = array();

    foreach ($fields as $key => $value) {
        array_push($keys, '`' . $key . '`=\'' . mysqli_real_escape_string($oConnection, $value) . '\'');
    }
    foreach ($wheres as $key => $value) {
        array_push($wehreKeys, '`' . $key . '`=\'' . mysqli_real_escape_string($oConnection, $value) . '\'');
    }

    $sQuery = 'UPDATE `' . $table . '` SET ' . implode(',', $keys) . ' WHERE (' . implode(') AND (', $wehreKeys) . ')';
    return $sQuery;
}

function BuildSelectSQL(&$aQuery) {
    global $oConnection;
    if(!$oConnection) dbConnectNew();
    $query = 'SELECT ';
    if (count($aQuery['select']) == 0) {
        $query .= '*';
    } else {
        $query .= '`' . implode('`,`', $aQuery['select']) . '`';
    }
    $query .= ' FROM `' . $aQuery['table'] . '`';
    if (isset($aQuery['where'])) {
        if (count($aQuery['where']) != 0) {
            $wehreKeys = array();
            foreach ($aQuery['where'] as $key => $value) {
                array_push($wehreKeys, '`' . $key . '`=\'' . mysqli_real_escape_string($oConnection, $value) . '\'');
            }
            $query .= ' WHERE (' . implode(') AND (', $wehreKeys) . ')';
        }
    }
    if (isset($aQuery['order'])) {
        if (count($aQuery['order']) != 0) {
            $orderKeys = array();
            foreach ($aQuery['order'] as $key => $value) {
                array_push($orderKeys, '`' . $key . '` ' . mysqli_real_escape_string($oConnection, $value));
            }
            $query .= ' ORDER BY ' . implode(', ', $orderKeys);
        }
    }
    if (isset($aQuery['limit'])) {
        if ($aQuery['limit']) {
            $query .= ' LIMIT ' . $aQuery['limit'];
        }
    }
//    error_log($query);
    return $query;
}

function execSQL($aQuery) {
    if (!is_array($aQuery)) {
        return false;
    }
    if (isset($aQuery['insert']) && isset($aQuery['table']) ) {
        //	$oConnection = dbConnectNew();
        $sQuery = BuildInsertSQL($aQuery);
        return execSqlUpdate($sQuery, @$aQuery['bReturnResult']);
    }
    if (isset($aQuery['update']) && isset($aQuery['table']) && isset($aQuery['where'])) {
        //	$oConnection = dbConnect();
        $sQuery = BuildUpdateSQL($aQuery);
        return execSqlUpdate($sQuery);
    }
    if (isset($aQuery['select']) && isset($aQuery['table']) ) {
        //	$oConnection = dbConnect();
        $sQuery = BuildSelectSQL($aQuery);
        return execSqlLists($sQuery);
    }

    return false;
}

function dbConnectClose() {
    global $oConnection;
    mysqli_close($oConnection);
    unset($oConnection);
}




function dbConnectIntraDB() {
    global $oConnectionINTRA;

    $oConnectionINTRA = mysqli_connect(DB_HOST_INTRADB, DB_USER_INTRADB, DB_PASS_INTRADB);
    if ($oConnectionINTRA) {
        mysqli_select_db ($oConnectionINTRA, DB_NAME_INTRADB);
    } else {
        error_log(mysqli_error($oConnectionINTRA));
    }
    mysqli_set_charset($oConnectionINTRA, "utf8");
    //	return $oConnectionINTRA;
}

function execSqlOneColIntraDB($sQuery) {
    global $oConnectionINTRA;
    if(!$oConnectionINTRA) dbConnectIntraDB();

    //	mysqli_set_charset($oConnectionINTRA, "utf8");
    if ($oConnectionINTRA) {
        $oResult = mysqli_query($oConnectionINTRA, $sQuery);

        if($oResult){
            // Cycle through results
            $aRow = $oResult->fetch_object();

            // Free result set
            $oResult->close();
            return $aRow;
        }
    }
    return false;
}

function execSqlOneRowIntraDB($sQuery) {
    global $oConnectionINTRA;
    if(!$oConnectionINTRA) dbConnectIntraDB();

    //	mysqli_set_charset($oConnectionINTRA, "utf8");
    if ($oConnectionINTRA) {
        $oResult = mysqli_query($oConnectionINTRA, $sQuery);
        if ($oResult) {
            return mysqli_fetch_assoc($oResult);
        }
    }
    return false;
}
?>