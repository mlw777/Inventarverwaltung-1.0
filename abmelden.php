<?php
// Starten der Sitzung
session_start();

// Löschen aller Session-Variablen
$_SESSION = array();

// Zerstören der Sitzung
session_destroy();

// Weiterleitung zur Startseite
header("Location: Startseite.php"); 
exit();
?>
