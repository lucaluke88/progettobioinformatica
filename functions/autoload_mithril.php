<?php
	function __autoload($class)
	{
		$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
		if (file_exists($fileName))
		{
			require_once ($fileName);
		}
	}
