<?php
session_start();
session_destroy();
header("Location: ../Front Office/sign-in.html");
exit();
?>
