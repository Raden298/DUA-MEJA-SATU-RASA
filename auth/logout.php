<?php
require_once __DIR__ . '/../config/config.php';

// bersihin session user
$_SESSION = [];
session_destroy();

// balikin ke home
header('Location: /index.php');
exit;
