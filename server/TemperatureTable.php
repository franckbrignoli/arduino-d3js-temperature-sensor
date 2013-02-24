<?php
require_once('config.php');

/*
CREATE TABLE IF NOT EXISTS `temperature` (
  `reported_at` datetime NOT NULL,
  `temperature` float NOT NULL
)
*/

class TemperatureTable
{
	private static $conn = null;

	public static function getTodayReport($interval)
	{
		$query = "SELECT AVG(temperature) as temperature, CONCAT(LPAD(HOUR(`reported_at`), 2, '0'),':', LPAD((MINUTE(`reported_at`) DIV ".$interval." * ".$interval."), 2, '0'),':00') as reported_at FROM temperature WHERE DATE(`reported_at`) = CURDATE() GROUP BY HOUR(`reported_at`), MINUTE(`reported_at`) DIV ".$interval." ORDER BY reported_at ";

		return self::getConnection()->query($query);
	}

	public static function getTodayAverage()
	{
		$query = "SELECT AVG(temperature) as temperature FROM temperature WHERE DATE(`reported_at`) = CURDATE()";

		return self::getConnection()->query($query);
	}

	public static function getConnection()
	{
		if (!isset(self::$conn))
			self::$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);		

		return self::$conn;
	}

	public static function add($temperature)
	{
		$query = 'INSERT INTO `temperature` VALUES (NOW(), :temperature)';
	
		$stmt = self::getConnection()->prepare($query);
		$stmt->execute(array('temperature' => $temperature));
	}
}


