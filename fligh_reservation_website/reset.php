<?php
require_once('config.php');
require_once('db_handler.php');
require_once('functions.php');

resetStatoDB();
header("location:index.php");