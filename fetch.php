<?php

require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/parse.php');


// Fetch a Wikispecies reference tenplate
function fetch_reference($reference_name)
{
	$reference = null;

	$url = 'https://species.wikimedia.org/w/index.php?title=Special:Export&pages=Template:' . $reference_name;

	$xml = get($url);

	$dom= new DOMDocument;
	$dom->loadXML($xml);
	$xpath = new DOMXPath($dom);

	$xpath->registerNamespace("wiki", "http://www.mediawiki.org/xml/export-0.10/");

	$nodeCollection = $xpath->query ("//wiki:text");
	foreach($nodeCollection as $node)
	{
		$text = $node->firstChild->nodeValue;
		$lines = explode("\n", $text);

		foreach ($lines as $line)
		{
			if (preg_match('/^\*\s+\{/', $line))
			{
				$reference = parse_wikispecies($line);
				
			}
		}			
	
	
	}
	
	return $reference;
}

if (0)
{

$refs = array(
'Botero,_2015b',
'Botero,_2015a',
'Monné_et_al.,_2016',
'Warren,_1894',
'Jakusch,_2003a',);
$refs=array(
'Jakusch,_2003'

);

foreach ($refs as $ref)
{
	$reference = fetch_reference($ref);
	if ($reference)
	{
		echo reference_to_ris($reference);
	}
}

}

?>
