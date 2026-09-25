<?php

require_once __DIR__ . "/auth.php";

start_admin_session();
$_SESSION = array();
session_destroy();

header("Location: index.php");
exit();

?>
