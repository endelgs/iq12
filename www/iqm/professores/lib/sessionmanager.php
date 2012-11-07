<?php
class SessionManager
{
	const UNDEFINED_INDEX = 'UI_ERROR';
	private function __construct()
	{
		
	}
	public static function getIndex($index)
	{
		SessionManager::check();
		return (isset($_SESSION[$index]))?$_SESSION[$index]:SessionManager::UNDEFINED_INDEX;
	}
	public static function setIndex($index,$value)
	{
		SessionManager::check();
		$_SESSION[$index] = $value;
	}
	public static function check()
	{
		if(!isset($_SESSION))
			session_start();
	}
}
?>