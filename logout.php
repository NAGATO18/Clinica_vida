<?php
session_start();
session_destroy(); // Elimina la sesión
header("Location: login.html"); // Redirige al login
exit();
