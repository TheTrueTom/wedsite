<?php
session_start();

session_unset();

session_destroy();

header('location: rsvp_display.php');
?>