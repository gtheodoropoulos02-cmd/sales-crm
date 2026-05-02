<?php
require_once 'config.php';
startSession();
$_SESSION = [];
session_destroy();
header('Location: index.php');
exit;
