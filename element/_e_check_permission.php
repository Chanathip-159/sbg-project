<?php
# ทดสอบภาษาไทย
if (!userPermission($_SESSION[_SES_USR_TYP_ID], $_SESSION[_SES_CUR_PAGE])) {
	require_once("_t_not_access.php");
	exit;
}
?>