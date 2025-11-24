<?php
require_once("_1_defines.php");
function pageMapping($page) {
	switch($page) {
		case "first":
			return "_t_first.php";
		case "manage_cus":
			return "_t_manage_cus.php";
		case "sub_user":
			return "_t_sub_user.php";
		case "by_contacts":
			return "_t_by_contacts.php";
		case "contacts":
			return "_t_contacts.php";
		case "by_file":
			return "_t_file.php";
		case "scheduled":
			return "_t_scheduled.php";
		case "scheduled_report":
			return "_t_scheduled_report.php";
		case "report":
			return "_t_report.php";
		case "report_admin":
			return "_t_report_admin.php";
		case "generate_csv":
			return "_s_generate_csv.php";
		case "sender":
			return "_t_sender.php";
		case "blacklist":
			return "_t_blacklist.php";
		case "sum_report":
			return "_t_summary_report.php";
		case "sender_list":
			return "_t_sender_list.php";
		case "keyword":
			return "_t_keyword.php";
		case "keyword_list":
			return "_t_keyword_list.php";
		case "department":
			return "_t_department.php";
		case "department_list":
			return "_t_department_list.php";
	}
}
/*
define('_SES_USR_ROT',0);
define('_SES_USR_SA',1);
define('_SES_USR_ADM',2);
define('_SES_USR_SYS_ADMIN',5);
define('_SES_USR_SYS_USER_C',6);
define('_SES_USR_SYS_USER_O',7);
define('_SES_USR_FIN',10);
define('_SES_USR_MKT',11);
define('_SES_USR_OPE',12);
define('_SES_USR_CCC',13);
define('_SES_USR_AGT',50);
define('_SES_USR_OFF',100);
define('_SES_USR_SACP',200);
define('_SES_USR_CP',201);
define('_SES_USR_USER',202);
//*/
function userPermission($type,$page) {
	switch($type) {
		case _SES_USR_ROT:
			return true;
		case _SES_USR_CP:
		case _SES_USR_USER:
			switch($page) {
				case "first":
				case "manage_cus":
				case "by_contacts":
				case "contacts":
				case "by_file":
				case "scheduled":	
				case "scheduled_report":
				case "report":
				case "report_admin":
				case "generate_csv":
				case "sender":
				case "blacklist":
				case "sender_list":
				case "keyword":
				case "keyword_list":
				case "department":
					return true;
			}
		break;
		default: return false;
	}
}
?>