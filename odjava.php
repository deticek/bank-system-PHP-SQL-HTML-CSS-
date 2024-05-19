<?php
session_start();

// Počistimo sejo
session_unset();
session_destroy();

// Izpraznimo vsebino datoteke 'id.json'
file_put_contents('id.json', '');

// Preusmerimo na index.html
header("Location: index.html");
exit();
?>
