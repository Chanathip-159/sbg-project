<?php 
# ทดสอบภาษาไทย

// start setup year to search bar with only exist data
#$sql_year_list = "SELECT DISTINCT YEAR(SerialNo_ExecDatetime) as yr FROM [ISAG].[dbo].[SerialNumbers]";
#if ($stmt = querySql($sql_year_list)) {
#	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
#		$year_list[] = $row['yr'];
#	}
#}
// end setup year to search bar with only exist data

// start set year by get last 5 year
#for($i=date("Y")-5; $i<=date("Y"); $i++) {
#	$year_list[] = $i;
#}
// end set year by get last 5 year

# select search bar file
$search_bar = "_e_search_bar.php";

// set $year_list=null use for hide search bar
$year_list=null;
$group_contact_mode = true;
/*
if ($year_list!==null) {
	// setup date range for search if $year_list is ok
	if (strlen($_POST[_STA_YEAR]) > 0) $start_year = $_POST[_STA_YEAR]; else $start_year=date('Y');
	if (strlen($_POST[_STA_MONTH]) > 0) $start_month = $_POST[_STA_MONTH]; else $start_month=date('m');
	if (strlen($_POST[_STA_DAY]) > 0) $start_day = checkRealLastDay($start_year, $start_month, $_POST[_STA_DAY]); else $start_day=1;
	if (strlen($_POST[_END_YEAR]) > 0) $end_year = $_POST[_END_YEAR]; else $end_year=date('Y');
	if (strlen($_POST[_END_MONTH]) > 0) $end_month = $_POST[_END_MONTH]; else $end_month=date('m');
	if (strlen($_POST[_END_DAY]) > 0) $end_day = checkRealLastDay($end_year, $end_month, $_POST[_END_DAY]); else $end_day=date('t');

	#$sql_range_time = "BETWEEN '".genDateYmd($start_year, $start_month, $start_day)." 00:00:00.000' AND '".genDateYmd($end_year, $end_month, $end_day)." 23:59:59.999'";
}
//*/
if (strlen($_POST[_TXT_SEARCH])) $text_search = $_POST[_TXT_SEARCH]; else $text_search = null;
if (strlen($_POST[_SEL_GROUP]) && validateNumber($_POST[_SEL_GROUP])) {
	$group_select_id = $_POST[_SEL_GROUP]; 
	$select_group = "AND [GROUPINFO_Id] = $group_select_id"; 
}else{
	$select_group = null;
}

#$href_fields[0] = "<a href=\"#myModal\" title=\"Edit\" data-toggle=\"modal\" id=\"_SID_\" data-target=\"#editAccModal\">_R_</a>"; // T = transaction
//$pattern_icons['_ICON_1_'] = "<a href=\""._MAIN_DF."account_edit&Sid=_SID_1_\" title=\"Edit\"><i class=\"fa fa-fw fa-file-alt\"></i></a>";
$pattern_icons['_ICON_1_'] = "<a href=\"#myModal\" title=\"Edit\" data-toggle=\"modal\" id=\"_SID_1_-_SID_2_\" data-target=\"#editGroConModal\"><i class=\"fa fa-fw fa-edit\"></i></a> 
<a href=\"#myModal\" title=\"Edit\" data-toggle=\"modal\" id=\"del-_SID_1_-_SID_2_\" data-target=\"#editGroConModal\"><i class=\"fa fa-fw fa-trash\"></i></a>";

$sql_from = "[ITOP].[dbo].[GROUPINFO], [ITOP].[dbo].[GROUPMAP]";
$sql_where = "[GROUPINFO_Id] = [GROUPMAP_Id] AND [GROUPINFO_Msisdn] = '".$_SESSION[_SES_USR_NAME]."'$select_group";
#$orderby = "[ACC_Msisdn] ASC";
$table_mode = 2;// 0=white and gray; 1=color; 2=mix

#$table_name = "Contacts";
#$save_enable = true;
#$table_define = "<div>Master:<i class=\"fa fa-fw fa-university fa-fw \"></i>, Member:<i class=\"fa fa-fw fa-briefcase fa-fw \"></i>, Individual:<i class=\"fa fa-fw fa-archive fa-fw\"></i>, End-User:<i class=\"fa fa-fw fa-user fa-fw\"></i></div>";
$text_search_width = "15";

// create table configuration array
// _TYP_FIELD:: -1 = icon and not query; 0 = not show; 1 = string; 2 = date time; 3 = number; 4 = money; 100 = pattern icon or no search; 200 = sum/count (num); 201 = sum/count (money)
$fields_info = Array(
	Array(_SQL_FIELD => "GROUPMAP_Msisdn"
		, _LAB_FIELD=>"Phone No."
		, _TYP_FIELD=>1
		, _SEA_FIELD=>true
		, _SORT_FIELD=>1) // default sort by: 1 = sort ase, 2 = desc
	,Array(_SQL_FIELD => "GROUPMAP_Name"
		, _LAB_FIELD=>"Name"
		, _TYP_FIELD=>1
		, _SEA_FIELD=>true)
	,Array(_SQL_FIELD => "GROUPINFO_Name"
		, _LAB_FIELD=>"Group name"
		, _TYP_FIELD=>1
		, _SEA_FIELD=>true)
	,Array(_SQL_FIELD => "GROUPMAP_Description"
		, _LAB_FIELD=>"Description"
		, _TYP_FIELD=>1
		, _SEA_FIELD=>true)
	, Array(_SQL_FIELD => "'_ICON_1_' AS icon1"
		, _LAB_FIELD=>"Edit"
		, _TYP_FIELD=>100
		, _RID_FIELD=>'GROUPMAP_Id,GROUPMAP_Msisdn' // field id to put in _SID_1_ to _SID_3_ format Service_ID,Provider_ID
		, _ICO_FIELD=>$pattern_icons['_ICON_1_']
		, _SORT_BOC_FIELD => true)
	,Array(_SQL_FIELD => "GROUPMAP_Id"
		, _TYP_FIELD=>0
		, _SORT_BOC_FIELD=>true)
);
############################################
require_once("element/_e_list_items_z1.php");
if (!$dbh) {
?>
			<!-- Page Content -->
			<h1>Error</h1>
			<hr>
			<p>This is an internal error.</p>
<?php
}else{
	require_once("element/_e_list_items_z2.php");
	require_once("element/_p_dialog_add_group_contact.php");
	//require_once("element/_e_dialog_add_group_contact_csv.php");
	require_once("element/_p_dialog_edit_group_contact.php");
}
?>
