<?php
require_once '../config/db.php';
require_once '../src/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$auth->logout();
