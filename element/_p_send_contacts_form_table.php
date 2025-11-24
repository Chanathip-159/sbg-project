<?php 
# ทดสอบภาษาไทย

# select search bar file
$search_bar = "_e_search_bar.php";

// set $year_list=null use for hide search bar
$year_list=null;
$group_contact_mode = true;

if (strlen($_POST[_TXT_SEARCH])) $text_search = $_POST[_TXT_SEARCH]; else $text_search = null;
if (strlen($_POST[_SEL_GROUP]) && validateNumber($_POST[_SEL_GROUP])) {
	$group_select_id = $_POST[_SEL_GROUP]; 
	$select_group = "AND [GROUPINFO_Id] = $group_select_id"; 
}else{
	$select_group = null;
}

#$href_fields[0] = "<a href=\"#myModal\" title=\"Edit\" data-toggle=\"modal\" id=\"_SID_\" data-target=\"#editAccModal\">_R_</a>"; // T = transaction
//$pattern_icons['_ICON_1_'] = "<a href=\""._MAIN_DF."account_edit&Sid=_SID_1_\" title=\"Edit\"><i class=\"fa fa-fw fa-file-alt\"></i></a>";
$pattern_icons['_ICON_1_'] = "<a href=\"#\" onclick=\"addTopupNumberToTopupList('_SID_1_')\"><i class=\"fa fa-fw fa-plus-circle\"></i></a>";

$sql_from = "[SBG].[dbo].[GROUPS]";
$sql_where = "[GROUP_Username] = '".$_SESSION[_SES_USR_NAME]."'";
$orderby = "[GROUP_Name] ASC";
$table_mode = 2;// 0=white and gray; 1=color; 2=mix

#$table_name = "Contacts";
#$save_enable = true;
#$table_define = "<div>Master:<i class=\"fa fa-fw fa-university fa-fw \"></i>, Member:<i class=\"fa fa-fw fa-briefcase fa-fw \"></i>, Individual:<i class=\"fa fa-fw fa-archive fa-fw\"></i>, End-User:<i class=\"fa fa-fw fa-user fa-fw\"></i></div>";
$text_search_width = "15";

// create table configuration array
// _TYP_FIELD:: -1 = icon and not query; 0 = not show; 1 = string; 2 = date time; 3 = number; 4 = money; 100 = pattern icon or no search; 200 = sum/count (num); 201 = sum/count (money)
$fields_info = Array(
	Array(_SQL_FIELD => "GROUP_Id"
		, _LAB_FIELD=>"Group ID"
		, _TYP_FIELD=>1
		, _SEA_FIELD=>true
		, _SORT_FIELD=>1) // default sort by: 1 = sort ase, 2 = desc
	,Array(_SQL_FIELD => "GROUP_Name"
		, _LAB_FIELD=>"Name"
		, _TYP_FIELD=>1
		, _SEA_FIELD=>true)
	,Array(_SQL_FIELD => "GROUP_Description"
		, _LAB_FIELD=>"Description"
		, _TYP_FIELD=>1
		, _SEA_FIELD=>true)
	, Array(_SQL_FIELD => "'_ICON_1_' AS icon1"
		, _LAB_FIELD=>"Add"
		, _TYP_FIELD=>100
		, _RID_FIELD=>'GROUP_Id' // field id to put in _SID_1_ to _SID_3_ format Service_ID,Provider_ID
		, _ICO_FIELD=>$pattern_icons['_ICON_1_']
		, _SORT_BOC_FIELD => true)
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
}
?>
