<?php

// called by Database
class ignoreWordList extends Singleton {
	
	private $wordlist;
	
	protected function __construct()
	{
		$this->wordlist = array();
		
		@$handle = fopen(__DIR__.'/ignoreWords.txt', 'rb');
		
		while (!feof($handle))
		{
			$this->wordlist[strtolower(trim(fgets($handle, 999)))] = 0;
		}	
	}
	
	protected function isIgnoreWord($string)
	{
		return isset($this->wordlist[strtolower(trim($string))]);
	}
	
}

?>