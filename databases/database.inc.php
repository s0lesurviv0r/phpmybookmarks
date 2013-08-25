<?php

abstract class Database
{
	/*
	 * @param 
	 */
	abstract public function query($sql);
	
	abstract public function get_column($sql);
	
	abstract public function get_row($sql);
	
	abstract public function get_rows($sql);
}

?>