<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

function databaseConnection() {
    
    $authConfig = Array("host" => "localhost", "user" => "root", "password" => "Aort101ms!", "catalogue" => "sunlibrary");

    $mysqli = mysqli_connect($authConfig["host"], $authConfig["user"], $authConfig["password"], $authConfig["catalogue"]);


    return $mysqli;
}
