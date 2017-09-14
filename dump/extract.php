<?php

// extract references from a Wikispecies dump

require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/couchsimple.php');

$filename = 'specieswiki-20161120/specieswiki-20161120-pages-articles-multistream.xml';

$file_handle = fopen($filename, "r");


$state = 0;
$page = '';
$title = '';
$refs = array();
$is_authority = false;

$timestamp = '';

$force = true;

$count = 0;

while (!feof($file_handle)) 
{
	$line = fgets($file_handle);
	
	//echo $line . "\n";
	
	switch ($state)
	{
		case 0:
			if (preg_match('/^\s+<page>/', $line))
			{
				$state = 1;
				$page = '';
				$refs = array();
				$is_authority = false;
				$timestamp = '';
				$title = '';
				//echo ".\n";
			}
			break;
			
		case 1:
			if (preg_match('/^\s+<\/page>/', $line))
			{
				//echo "\n\n*****\n\n";
				//echo $page;
				
				if (count($refs) > 0)
				{
				
					$obj = new stdclass;
					
					$obj->_id = str_replace(' ', '_', $title);
					$obj->title = $title;
					$obj->timestamp = $timestamp;
					
					foreach ($refs as $r)
					{
						$citation = new stdclass;
						$citation->string = $r;
						
						$citation->string = str_replace('</text>', '', $citation->string);
						
						$obj->references[] = $citation;
					}
					
					if ($is_authority)
					{
						//echo "*** person ***\n";
					}
					

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
					
					$count++;
					
					if ($count == 10000) 
					{
						exit();
					}
				}
							
				$state = 0;
			}
			else
			{
				$page .= $line;
				
				if (preg_match('/^\s*<title>(?<title>.*)<\/title>/', $line, $m))
				{
					//print_r($m);
					$title = $m['title']; 
				}

				// <timestamp>2015-10-29T19:34:16Z</timestamp>

				if (preg_match('/^\s*<timestamp>(?<timestamp>.*)<\/timestamp>/', $line, $m))
				{
					//print_r($m);
					$timestamp = $m['timestamp']; 
										
				}
				
				
				
				if (preg_match('/^\*\s+\{\{a/', $line))
				{
					// possible reference
					
					$text = trim($line);
					$line = str_replace('</text>', '', $text);
					
					$refs[] = $text;
					//echo $title . "|" . $line . "\n";
				}
				
				if (preg_match('/\[\[Category:Taxon authorities\]\]/', $line))
				{
					$is_authority = true;
				}
				
				
			}
			break;
				
		default:
			break;
			
	}
}

?>
