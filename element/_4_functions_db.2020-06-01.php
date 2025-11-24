<?php
require_once("/var/php.lib/function.basic.db.php");
if (strlen($dbh_error)) {
	echoMsg(__FILE__, __FUNCTION__, "database", "dbh_error :: $dbh_error", 255);
	exit(0);
}

function getRows($dbh, $sql_from_where, $orderby) {
	$sql = "SELECT COUNT(*) AS row_count 
FROM (
	SELECT ROW_NUMBER() OVER(ORDER BY $orderby) AS RowID
	$sql_from_where
) AS counting";
	//return querySqlSingleFieldEx($sql);
	$stmt = querySqlEx($sql);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return $row['row_count'];
}
function selectByPage($dbh, $sql_fields_from_where, $orderby, $page, $max_rows) {
	$rowStart = ($page-1) * $max_rows; // start from zero
	$rowStop = $page * $max_rows;
	$sql = "SELECT paging.* 
FROM (
	SELECT ROW_NUMBER() OVER(ORDER BY $orderby) AS RowID
		,$sql_fields_from_where
	) AS paging 
WHERE paging.RowID > $rowStart AND paging.RowID <= $rowStop
ORDER BY RowID";
	return querySqlEx($sql);
}
function getFieldName ($field_name, $long=false) {
	$fields = explode(" AS ", $field_name);
	$count_as = count($fields);
	if ($count_as > 1) {
		if ($long) {
			return trim($fields[0]);
		} else {
			return trim(str_replace(Array("[","]"),"", $fields[$count_as-1]));
		}
	}else{
		if ($long) {
			return trim($field_name);
		} else {
			$last_field = explode(".", $field_name);
			return trim(str_replace(Array("[","]"),"", $last_field[count($last_field)-1]));
		}
	}
}
function filterWithNormalChar ($str) {
    /*
	return preg_match('/[^A-Za-z0-9.#\\-$]/', $str);
	return !preg_match('/[\'^?$%&*()}{@#~?><>,|=_+?-]/', $str);
	//*/
	return !preg_match('/[\'^?$%&()}{~?><>,=?]/', $str); // ignore _ and - and +
}
?>