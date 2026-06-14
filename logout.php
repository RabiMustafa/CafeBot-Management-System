<?php
session_start();
session_unset();    // Remove all session variables
session_destroy();  // Destroy the session completely

// Send them back to the home page
header("Location: index.php");
exit();
?>