<?php
require_once(__DIR__.'/../../php.lib/function.basic.php');
require_once('_p_permission.php'); # convert page & check permission
function getRealNamePage($type, $page) {
	if(!userPermission($type, $page)) {
		return "_t_not_access.php";
	}else{
		$real_page = pageMapping($page);
		if (strlen($real_page)) return $real_page; else return "_t_error.php";  // not found
	}
}
function menuPermission($menu, $pages) {
	foreach(explode(",", $pages) AS $page) {
		if($menu == $page) {
			echo _MENU_ACTIVE;
			return true;
		}
	}
	return false;
}
function tableFormat($type, $data) {
	if (!isset($data)||!strlen($data)) return "-";
	// type:: 0 = not show; 1 = string; 2 = date time; 3 = number; 4 = money; 5 = mobile phone; 10 = console; 100 = pattern icon; 200 = sum (num); 201 = sum (money)
	switch ($type) {
		case 1:
		case 100:
			return $data;
		break;
		case 2:
			return date("Y-m-d H:i:s",strtotime($data));
		break;
		case 3:
		case 200:
			return number_format($data, 0,"","");
		break;
		case 4:
		case 201:
			return number_format($data, 2,".",",");
		break;
		case 5:
			return convert2ThaiPhoneNumber($data);
		break;
		case 10:
			$high = count(explode("\n",str_replace("\r","",$data)));
			return '<textarea class="form-control rounded-0" style="font-size: 8pt" rows="'.($high>5?$high:5).'" cols="160" disabled>'.$data.'</textarea>';
		break;
	}
}
function convColorStyle($color) {
	global $class_color_code;
	//blue, gray, green, red, yellow, light-blue, light-gray, dark
	
	$style = "style=\"background-color:";
	switch ($color) {
		case "info": return "$style#EEF\"";
		case "active": return "$style#EEE\"";
		case "warning": return "$style#FFE\"";
		case "success": return "$style#EFE\"";
		case "danger": return "$style#FEE\"";
	}//*/
	#return "style=\"background-color:".$class_color_code[$color]."\"";
}
function tableAlign($mode) {
	switch ($mode) {
		case "1": return "text-left";
		case "2": return "text-right";
		default: return "text-center";
	}
}
function tableBgColor($mode=0, $seq, $mark=null, $oper=null, $value=null, $color=null) {
	/*
	mode: 0=white and gray; 1=color; 2=mix
	class="info" <font color="#CFF"> blue
	class="active" <font color="#E7E7E7"> gray
	class="warning" <font color="#FFFFCC"> yellow
	class="success" <font color="#CCFFCC"> green
	class="danger" <font color="#FFCCCC"> red
	//*/
	if (!$seq) return "";
	if ($mode==0) {
		if ($seq%2 == 0) {
			return convColorStyle("active");
		}else{ 
			return "";
		}
	}
	if (strlen($mark)&&strlen($oper)&&strlen($value)&&strlen($color)) { // mode==1
		$marks = explode(",", $mark);
		$colors = explode(",", $color);
		for($i=0; $i<count($marks); $i++) {
			$mark = trim($marks[$i]);
			$color = convColorStyle(trim($colors[$i]));
			if ($color != null) {
				switch($oper) {
					case "<": if ($value < $mark) return $color; break;
					case ">": if ($value > $mark) return $color; break;
					case "<=": if ($value <= $mark) return $color; break;
					case ">=": if ($value >= $mark) return $color; break;
					case "<>": case "!=": if ($value != $mark) return $color; break;
					case "==": 
					default:
						$range = explode("-",$mark);
						if (count($range)>1) {
							if ($value >= trim($range[0]) && $value <= trim($range[1])) return $color;
						}else{
							if ($value == $mark) return $color;
						}
					break;
				}
			}
		}
		//if ($mode==2) {
		//	if ($seq%2 != 0) return convColorStyle("active");
		//}
	}
}
function checkRealLastDay($year, $month, $day) {
	$real_last_date = date("t", strtotime("$year-$month-01 00:01"));
	if ($day > $real_last_date) {
		$day = $real_last_date;
	}
	return sprintf('%02d', $day);
}
function genDateYmd($year, $month, $day) {
	return "$year-".sprintf('%02d', $month)."-".checkRealLastDay($year, $month, $day);
}
function splitItemsToPages($items, $max_rows) {
	if ($items > 0) return ceil($items/$max_rows); else return 0;
}
?>