<?php
require_once "libs/database.php";
require_once "libs/controller.php";
require_once "libs/view.php";
require_once "libs/model.php";
require_once "libs/app.php";
require_once "config/config.php";
// include_once 'includes/user.php';
// include_once 'includes/user_session.php';


$data = new Database();
if ($data->connect_dobra()) {
    $app = new App();
} else {
}
