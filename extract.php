<?php

require_once('parse.php');
require_once('fetch.php');

$filename = 'examples/william_warren_wikispecies.txt';
$filename = 'examples/Oldfield_Thomas.txt';

$text = file_get_contents($filename);

$lines = explode("\n", $text);

foreach ($lines as $line)
{
	if (preg_match('/^\*\s+\{/', $line))
	{
		//echo $line . "\n";
		
		$reference = parse_wikispecies($line);
		echo reference_to_ris($reference);
	}
	
	if (preg_match('/^\{\{(?<refname>.*[0-9]{4}.*)\}\}/', $line, $m))
	{
		//echo $line . "\n";
		//print_r($m);
		
		$refname = $m['refname'];
		$refname = str_replace(' ', '_', $refname);
		
		$reference = fetch_reference($refname);
		echo reference_to_ris($reference);
	}
	
}

?>