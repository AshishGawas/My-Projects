<?php
require_once __DIR__ . '/config.php';
verify_csrf();
session_unset();
session_destroy();
header('Location: /index.php');
