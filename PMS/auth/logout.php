<?php
session_start();
session_destroy();
header("Location: /PMS/auth/login.php");
exit;