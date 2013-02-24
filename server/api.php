<?php

require_once('TemperatureTable.php');

$token = $_POST['token'];
$temperature = $_POST['temperature'];
if (isset($token) && $token == TOKEN && isset($temperature))
{
	TemperatureTable::add($temperature);
}

