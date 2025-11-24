<?php
# ทดสอบภาษาไทย
define('_CEO', false);
define('_DEB', true);
define('_MAX_NUM_ROWS', 200);

define('_DEST_FILE', "DF");
define('_MAX_ROWS', "max_rows");
define('_SEL_PAGE', "select_page");
define('_POST_BYPASS', "bypass");

define('_SES_CUR_PAGE', "session_current_page");
define('_SES_PROVI_ID', "session_provider_id");
define('_SES_SERVI_ID', "session_service_id");
define('_SES_MODE_TYP', "session_mode_type");
define('_SES_PAGE_NUM', "session_page_number");

define('_SES_USR_DIS_NAME', "session_user_display_name");
define('_SES_USR_NAME', "session_user_name");
define('_SES_USR_TYP_NAME', "session_user_type_name");
define('_SES_USR_TYP_ID', "session_user_type_id");

define('_SES_SQL_CUR_QRY_ROW', "session_sql_current_query_rows");
define('_SES_SQL_CUR_QRY_SEL', "session_sql_current_query_select");
define('_SES_SQL_CUR_QRY_ODR', "session_sql_current_query_order");

define('_SES_USR_ROT', 0);
define('_SES_USR_SA', 1);
define('_SES_USR_ADM', 2);
define('_SES_USR_SYS_ADMIN', 5);
define('_SES_USR_SYS_USER_C', 6);
define('_SES_USR_SYS_USER_O', 7);
define('_SES_USR_FIN', 10);
define('_SES_USR_MKT', 11);
define('_SES_USR_OPE', 12);
define('_SES_USR_CCC', 13);
define('_SES_USR_AGT', 50);
define('_SES_USR_OFF', 100);
define('_SES_USR_SACP', 200);
define('_SES_USR_CP', 201);
define('_SES_USR_USER', 202);

define('_ID_FIELD', "id_field");
define('_SEA_FIELD', "search_field");
define('_SQL_FIELD', "sql_field");
define('_LAB_FIELD', "label_field");
define('_TYP_FIELD', "type_field");
define('_COM_TYP_FIELD', "com_type_field"); // _COM_TYP_FIELD is a data type to present on combine field
define('_COM_FIELD1', "com_field1"); // _COM_FIELD1 is a number of $fields_info array
define('_COM_FIELD2', "com_field2"); // _COM_FIELD2 is a number of $fields_info array
define('_COM_FIELD3', "com_field3"); // _COM_FIELD3 is a number of $fields_info array
define('_BG_MARK_FIELD', "bg_mark_field"); // _BG_MARK_FIELD is a value of decision
define('_BG_OPER_FIELD', "bg_oper_field"); // _BG_OPER_FIELD set operator with _BG_MARK_FIELD
define('_BG_COLOR_FIELD', "bg_color_field"); // _BG_COLOR_FIELD for fill bg color of row : info>blue; active>gray; warning>yellow; success>green; danger>red
define('_ALG_FIELD', "align_field"); // 1=left; 2=right
define('_ICO_FIELD', "icon_field"); // replace icon to this field
define('_RID_FIELD', "ref_icon_field"); // replace icon to this field
define('_HREF_FIELD', "href_field"); // replace <a> href to this field exclude value = 0
define('_HREF_FIELD_ZERO', "href_field_zero"); // replace <a> href to this field 
define('_VAL_HTML_FIELD', "value_html_field"); // replace value from db to html code
define('_SORT_FIELD', "sort_field");
define('_SORT_DESC_FIELD', "sort_desc_field");
define('_SORT_BOC_FIELD', "sort_block_field");
define('_FORCE_WORD_WARP_FIELD', "force_word_warp");

define('_TXT_SEARCH', "text_search");
define('_STA_YEAR', "start_year");
define('_END_YEAR', "end_year");
define('_STA_MONTH', "start_month");
define('_END_MONTH', "end_month");
define('_STA_DAY', "start_day");
define('_END_DAY', "end_day");

define('_USERNAME', "username");
define('_SENDER', "sender");

define('_USR_SUM', "user_sumrpt");
define('_SENDER_SUM', "sender_sumrpt");

include_once('_p_define_extend.php');
?>