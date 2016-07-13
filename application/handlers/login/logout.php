<?php
/*
 * Logout handler:
 *
 * - Logs the user out.
 */
namespace application\handlers\login\logout;

session_start();

// Unset login related sessions

unset($_SESSION['login']);
unset($_SESSION['userId']);

header("Location: ../../../web/index.php");
exit();

?>