<?php
     session_start();
     unset($_SESSION['user_id']);
     session_destroy();
     header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
     header('Pragma: no-cache');
     header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
     header('Location: ../index.php');
?>