<?php
namespace Root0x7;


class Files
{
	public function get($filename)
	{
		return file_get_contents(__DIR__."/../$filename");
	}
	public function put($file, $data)
	{
		return file_put_contents(__DIR__."/../$file",$data);
	}
}
