<?php
session_start();
session_unset();
session_destroy();
header("Location: ../view/pages/LandingPage.php");
exit();
?>
