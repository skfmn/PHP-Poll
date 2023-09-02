<?php
define('HOST','{#servername#}');
define('USER','{#username#}');
define('PASSWORD','{#dbpassword#}');
define('DATABASE','{#dbname#}');
define('DBPREFIX','{#dbprefix#}');
define('BASEDIR','{#basedir#}');
define('POLLDIR','{#polldir#}');

if ($_SERVER["HTTPS"] == "off") {
  $http = "http";
}else{
  $http = "https";
}

$httpHost = $_SERVER["HTTP_HOST"];
$redirect = $http."://".$httpHost.POLLDIR;

$version = "1.2";

?>
