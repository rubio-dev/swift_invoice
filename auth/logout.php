<?php
require_once '../config/setup.php';

session_unset();
session_destroy();

redirect('/swift_invoice/auth/login.php');
?>