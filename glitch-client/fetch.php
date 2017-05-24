<?php

// Consume glitch-based services to parse Wikipsecies references


// can we associate reference with authors
// can we associate references with Wikidata ids for authors (and other things?)

require_once(dirname(dirname(__FILE__)) . '/lib.php');

$queue = array();


$title = 'Ryuthela nishihirai';
$title = 'Ryuthela';
//$title = 'ISSN_0363-6445';

$title = 'Charles_Edward_Griswold';

//$title = 'Langbiana';
//$title = 'Lodovico_di_Caporiacco';

//$title = 'Pakawin_Dankittipakul';
//$title = 'Draconarius';
//$title = 'William_Lucas_Distant';
//$title = 'Zsolt_Bálint';
//$title = 'Ryuthela nishihirai';
//$title = 'Ryuthela';

//$title='Hai-Qiang_Yin';

$citations = array();

$queue[] = $title;

while (count($queue) > 0)
{
	$title = array_pop($queue);
	
	$title = str_replace(' ', '_', $title);
	$title = str_replace('&', '%26', $title);

	// service to retrieve references from page
	$url = 'https://rebel-lead.glitch.me/page?q=' . $title;
	
	// echo $url . "\n";

	$json = get($url);
	
	//echo $json;

	if ($json != '')
	{
	
		$obj = json_decode($json);
	
		print_r($obj);
		
		if (isset($obj->links))
		{		
			foreach ($obj->links as $link)
			{
				if (!in_array($link, $queue)) 
				{
					$queue[] = $link;
				}
			}
		}	
	
		if (isset($obj->references))
		{
			// service to parse reference
			foreach ($obj->references as $string)
			{
				$url = 'https://acoustic-bandana.glitch.me/parse?string=' . urlencode($string);
			
				$json = get($url);
				if ($json != '')
				{
					$citation = json_decode($json);
					$citations[] = $citation;
				}
			}	
		}
		
	}
}

print_r($citations);


?>
