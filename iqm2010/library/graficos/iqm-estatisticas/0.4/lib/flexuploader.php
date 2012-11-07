<?php 
class FlexUploader
{
	public $fileName = 'file.dat';
	public $path = '';
	public $fileContents = '';
	public $fileMode = 'w';
	
	public function save()
	{
		$handle = fopen($this->path.$this->fileName,$this->fileMode);
		fwrite($handle,base64_decode($this->fileContents));
		fclose($handle);
	}
	public function chmod($mode)
	{
		exec("chmod $mode {$this->path}{$this->fileName}");
	}
}