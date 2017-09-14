<?php

// Consume glitch-based services to parse Wikipsecies references


// can we associate reference with authors
// can we associate references with Wikidata ids for authors (and other things?)

require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/couchsimple.php');

$queue = array();

// Examples

$title = 'Ryuthela nishihirai';
$title = 'Ryuthela';
//$title = 'ISSN_0363-6445';

//$title = 'Charles_Edward_Griswold';

//$title = 'Langbiana';
//$title = 'Lodovico_di_Caporiacco';

//$title = 'Pakawin_Dankittipakul';
//$title = 'Draconarius';
//$title = 'William_Lucas_Distant';
//$title = 'Zsolt_Bálint';
//$title = 'Ryuthela nishihirai';
//$title = 'Ryuthela';

//$title='Hai-Qiang_Yin';

//$title = 'Tord_Tamerlan_Teodor_Thorell';

//$title = 'Mauricio_M._Garcia';
//$title = 'Andrew_Edward_Z._Short';

$title = 'Campylospermum_serratum'; // lots of references in various forms
//$title = 'Template:Pederneiras_et_al.,_2014';

$title = 'Hugh_Bryan_Spencer_Womersley';
$title = 'Gerald_Thompson_Kraft';
$title = 'Nothofagus';
$title = 'Fuscospora';

$title = 'Trachylepis_atlantica';
$title = 'Trachylepis';

$title = 'Hapalomys';
$title = 'Viatcheslav_V._Rozhnov';

$title = 'Ryuthela nishihirai';

$title = 'Erik_J._van_Nieukerken';

$title = 'Guillermo_Kuschel';
$title = 'Roberto_Pace'; // huge number of articles

$citations = array();

$queue[] = $title;

$counter = 0;
$force = true;

while (count($queue) > 0)
{
	$title = array_pop($queue);
	
	$title = str_replace(' ', '_', $title);
	$title = str_replace('&', '%26', $title);

	// service to retrieve references from page
	$url = 'https://rebel-lead.glitch.me/page?q=' . $title;
	
	// echo $url . "\n";

	$json = get($url);

	if ($json != '')
	{
		$obj = json_decode($json);
	
		print_r($obj);
		
		// Add any reference template links to queue
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
	
		// Attempt to parse reference
		if (isset($obj->references))
		{
			// service to parse reference
			foreach ($obj->references as $string)
			{
				$url = 'https://acoustic-bandana.glitch.me/parse?string=' . urlencode($string);
			
				$json = get($url);
				if ($json != '')
				{
					$counter++;
				
					$citation = json_decode($json);
					
					$ignore = true;
					
					if (isset($citation->unstructured))
					{
						$ignore = false;
						if ($citation->unstructured == '')
						{
							$ignore = true;
						}
					}

					if (!$ignore)
					{					
					
						$citations[] = $citation;
					
						// default identifier is combination of Wikispecies age and counter
						$identifier = $title . '_' . $counter;
					
						// if we have a template then we have a URL for this reference
						if (isset($citation->WIKISPECIES))
						{
							$identifier = $citation->WIKISPECIES;
						}					
					
						$go = true;
	
						// Check whether this record already exists (i.e., have we done this object already?)
						$exists = $couch->exists($identifier);
	
						if ($exists)
						{
							echo "$identifier Exists\n";
							$go = false;
		
							if ($force)
							{
								echo "[forcing]\n";
								$couch->add_update_or_delete_document(null, $identifier, 'delete');
								$go = true;		
							}
						}

						if ($go)
						{
							// Do we want to attempt to add any identifiers here, such as DOIs?
					
							// couchdb
							$doc = new stdclass;

							$doc->_id = $identifier;

							// By default message is empty and has timestamp set to "now"
							// This means it will be at the end of the queue of things to add
							$doc->{'message-timestamp'} = date("c", time());
							$doc->{'message-modified'} 	= $doc->{'message-timestamp'};
							$doc->{'message-format'} 	= 'application/vnd.crossref-citation+json';
					
							$doc->message = $citation;
						
							$resp = $couch->send("PUT", "/" . $config['couchdb_options']['database'] . "/" . urlencode($doc->_id), json_encode($doc));
							var_dump($resp);					
						}					
					}
				}
			}	
		}		
	}
}

print_r($citations);


?>
