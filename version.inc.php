<?php

class Version
{
	public static $major = 0;
	public static $minor = 1;
	public static $revision = 0;
	
	public static function to_string()
	{
		return Version::$major . "." .
				Version::$minor . "." .
				Version::$revision;
	}
}