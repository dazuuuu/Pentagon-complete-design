<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;

Auth::startSession();
Auth::logout();
header('Location: login.php');
exit;
