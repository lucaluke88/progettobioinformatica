<?php
	spl_autoload_register(function ($class)
	{
		$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
		if (file_exists($fileName)) {
			require_once($fileName);
		}
	}, false, true);

	function dirToArray($dir)
	{

		$result = array();

		$cdir = scandir($dir);
		foreach ($cdir as $key => $value)
		{
			if (!in_array($value, array(".", "..")))
			{
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
				{
					$result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
				}
				else
				{
					$result[] = $value;
					
				}
			}
		}

		return $result;
	}
