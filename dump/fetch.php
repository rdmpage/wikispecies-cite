<?php

// Fetch pages direct from Wiki and extract references

require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/couchsimple.php');


$force = true;

$page_name = 'William_Elford_Leach';
$page_name = 'Abelisauroidea';
$page_name = 'Abelisauridae';
$page_name = 'Template:Tortosa_et_al.,_2014';
$page_name = 'Wandinidae';
$page_name = 'Pseudocyphocaris';
$page_name = 'Lysianassoidea';
$page_name = 'Varanus';

$page_name = 'Micropterix_longicornuella';
$page_name = 'Micropterix';
$page_name = 'User:Neferkheperre';
$page_name = 'Template:Ross_%26_Perreault,_1999';

$page_names = array('Thierry_Bouyer');

$page_names = array('Thierry_Bouyer');

$page_names = array('Michel_Libert');

$include_transclusions = true;

while (count($page_names) > 0)
{
	$page_name = array_pop($page_names);
	
	$url = 'https://species.wikimedia.org/w/index.php?title=Special:Export&pages=' . $page_name;

	$xml = get($url);
	
	echo $xml;

	$dom= new DOMDocument;
	$dom->loadXML($xml);
	$xpath = new DOMXPath($dom);

	$xpath->registerNamespace("wiki", "http://www.mediawiki.org/xml/export-0.10/");
	
	$obj = new stdclass;
	
	$obj->_id = $page_name;
	
	$nodeCollection = $xpath->query ("//wiki:title");
	foreach($nodeCollection as $node)
	{
		$obj->title = $node->firstChild->nodeValue;
	}	
	
	$nodeCollection = $xpath->query ("//wiki:timestamp");
	foreach($nodeCollection as $node)
	{
		$obj->timestamp = $node->firstChild->nodeValue;
	}	
	
	
	$nodeCollection = $xpath->query ("//wiki:text");
	foreach($nodeCollection as $node)
	{
		$obj->references = array();
		
		// get text
		$obj->text = $node->firstChild->nodeValue;		
		$lines = explode("\n", $obj->text);
		
		foreach ($lines as $line)
		{
			if (preg_match('/^\*\s+\{\{a/', $line))
			{
				// possible reference
				
				$r = trim($line);
				$r = str_replace('</text>', '', $r);
				
				$citation = new stdclass;
				$citation->string = $r;
				
				$obj->references[] = $citation;
			}	
			
			if ($include_transclusions)
			{
				// transcluded references
				if (preg_match('/^(\*\s+)?\{\{(?<refname>[A-Z][\p{L}]+([,\s&;[a-zA-Z]+)[0-9]{4}[a-z]?)\}\}$/u', trim($line), $m))
				{
					$refname = $m['refname'];
					$refname = str_replace(' ', '_', $refname);
					$refname = str_replace('&', '%26', $refname);	
					
					$page_names[] = 'Template:' . $refname;		
				}			
			}
			
			
		}		
		// taxonomy
		if (preg_match('/== Taxonavigation ==\s+\{\{(?<parent>.*)\}\}/Uu', $obj->text, $m))
		{
			$obj->taxonavigation = $m['parent'];
		}
		

		
		
	}
	
	//print_r($page_names);
	//exit();
		
	// service to parse reference
	$n = count($obj->references);
	for ($i = 0; $i < $n; $i++)
	{
		$string = $obj->references[$i]->string;

		$url = 'https://acoustic-bandana.glitch.me/parse?string=' . urlencode($string);

		$json = get($url);
		if ($json != '')
		{
			$counter++;

			$csl = json_decode($json);
	
			$ignore = true;
	
			if (isset($csl->unstructured))
			{
				$ignore = false;
				if ($csl->unstructured == '')
				{
					$ignore = true;
				}
			}

			if (!$ignore)
			{					
	
				$obj->references[$i]->csl = $csl;
			}
		}
	}	
	
	
	print_r($obj);
	
	$go = true;

	// Check whether this record already exists (i.e., have we done this object already?)
	$exists = $couch->exists($obj->_id);

	if ($exists)
	{
		echo $obj->_id . " exists\n";
		$go = false;

		if ($force)
		{
			echo "[forcing]\n";
			$couch->add_update_or_delete_document(null, $obj->_id, 'delete');
			$go = true;		
		}
	}

	if ($go)
	{
		// Do we want to attempt to add any identifiers here, such as DOIs?
		$resp = $couch->send("PUT", "/" . $config['couchdb_options']['database'] . "/" . urlencode($obj->_id), json_encode($obj));
		var_dump($resp);					
	}						
						
	$include_transclusions = false; // only do this the first time

}

?>
