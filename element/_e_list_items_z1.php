<?php
$php_root_file=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_FILENAME'];
$fields = Array(); $labels_info = Array(); $search_fields = Array(); $hidden_info=Array(); $group_info=Array(); $c = 0;
if (strlen($fields_info[$sort_field][_SQL_FIELD])>0) {
	if ($sort_desc_field) $orderby = getFieldName($fields_info[$sort_field][_SQL_FIELD], true)." DESC"; else $orderby = getFieldName($fields_info[$sort_field][_SQL_FIELD], true);
}
foreach($fields_info AS $field) {
	if ($field[_TYP_FIELD] >= 0 && $field[_TYP_FIELD] < 200) $group_info[] = $field[_SQL_FIELD];
	if ($field[_TYP_FIELD] >= 0) $fields[] = $field[_SQL_FIELD];
	if ($field[_TYP_FIELD] == 0) $hidden_info[] = $c;
	if ($field[_TYP_FIELD] > 0) $labels_info[] = $c;
	if ($field[_SEA_FIELD]) $search_fields[] = $c;
	$fields_info[$c][_ID_FIELD] = $c;
	if (strlen($orderby) == 0) {
		switch ($fields_info[$c][_SORT_FIELD]) {
			case 1: $orderby = getFieldName($fields_info[$c][_SQL_FIELD], true); break;
			case 2: $orderby = getFieldName($fields_info[$c][_SQL_FIELD], true)." DESC"; break;
		}
	}
	$c++;
}
if (strlen($orderby)==0) $orderby = $fields_info[0][_SQL_FIELD]; // default order by 1

$sql_fields = implode(",", $fields);
$sql_group = implode(",", $group_info);

$sql_text_search = "";
$sql_text_sender = "";
$sql_text_sumrpt = "";

// sender search
if($username != '')
{
	$sql_text_sender .= " AND SENDER_Username = '".$username."' ";
}
if($sender != '')
{
	$sql_text_sender .= " AND SENDER_Sender = '".$sender."' ";
}

// summary search
if($user_sumrpt != '')
{
	$sql_text_sumrpt .= " AND SUMRPT_Username = '".$user_sumrpt."' ";
}
if($sender_sumrpt != '')
{
	$sql_text_sumrpt .= " AND SUMRPT_Sender = '".$sender_sumrpt."' ";
}

if (strlen($text_search)>0) {
	foreach(explode(" ", $text_search." ") AS $word) {
		if (!filterWithNormalChar($word)) continue;
		$word = trim($word);
		if (strlen($word)>0) {
			$groupAnds = explode("++", $word);
			if (count($groupAnds) == 1) {
				$search_by_label = explode("|",$word);
				if ((strlen($search_by_label[0])>0)&&(strlen($search_by_label[1])>0)) {
					if ($search_by_label[0] >= count($fields_info)) continue;
					$id = $search_by_label[0];
					$word = $search_by_label[1];
					if (validateNumber($word)) {
						if (strlen($word)==10 && substr($word,0,1) == "0") $word = convert2ThaiPhoneNumber($word, 0);
					}
					$type = $fields_info[$id][_TYP_FIELD];
					if (!$type) {
						if ($fields_info[$id][_COM_TYP_FIELD]) $type = $fields_info[$id][_COM_TYP_FIELD];
					}
					// type:: 0 = not show; 1 = string; 2 = date time; 3 = number; 4 = money; 5 = mobile phone; 100 = pattern icon; 200 = sum (num); 201 = sum (money)
					switch($type) {
						case 1:
							$sql_text_search .= " OR LOWER(".getFieldName($fields_info[$id][_SQL_FIELD], true).") LIKE LOWER('%$word%' collate Thai_BIN) "; // need to use log field inside WHERE condition
						break;
						case 3:
						case 4:
							if (validateNumber($word, 1) && ((int)$word < 0xFFFFFFFF)) {
								$sql_text_search .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." = '$word' ";
							}else{
								$sql_text_search .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." = -1 ";
							}
						break;
						case 5:
							if (validateNumber($word)) {
								$sql_text_search .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." = '$word' ";
							}else{
								$sql_text_search .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." = -1 ";
							}
						break;
						case 10:
							$sql_text_search .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." LIKE '%$word%' ";
						break;
					}
				}else{
					if (validateNumber($word)) {
						if (strlen($word)==10 && substr($word,0,1) == "0") $word = convert2ThaiPhoneNumber($word, 0);
					}
					foreach($search_fields AS $search) {
						$type = $fields_info[$search][_TYP_FIELD];
						if (!$type) {
							if ($fields_info[$search][_COM_TYP_FIELD]) $type = $fields_info[$search][_COM_TYP_FIELD];
						}
						switch($type) {
							case 1:
								$sql_text_search .= " OR LOWER(".getFieldName($fields_info[$search][_SQL_FIELD], true).") LIKE LOWER('%$word%' collate Thai_BIN) "; // need to use log field inside WHERE condition
							break;
							case 3:
							case 4:
								if (validateNumber($word, 1) && ((int)$word < 0xFFFFFFFF)) {
									$sql_text_search .= " OR ".getFieldName($fields_info[$search][_SQL_FIELD], true)." = '$word' ";
								}else{
									$sql_text_search .= " OR ".getFieldName($fields_info[$search][_SQL_FIELD], true)." = -2 ";
								}
							break;
							case 5:
								if (validateNumber($word)) {
									$sql_text_search .= " OR ".getFieldName($fields_info[$search][_SQL_FIELD], true)." = '$word' ";
								}else{
									$sql_text_search .= " OR ".getFieldName($fields_info[$search][_SQL_FIELD], true)." = -1 ";
								}
							break;
							case 10:
								$sql_text_search .= " OR ".getFieldName($fields_info[$search][_SQL_FIELD], true)." LIKE '%$word%' ";
							break;
						}
					}
				}
			}else{
				$sql_text_search_and = ""; // clear
				foreach($groupAnds AS $word) {
					$search_by_label = explode("|",$word);
					if ((strlen($search_by_label[0])>0)&&(strlen($search_by_label[1])>0)) {
						if ($search_by_label[0] >= count($fields_info)) continue;
						$id = $search_by_label[0];
						$word = $search_by_label[1];
						if (validateNumber($word)) {
							if (strlen($word)==10 && substr($word,0,1) == "0") $word = convert2ThaiPhoneNumber($word, 0);
						}
						$type = $fields_info[$id][_TYP_FIELD];
						if (!$type) {
							if ($fields_info[$id][_COM_TYP_FIELD]) $type = $fields_info[$id][_COM_TYP_FIELD];
						}
						// type:: 0 = not show; 1 = string; 2 = date time; 3 = number; 4 = money
						switch($type) {
							case 1:
								$sql_text_search_and .= " AND LOWER(".getFieldName($fields_info[$id][_SQL_FIELD], true).") LIKE LOWER('%$word%' collate Thai_BIN) "; // need to use log field inside WHERE condition
							break;
							case 3:
							case 4:
								if (validateNumber($word, 1)) {
									$sql_text_search_and .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." = '$word' ";
								}else{
									$sql_text_search_and .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." = -3 ";
								}
							break;
							case 5:
								if (validateNumber($word)) {
									$sql_text_search .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." = '$word' ";
								}else{
									$sql_text_search .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." = -1 ";
								}
							break;
							case 10:
								$sql_text_search .= " OR ".getFieldName($fields_info[$id][_SQL_FIELD], true)." LIKE '%$word%' ";
							break;
						}
					}else{
						if (validateNumber($word)) {
							if (strlen($word)==10 && substr($word,0,1) == "0") $word = convert2ThaiPhoneNumber($word, 0);
						}
						foreach($search_fields AS $search) {
							$type = $fields_info[$search][_TYP_FIELD];
							if (!$type) {
								if ($fields_info[$search][_COM_TYP_FIELD]) $type = $fields_info[$search][_COM_TYP_FIELD];
							}
							switch($type) {
								case 1:
									$sql_text_search_and .= " AND LOWER(".getFieldName($fields_info[$search][_SQL_FIELD], true).") LIKE LOWER('%$word%' collate Thai_BIN) "; // need to use log field inside WHERE condition
								break;
								case 3:
								case 4:
									if (validateNumber($word, 1)) {
										$sql_text_search_and .= " OR ".getFieldName($fields_info[$search][_SQL_FIELD], true)." = '$word' ";
									}else{
										$sql_text_search_and .= " OR ".getFieldName($fields_info[$search][_SQL_FIELD], true)." = -4 ";
									}
								break;
								case 10:
									$sql_text_search .= " OR ".getFieldName($fields_info[$search][_SQL_FIELD], true)." LIKE '%$word%' ";
								break;
							}
						}
					}
				}
				if (strlen($sql_text_search_and) > 0) $sql_text_search_and = substr($sql_text_search_and, 5); // delete first AND
				if (strlen($sql_text_search_and) > 0) $sql_text_search .= " OR ($sql_text_search_and) ";
			}
		}
	}
	// if (strlen($sql_text_search) > 0) $sql_text_search = substr($sql_text_search, 4); // delete first OR
}

if (strlen($sql_text_sender) > 0) $sql_text_sender = substr($sql_text_sender, 5); // delete first AND
if (strlen($sql_text_sumrpt) > 0) $sql_text_sumrpt = substr($sql_text_sumrpt, 5); // delete first AND
if (strlen($sql_text_search) > 0) $sql_text_search = substr($sql_text_search, 4); // delete first OR

if (strlen($sql_text_search)>0) {
	if (strlen($sql_where)>0) { 
		$sql_where = "$sql_where AND ($sql_text_search)";
	}else{
		$sql_where = "($sql_text_search)";
	}
}else{
	if (strlen($text_search)>0) { // make search fail
		if (strlen($sql_where)>0) { 
			$sql_where = "(1 = 2) AND $sql_where";
		}else{
			$sql_where = "1 = 2";
		}
	}
}

// -------------------------- sender sql search -------------------------
if (strlen($sql_text_sender)>0) {
	if (strlen($sql_text_search)>0) {
		$sql_where = "$sql_where AND ($sql_text_sender) AND ($sql_text_search)";
	} else {
		$sql_where = "$sql_where AND ($sql_text_sender)";
	}
} else if(strlen($sql_text_search)>0){
	if (strlen($sql_text_sender)>0) {		
		$sql_where = "$sql_where AND ($sql_text_search) AND ($sql_text_sender)";
	} else {
		$sql_where = "$sql_where AND ($sql_text_search)";
	}
} else {
	if (strlen($text_search)>0) { // make search fail
		if (strlen($sql_where)>0) { 
			$sql_where = "(1 = 2) AND $sql_where";
		}else{
			$sql_where = "1 = 2";
		}
	}
}

// -------------------------- summary sql search -------------------------
if (strlen($sql_text_sumrpt)>0) {
	if (strlen($sql_text_search)>0) {
		$sql_where = "$sql_where AND ($sql_text_sumrpt) AND ($sql_text_search)";
	} else {
		$sql_where = "$sql_where AND ($sql_text_sumrpt)";
	}
} else if(strlen($sql_text_search)>0){
	if (strlen($sql_text_sumrpt)>0) {		
		$sql_where = "$sql_where AND ($sql_text_search) AND ($sql_text_sumrpt)";
	} else {
		$sql_where = "$sql_where AND ($sql_text_search)";
	}
} else {
	if (strlen($text_search)>0) { // make search fail
		if (strlen($sql_where)>0) { 
			$sql_where = "(1 = 2) AND $sql_where";
		}else{
			$sql_where = "1 = 2";
		}
	}
}

if (count($fields) > count($group_info)) {
	if (strlen($sql_where)>0) { 
		$sql_from_where = "FROM $sql_from
			WHERE $sql_where
			GROUP BY $sql_group";
	}else{
		$sql_from_where = "FROM $sql_from
			GROUP BY $sql_group";
	}
}else{
	if (strlen($sql_where)>0) { 
		$sql_from_where = "FROM $sql_from
			WHERE $sql_where";
	}else{
		$sql_from_where = "FROM $sql_from";
	}
}

$sql_fields_from_where = "$sql_fields
	$sql_from_where";
$data_count = 0;
echo $sql_from_where;
// echo $sql_fields_from_where;
?>