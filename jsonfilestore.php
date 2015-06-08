<?php

class JsonFileStore 
{
	private $filename;
	private $mod_date;
	public $current_date;

	public function __construct($filename) {
        $this->mod_date = '';
        $this->filename = $filename;
        $this->current_date = date("c");
    }

	public function doesExist() 
	{
		if (is_file($this->filename)) {
			return true;	
		} else {
			return false;
		}
	}

	public function isRecent()
	{
		$file_mod_date = stat($this->filename);
		$this->mod_date = new DateTime('@'. $file_mod_date['mtime']); 
		
		//check if modified date is today
		//echo $this->current_date;

		$foo = new DateTime($this->current_date);//double check why date object not recognised

	    if ($this->formatYmd($this->mod_date) == $this->formatYmd($foo)) {
	    	return true;
	    } else {
	    	return false;
   		}
	}

	private function formatYmd($date)
	{
		return $date->format('Y-m-d');
	}

	public function read() //change to private when add a validate function
	{
		return json_decode(file_get_contents($this->filename),TRUE);
	}

	public function write($jsondata)
	{	
		$file = fopen($this->filename,'w+'); // or die('Cannot open file');
	    if ($file === false){
	     	//TODO error opening file 
	    }

	    fwrite($file, json_encode($jsondata));
	    fclose($file);
	}

}

?>