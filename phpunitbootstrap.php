<?php

if (file_exists($file = __DIR__.'/../../autoload.php'))
{
	include $file;
}
else
{
	include 'vendor/autoload.php';
}