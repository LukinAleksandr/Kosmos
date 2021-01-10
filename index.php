<?php
session_name("SESSKOSID");
session_start(); 

define('ROOT', $_SERVER['DOCUMENT_ROOT']);

define('SITE', ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);


    include_once ROOT . "/components/Router.php";
    include_once ROOT . "/components/DB.php";
    include_once ROOT . "/components/StaticStatement.php";
    include_once ROOT . "/components/Validator.php";


    $r = new Router();
    $db = new DB();
    $ss = new StaticStatement();
    $validator = new Validator();

    $r->go();