<?php
define('_LOGN','/var/php.sbg.log/sbg.web');
define('_APP_ID',3); // ADMINAPPLEVEL_AppId = ADMINAPPinfo_Id = 3 = SBG
require_once(__DIR__.'/../../php.lib/define.profile.php');
/* //
define('_SERVICE_CAT',				1);
define('_SERVICE_ID',				1000000+_APP_ID);
define('_PROVIDER_ID',				1000);
define('_PROVIDER_NAME',		"CAT_MOBILE_VAS");
//*/
define('_SERVICE_NAME',"SBG"); // use [APPINFO_Name]

# interface
define('_MAX_FILE_ROWS',100000); // max msisdns per file
define('_GROUP_NUM',10);
define('_CONTACT_NUM',100);
define('_SMS_SERVICE_TYPE',"BUK"); // service type for send SMS
define('_ADMIN_L',10); // <=10 == Admin can do everything

# search bar
define('_SEL_TYPE',"select_type");
define('_SEL_GROUP',"select_group");

?>