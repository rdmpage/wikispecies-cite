<?php

require_once('parse.php');
require_once('fetch.php');

$filename = 'examples/william_warren_wikispecies.txt';
$filename = 'examples/Oldfield_Thomas.txt';
$filename = 'examples/boulenger.txt';
//$filename = 'alpheus.txt';

$text = file_get_contents($filename);

$lines = explode("\n", $text);

$fetch = false; // offline
//$fetch = true; // online

foreach ($lines as $line)
{
	$matched = false;
	
	if (!$matched)
	{
		// skip commons template
		if (preg_match('/^\*\s*\{\{commons\}\}/', $line, $m))
		{
			$matched = true;
		}
	}
	
	
	if (!$matched)
	{
		// transcluded template in list 
		if (preg_match('/^\*\s+\{\{(?<refname>[A-Z][a-z]+,\s+[0-9]{4}[a-z]?)\}\}/', $line, $m))
		{
			//echo $line . "\n";
			//print_r($m);
		
			$refname = $m['refname'];
			$refname = str_replace(' ', '_', $refname);
		
			if ($fetch)
			{
				$reference = fetch_reference($refname);
				echo reference_to_ris($reference);
			}
			
			$matched = true;
		}
	}



	// inline reference
	if (!$matched)
	{

		if (preg_match('/^\*\s+\{/', $line))
		{
			//echo $line . "\n";
		
			$reference = parse_wikispecies($line);
			echo reference_to_ris($reference);
		
	//		print_r($reference);
		
			if (!isset($reference->title))
			{
				echo "not parsed\n"; 
				exit();
			}
			
			$matched = true;
		}
	}
	
	if (!$matched)
	{
		// transcluded template
		if (preg_match('/^\{\{(?<refname>.*[0-9]{4}.*)\}\}/', $line, $m))
		{
			//echo $line . "\n";
			//print_r($m);
		
			$refname = $m['refname'];
			$refname = str_replace(' ', '_', $refname);
		
			if ($fetch)
			{
				$reference = fetch_reference($refname);
				echo reference_to_ris($reference);
			}
			$matched = true;
		}
	}
	
}

?>