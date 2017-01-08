<?php

require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/utils.php');

// Exploring code to parse Wikispecies citations


//----------------------------------------------------------------------------------------
function escape_pattern($pattern)
{
	$pattern = str_replace('}', '\}', $pattern);
	$pattern = str_replace('[', '\[', $pattern);
	$pattern = str_replace(']', '\]', $pattern);
	$pattern = str_replace('(', '\(', $pattern);
	$pattern = str_replace(')', '\)', $pattern);
	$pattern = str_replace("'", "\'", $pattern);
	$pattern = str_replace("+", "\+", $pattern);
	
	return $pattern;
}

//----------------------------------------------------------------------------------------
function post_process(&$reference)
{
	if (isset($reference->title))
	{
		$reference->title = preg_replace("/\{\{aut\|([^\}\}]+|(?R))*}}/u", '$1', $reference->title);
	
		if (preg_match('/^\[(?<date>[0-9]{4})\]\.?\s+(?<title>.*)/u', $reference->title, $m))
		{
			$reference->date = $m['date'];
			$reference->title = $m['title'];
		}

		$reference->title = preg_replace("/''([^'']+|(?R))''/u", '<i>$1</i>', $reference->title);
		
		$reference->title = preg_replace("/:$/u", '', $reference->title);
	}	
	
	if (isset($reference->wikispecies))
	{
		$reference->id = $reference->wikispecies;
	}
	
	if (!isset($reference->type))
	{
		if (isset($reference->journal))
		{
			$reference->type = 'article';
		}
		else 
		{
			if (isset($reference->title))
			{
				$reference->type = 'book';	
			}
		}
	}
}

//----------------------------------------------------------------------------------------
function parse_wikispecies($string, $debug = false)
{
	$reference = new stdclass;
	$reference->notes = $string;
	$reference->parts = array();
	
	// do some cleaining if needed
	/*
	if (preg_match('/\*\s+\{\{ZooBankRef/', $string))
	{
		$string = preg_replace('/\*\s+\{\{ZooBankRef/', '{{ZooBankRef', $string);
	}
	*/


	// Extract all items enclosed in ''
	/*
	if (preg_match_all("/
		([^']''([^'']+|(?R))*'')
		/x", $string, $m))
	{
		print_r($m);
		//exit();					
	}
	*/
				
	// Volume----------------------------------------------------------------------------

	if (!isset($reference->parts['VOLUME-PAGINATION'] ))
	{
		if (preg_match("/
			''(?<journal>[^'']+)''
			,?
			\s+	
			(?<volume_pagination>		
			'''(?<volume>[0-9]+)'''
			(\([0-9]+\))?
			:
			\s+
			\[
			(?<url>http(.*))
			\s+
			(?<spage>[0-9]+)
			([-|–](?<epage>\d+))?
			\]
			)
			/x", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['JOURNAL'] = "''" . $m['journal'] . "''";
			$reference->parts['VOLUME-PAGINATION'] = $m['volume_pagination'];

			$reference->journal = $m['journal'];

			$reference->volume = $m['volume'];
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}

		}
	}	

	if (!isset($reference->parts['VOLUME-PAGINATION'] ))
	{
		//'']] 6(1): 1–66.	
		// '']] (8), '''16'''(92): 146-152.	
		if (preg_match("/
					''\]\]
					(\s+\((?<series>\d+)\))?
					,?
					\s+
					(''')?
					(?<volume>\d+)
					(''')?
					(\((?<issue>.*)\))?
					:
					\s*(?<spage>[0-9]+)
					([-|–](?<epage>\d+))?				
					/xu", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['VOLUME-PAGINATION'] = $m[0];
			
			if ($m['series'] != '')
			{
				$reference->series = $m['series'];
			}

			$reference->volume = $m['volume'];
			
			if ($m['issue'] != '')
			{
				$reference->issue = $m['issue'];
			}			
			
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}
		}	
	}
	
	if (!isset($reference->parts['VOLUME-PAGINATION'] ))
	{
		// '']], 7th ser. 11: 496–499.
		if (preg_match("/''\]\],?(\s+(?<series>\d+)[a-z]+ ser\.)\s+(''')?(?<volume>\d+)(''')?(\((?<issue>.*)\))?:\s*(?<spage>[0-9]+)([-|–](?<epage>\d+))?/u", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['VOLUME-PAGINATION'] = $m[0];
			
			if ($m['series'] != '')
			{
				$reference->series = $m['series'];
			}

			$reference->volume = $m['volume'];
			
			if ($m['issue'] != '')
			{
				$reference->issue = $m['issue'];
			}			
			
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}
		}	
	}
		
	//  37. 35–52.
	if (!isset($reference->parts['VOLUME-PAGINATION'] ))
	{
		//  37. 35–52.
		if (preg_match("/(\]\])?\s+(?<volume>\d+)\.\s+(?<spage>[0-9]+)([-|–](?<epage>\d+))?\./u", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['VOLUME-PAGINATION'] = $m[0];

			$reference->volume = $m['volume'];
						
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}
		}	
	}
	
	
	// ''Proceedings of the Zoological Society of London'' 1888: 130–135
	//  ''Annals and Magazine of Natural History'' ser 6, 9: 250–254.
	if (!isset($reference->parts['JOURNAL']) && !isset($reference->parts['VOLUME-PAGINATION']))
	{
		if (preg_match("/
			''(?<journal>(\w+\.?(,?\s+\w+[']?\w+\.?)*))''
			(\s+ser\.?\s+(?<series>\d+))?
			,?
			\s+
			(?<volume_pagination>
			(?<volume>\d+([-|–]\d+)?)
			\s*
			(\((?<issue>.*)\))?
			[,|:]
			\s*(?<spage>[0-9]+)
			([-|–](?<epage>\d+))?
			)
			\b						
			/ux", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['JOURNAL'] = "''" . $m['journal'] . "''";
			$reference->parts['VOLUME-PAGINATION'] = $m['volume_pagination'];

			$reference->journal = $m['journal'];
			
			if (preg_match("/\[\[ISSN\s+(?<issn>[0-9]{4}-[0-9]{3}([0-9]|X))\|(?<journal>.*)\]\]/", $reference->journal, $mm))
			{
				$reference->issn = $mm['issn'];
				$reference->journal = $mm['journal'];
			}
			
			
			if ($m['series'] != '')
			{
				$reference->series = $m['series'];
			}
			
			$reference->volume = $m['volume'];
			if ($m['issue'] != '')
			{
				$reference->issue = $m['issue'];
			}
	
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}
		}
	}
	
	//  ''Annali del Museo Civico di Storia Naturale di Genova'' (2) 13: 304–347.
	if (!isset($reference->parts['JOURNAL']) && !isset($reference->parts['VOLUME-PAGINATION']))
	{
		if (preg_match("/
			''(?<journal>[^'']+)''
			(\s+\((?<series>\d+))\)?
			,?
			\s*
			(?<volume_pagination>
			(?<volume>\d+)
			\s*
			(\((?<issue>.*)\))?
			[,|:]
			\s*(?<spage>[0-9]+)
			([-|–](?<epage>\d+))?
			)						
			/ux", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['JOURNAL'] = "''" . $m['journal'] . "''";
			$reference->parts['VOLUME-PAGINATION'] = $m['volume_pagination'];

			$reference->journal = $m['journal'];
			
			if (preg_match("/\[\[ISSN\s+(?<issn>[0-9]{4}-[0-9]{3}([0-9]|X))\|(?<journal>.*)\]\]/", $reference->journal, $mm))
			{
				$reference->issn = $mm['issn'];
				$reference->journal = $mm['journal'];
			}
			
			
			if ($m['series'] != '')
			{
				$reference->series = $m['series'];
			}
			
			$reference->volume = $m['volume'];
			if ($m['issue'] != '')
			{
				$reference->issue = $m['issue'];
			}
	
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}
		}
	}	

	if (!isset($reference->parts['JOURNAL']) && !isset($reference->parts['VOLUME-PAGINATION']))
	{
		// ''Trans. Lep. Soc. Japan'', '''53'''(4): 225-228
		if (preg_match("/
			''(?<journal>[^'']+)''
			,?
			\s+
			(?<volume_pagination>
			'''(?<volume>\d+)'''
			(\((?<issue>.*)\))?
			[,|:]
			\s+(?<spage>[0-9]+)
			([-|–](?<epage>\d+))?
			)						
			/ux", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['JOURNAL'] = "''" . $m['journal'] . "''";
			$reference->parts['VOLUME-PAGINATION'] = $m['volume_pagination'];

			$reference->journal = $m['journal'];
			$reference->volume = $m['volume'];
			if ($m['issue'] != '')
			{
				$reference->issue = $m['issue'];
			}
	
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}
		}
	}
	
	if (!isset($reference->parts['JOURNAL']) && !isset($reference->parts['VOLUME-PAGINATION']))
	{
		// ''Zoologische Mededelingen'' '''85 (1)''': 1-10.
		if (preg_match("/
			''(?<journal>[^'']+)''
			,?
			\s+
			(?<volume_pagination>
			'''(?<volume>\d+)
			\s+
			\((?<issue>.*)\)
			'''
			[,|:]
			\s+(?<spage>[0-9]+)
			([-|–](?<epage>\d+))?
			)						
			/ux", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['JOURNAL'] = "''" . $m['journal'] . "''";
			$reference->parts['VOLUME-PAGINATION'] = $m['volume_pagination'];

			$reference->journal = $m['journal'];
			$reference->volume = $m['volume'];
			if ($m['issue'] != '')
			{
				$reference->issue = $m['issue'];
			}
	
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}
		}
	}	
	
	// . Annals and Magazine of Natural History, ser. 7, 10: 311–314.
	if (!isset($reference->parts['JOURNAL']) && !isset($reference->parts['VOLUME-PAGINATION']))
	{
		// . Annals and Magazine of Natural History, ser. 7, 10: 311–314.
		if (preg_match("/
			
			\.
			\s+
			(?<journal>\w+(\s+\w+)*)
			,
			(\s+ser\.?\s+(?<series>\d+),)?
			\s+
			(?<volume_pagination>
			(?<volume>\d+)
			(\((?<issue>.*)\))?
			[,|:]
			\s+(?<spage>[0-9]+)
			([-|–](?<epage>\d+))?
			)						
			/ux", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['JOURNAL'] = $m['journal'];
			$reference->parts['VOLUME-PAGINATION'] = $m['volume_pagination'];

			$reference->journal = $m['journal'];
			$reference->volume = $m['volume'];
			$reference->series = $m['series'];
			if ($m['issue'] != '')
			{
				$reference->issue = $m['issue'];
			}
	
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}
		}
	}
	
	// proc calif
	// ''Proceedings of the California Academy of Sciences'' (Series 4) 59(3): 113–123.
	if (!isset($reference->parts['JOURNAL']) && !isset($reference->parts['VOLUME-PAGINATION']))
	{
		// . Annals and Magazine of Natural History, ser. 7, 10: 311–314.
		if (preg_match("/
			''(?<journal>[^'']+)''
			\s+
			\([s|S]eries\s+(?<series>\d+)\)
			\s+
			(?<volume_pagination>
			(?<volume>\d+)
			(\((?<issue>.*)\))?
			[,|:]
			\s+(?<spage>[0-9]+)
			([-|–](?<epage>\d+))?
			)						
			/ux", $string, $m))
		{
			$reference->matched[] = __LINE__;	
			$reference->parts['JOURNAL'] = $m['journal']; // . ' ' . $m['series'];
			$reference->parts['VOLUME-PAGINATION'] = $m['volume_pagination'];

			$reference->journal = $m['journal'];
			$reference->series = $m['series'];			
			$reference->volume = $m['volume'];
			if ($m['issue'] != '')
			{
				$reference->issue = $m['issue'];
			}
	
			$reference->spage = $m['spage'];

			if ($m['epage'] != '')
			{
				$reference->epage = $m['epage'];
			}
		}
	}
	
	
	// Zootaxa	
	if (!isset($reference->parts['JOURNAL']) && !isset($reference->parts['VOLUME-PAGINATION']))
	{
//		if (preg_match("/\{\{zootaxa(\|(\d+)\|(\d+)\|(\d+)\|(\d+)(\|\d+)*)\}\}/", $string, $m))
//		if (preg_match("/\{\{zootaxa(\|\d+)+\}\}/", $string, $m))
				
		if (preg_match("/(\{\{zootaxa\|([^\}\}]+|(?R))*\}\})/", $string, $m))
		{
			$reference->matched[] = __LINE__;
			$reference->parts['JOURNAL'] = $m[0];
			
			$reference->journal = 'Zootaxa';
			$reference->issn = '1175-5326';
			
			$parts = explode("|", $m[2]);
			
			//print_r($parts);
			
			//print_r($m);echo count($m); exit();
			switch ($parts[0])
			{
				case '2001':
					break;
		
				case '2002':
					break;
	
	
				case '2010':
				case '2011':
					$reference->year = $parts[0];
					$reference->volume = $parts[1];
					$reference->spage = $parts[2];
					$reference->epage = $parts[3];
					
					$reference->url = 'http://www.mapress.com/zootaxa/' . $reference->year . '/f/z' . str_pad($reference->volume, 5, '0', STR_PAD_LEFT) . 'p' . str_pad($reference->epage, 3, '0', STR_PAD_LEFT) . 'f.pdf';
					break;
					
				case '2013':
				case '2014':
				case '2015':
				case '2016':
					if (count($parts) == 6)
					{
						$reference->year = $parts[0];
						$reference->volume = $parts[1];
						$reference->issue = $parts[2];
						$reference->spage = $parts[3];
						$reference->epage = $parts[4];
						$reference->artnum = $parts[5];
						
						// 10.11646/zootaxa.4058.1.5
						$reference->doi = '10.11646/zootaxa.' . $reference->volume . '.' . $reference->issue . '.' . $reference->artnum;
					}
					break;					
		
				default:
					break;
			}			
		
			
		
		
		
		}


	}	
	
	// book pagination
	// i–xii + 1–373
	if (!isset($reference->parts['JOURNAL']) && !isset($reference->parts['VOLUME-PAGINATION']))
	{
		if (preg_match("/(?<pagination>[ixvlc]+[-|–][ixvlc]+\s+\+\s+\d+[-|–]\d+)/u", $string, $m))
		{
			$reference->matched[] = __LINE__;
			$reference->parts['VOLUME-PAGINATION'] = $m[0];
		}
	}
		
		
	
	// Journal----------------------------------------------------------------------------
	// Journal with ISSN 
	if (preg_match('/\[\[ISSN (?<issn>[0-9]{4}-[0-9]{3}([0-9]|X))\|\'\'(?<journal>.*)\'\'\]\]/', $string, $m))
	{
		$reference->matched[] = __LINE__;
		$reference->parts['JOURNAL'] = $m[0];
		
		$reference->journal = $m['journal'];
		$reference->issn = $m['issn'];
	}	


	if (!isset($reference->parts['JOURNAL']) && !isset($reference->parts['VOLUME-PAGINATION']))
	{

		// Hmmm, no journal...

	}

	
	// Authors----------------------------------------------------------------------------
	
	// same article may have multiple kids of author templates WTF!
	
	//if (!isset($reference->parts['AUTHOR']))
	{
		// {{a		
		if (preg_match_all("/
			(\{\{a\|([^\}\}]+|(?R))*\}\})
			/x", $string, $m))
		{
			$reference->matched[] = __LINE__;
	
			foreach ($m[0] as $mm)
			{
				$reference->parts['AUTHOR'][] = $mm;
			}
	
			$n = count($m);
	
			foreach ($m[$n - 1] as $a)
			{
		
				if (preg_match('/(?<uri>.*)\|(?<name>.*)/u', $a, $mm))
				{
					$author = new stdclass;
					$author->name = $mm['name'];
					$author->wikispecies = preg_replace('/\s+/u', '_', $mm['uri']);
			
					$reference->authors[] = $author;
			
				}
			}
		}
	}
	
	// authors with aut template and links
	//if (!isset($reference->parts['AUTHOR']))
	{
		if (preg_match_all("/
			(\{\{aut\|\[\[([^\}\}]+|(?R))*\]\]\}\})
			/x", $string, $m))
		{
			$reference->matched[] = __LINE__;
	
			foreach ($m[0] as $mm)
			{
				$reference->parts['AUTHOR'][] = $mm;
			}
	
			$n = count($m);
	
			foreach ($m[$n - 1] as $a)
			{
		
				if (preg_match('/(?<uri>.*)\|(?<name>.*)/u', $a, $mm))
				{			
					$author = new stdclass;
					$author->name = $mm['name'];
					$author->wikispecies = preg_replace('/\s+/u', '_', $mm['uri']);
			
					$reference->authors[] = $author;
			
				}
			}
	
		}	
	}
		
	// aut and no links
	//if (!isset($reference->parts['AUTHOR']))
	{
		// {{aut|Beutel, R.G.}}
		if (preg_match_all("/
			(\{\{aut[h]?\|([^\}\}]+|(?R))*r?\}\})
			/x", $string, $m))
		{
			$reference->matched[] = __LINE__;
		
			foreach ($m[0] as $mm)
			{
				$reference->parts['AUTHOR'][] = $mm;
			}
	
			$n = count($m);
	
		
			foreach ($m[$n - 1] as $a)
			{
				if (preg_match('/^(?<initials>.*)\|(?<surname>.*)\|r$/u', $a, $mm))
				{
					$author = new stdclass;
					$author->name = $mm['initials'] . ' ' . $mm['surname'];
					$reference->authors[] = $author;			
				}
				else
				{
					$authorstrings = array();
			
			
					// may have multiple authors
					$a = preg_replace('/\s+&\s+/u', '|', $a);
					$a = preg_replace('/\.,\s+/u', '.|', $a);
			
					$authorstrings = preg_split('/\|/u', $a);
					foreach ($authorstrings as $aname)
					{
						$author = new stdclass;
						$author->name = $aname;
						$reference->authors[] = $author;
					}
				}
			}
		}	
	}	
	
	/*
	// auth template
	// {{auth|A.|Anker|r}}
	if (preg_match_all("/
		(\{\{auth\|([^\}\}]+|(?R))*r\}\})
		/x", $string, $m))
	{
		$reference->matched[] = __LINE__;
		
		foreach ($m[0] as $mm)
		{
			$reference->parts['AUTHOR'][] = $mm;
		}
	
		$n = count($m);
	
		
		foreach ($m[$n - 1] as $a)
		{
			$parts = preg_split('/\|/u', $a);
			foreach ($authorstrings as $aname)
			{
				$author = new stdclass;
				$author->name = $parts[0] . ' ' . $parts[1];
				$reference->authors[] = $author;
			}
		}
	
	}	
	*/
	
	

	// Year ------------------------------------------------------------------------------
	// year
	if (preg_match('/\}\}[,|\.]?\s+[\(]?(?<year>[0-9]{4})[\)]?/', $string, $m))
	{
		$reference->matched[] = __LINE__;
		$reference->parts['YEAR'] = $m[0];
	
		$reference->year = $m['year'];
	}

	// Title  ----------------------------------------------------------------------------
	// title
	if (isset($reference->parts['YEAR']) && isset($reference->parts['JOURNAL']))
	{
		$pattern = 
			escape_pattern($reference->parts['YEAR'])
			. '[a-z]?(\/\d)?(\([0-9]{4}\))?[\.|:]?\s+(?<title>.*)\s+'
			. '(\'\')?'
			. escape_pattern($reference->parts['JOURNAL']);
		
		
		//echo $pattern . "\n";
		
		if (preg_match('/' . $pattern . '/u', $string, $m))
		{
			$reference->matched[] = __LINE__;
			$reference->parts['TITLE'] = $m['title'];
	
			$reference->title = $m['title'];
			$reference->title = preg_replace('/\.$/u', '', $reference->title);

		}
	}
	
	// title
	if (!isset($reference->parts['TITLE']) && isset($reference->parts['YEAR']) && isset($reference->parts['VOLUME-PAGINATION']))
	{
		$pattern = 
			escape_pattern($reference->parts['YEAR'])
			. '[a-z]?(\([0-9]{4}\))?[\.|:]?\s+(?<title>.*)\s+'
			. escape_pattern($reference->parts['VOLUME-PAGINATION']);
		
		//echo $pattern . "\n";
		
		if (preg_match('/' . $pattern . '/u', $string, $m))
		{
			$reference->matched[] = __LINE__;
			$reference->parts['TITLE'] = $m['title'];
	
			$reference->title = $m['title'];
			$reference->title = preg_replace('/\.$/u', '', $reference->title);
		}
	}	
	
	// title and journal with no ISSN
	if (!isset($reference->parts['TITLE']) && isset($reference->parts['YEAR']) && isset($reference->parts['VOLUME-PAGINATION']))
	{
		$pattern = 
			escape_pattern($reference->parts['YEAR'])
			. '[a-z]?[\.]?\s+(?<title>.*)\.'
			. '\s+(?<journal>.*)\s*'
			. escape_pattern($reference->parts['VOLUME-PAGINATION']);
		
		
		//echo $pattern . "\n";
		
		if (preg_match('/' . $pattern . '/u', $string, $m))
		{
			$reference->matched[] = __LINE__;
			$reference->parts['TITLE'] = $m['title'];
			$reference->parts['JOURNAL'] = $m['journal'];
	
			$reference->title = $m['title'];
			$reference->title = preg_replace('/\.$/u', '', $reference->title);
			$reference->journal = $m['journal'];
		}
	}		
	

	// not title yet, maybe a book
	if (isset($reference->parts['YEAR']) && !isset($reference->parts['TITLE']) && !isset($reference->parts['JOURNAL']))
	{
		$pattern = 
			escape_pattern($reference->parts['YEAR'])
			. "[a-z]?[\.|:]?\s+''(?<title>.*)''\.?\s+";
		
		if (preg_match('/' . $pattern . '/u', $string, $m))
		{
			$reference->matched[] = __LINE__;
			$reference->parts['TITLE'] = $m['title'];
	
			$reference->title = $m['title'];
			$reference->title = preg_replace('/\.$/u', '', $reference->title);

		}
	
	}

	if (isset($reference->parts['YEAR']) && !isset($reference->parts['TITLE']) && !isset($reference->parts['JOURNAL']))
	{
		$pattern = 
			escape_pattern($reference->parts['YEAR'])
			. "[a-z]?[\.|:]?\s+(?<title>.*)\.?\s+pp";
		
		if (preg_match('/' . $pattern . '/u', $string, $m))
		{
			$reference->matched[] = __LINE__;
			$reference->parts['TITLE'] = $m['title'];
	
			$reference->title = $m['title'];
			$reference->title = preg_replace('/\.$/u', '', $reference->title);

		}
	
	}


	// links
	
	// {{ZooBankRef|BC978EDA-7013-4BA0-9B7B-C6C565368347}}
	if (preg_match_all("/
		(\{\{ZooBankRef\|([^\}\}]+|(?R))*\}\})
		/x", $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['ZOOBANK'] = $m[0];		
		$reference->zoobank = strtolower($m[2][0]);
	}
	
	// {{ZooBank|pub:F2DC23DD-9FE1-4410-B0BA-21CFCE03C533}}
	if (preg_match_all("/
		(\{\{ZooBank\|pub:([^\}\}]+|(?R))*\}\})
		/x", $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['ZOOBANK'] = $m[0];		
		$reference->zoobank = strtolower($m[2][0]);
	}
	
	// [http://zoobank.org/?id=urn:lsid:zoobank.org:pub:AE1D69FB-FB3A-4ECF-B3E0-8ED5B5E9AE5B ZooBank]
	if (preg_match("/
		\[http:\/\/zoobank.org\/\?id=urn:lsid:zoobank.org:pub:(?<uuid>.*)\s+ZooBank\]
		/Uix", $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['ZOOBANK'] = $m[0];		
		$reference->zoobank = strtolower($m['uuid']);
	}
	
	
	// {{BHL|item/45394#page/393}} 
	if (preg_match_all("/
		(\{\{BHL\|([^\}\}]+|(?R))*\}\})
		/x", $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['BHL'] = $m[0];		
		$reference->bhl = strtolower($m[2][0]);
	}

	// [http://biodiversitylibrary.org/page/22131243 BHL] 
	if (preg_match('/\[http:\/\/(www\.)?biodiversitylibrary.org\/(?<bhl>(page|item)\/\d+)\s+BHL\]/i', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['BHL'] = $m[0];		
		$reference->bhl = $m['bhl'];
	}
	
	// [http://www.biodiversitylibrary.org/page/29997633#page/303/mode/1up Full article BHL (PDF)]	
	if (preg_match('/\[http:\/\/(www\.)?biodiversitylibrary.org\/(?<bhl>(page|item)\/\d+#(.*))\s+/Ui', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['BHL'] = $m[0];		
		$reference->bhl = $m['bhl'];
	}
	
	// [http://biodiversitylibrary.org/page/{{#if:{{{1|}}}|{{{1}}}|42960803}} BHL]
	if (preg_match('/\[http:\/\/(www\.)?biodiversitylibrary.org\/(?<bhl>page\/\{\{#if:\{\{\{1\|\}\}\}\|\{\{\{1\}\}\}\|\d+)\}\}\s+BHL\]/i', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['BHL'] = $m[0];		
		$reference->bhl = $m['bhl'];
		
		$reference->bhl = str_replace('{{#if:{{{1|}}}|{{{1}}}|', '', $reference->bhl);
	}
	
	// [http://www.jstor.org/stable/(?<jstor>\d+) JSTOR] 
	if (preg_match('/\[http:\/\/www.jstor.org\/stable\/(?<jstor>\d+)\s+JSTOR]/i', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['jstor'] = $m[0];		
		$reference->jstor = $m['jstor'];
	}	
	
	
	
	// {{doi|10.2307/3393034}}
	if (preg_match_all("/
		(\{\{doi\|([^\}\}]+|(?R))*\}\})
		/xi", $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['DOI'] = $m[0];		
		$reference->doi = strtolower($m[2][0]);
	}

	// wikispecies
	// <includeonly>[http://species.wikimedia.org/wiki/Template:Strand,_1906 Reference page.]</includeonly>

	if (preg_match('/\[(?<reference>http:\/\/species.wikimedia.org\/wiki\/Template:(?<wiki>.*))\s+Reference page\.?\]/i', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['WIKISPECIES'] = $m[0];		
		$reference->wikispecies = $m['wiki'];
	}
	
	// {{BibForm1|Edwards,_1925}}
	if (preg_match_all("/
		(\{\{BibForm1\|([^\}\}]+|(?R))*\}\})
		/xi", $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['BIBFORM1'] = $m[0];		
		$reference->wikispecies = $m[2][0];
	}
	
	// Handle
	// [http://scholarspace.manoa.hawaii.edu/handle/10125/1638 handle]
	if (preg_match('/\[http:\/\/scholarspace.manoa.hawaii.edu\/handle\/(?<handle>\d+\/\d+)\s+handle\]/iU', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['HANDLE'] = $m[0];		
		$reference->handle = $m['handle'];
	}		
	
	// [http://www.bioone.org/doi/abs/10.1676/04-064 Abstract]
	if (preg_match('/\[http:\/\/www.bioone.org\/doi\/abs\/(?<doi>.*)(\?.*)?\s+Abstract\]/iU', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['DOI'] = $m[0];		
		$reference->doi = urldecode($m['doi']);
	}		
	
	// cinii
	// [http://ci.nii.ac.jp/naid/110009885334/en 
	if (preg_match('/\[http:\/\/ci.nii.ac.jp\/naid\/(?<cinii>\d+)[\/]?/', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['CINII'] = $m[0];		
		$reference->cinii = $m['cinii'];
	}		
	
	// PDF
	if (preg_match('/\[(?<pdf>http[s]?:\/\/(.*))\s+PDF[\s+|\]]/U', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['PDF'] = $m[0];		
		$reference->pdf = $m['pdf'];
	}	
	
	// [http://www.mapress.com/zootaxa/2015/f/zt04060p002.pdf Full article (PDF)]	
	if (preg_match('/\[(?<pdf>http[s]?:\/\/.*\.pdf)\s+Full article(\s+\w+)? \(PDF\)\]/Ui', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['PDF'] = $m[0];		
		$reference->pdf = $m['pdf'];
	}	
	
	// ISBN
	// 978-1-77557-857-4
	if (preg_match_all('/ISBN\s+(?<isbn>[0-9]+[-]?[0-9]+[-?][0-9]+[-]?[0-9]*[-]*[xX0-9])/',  $string, $m))
	{
		$reference->matched[] = __LINE__;	
		foreach ($m[0] as $mm)
		{
			$reference->parts['ISBN'][] = $mm;
		}
	
		foreach ($m['isbn'] as $isbn)
		{
			$reference->isbn[] = $isbn;
		}
	}	


	// Quick and dirty OpenURL
	$parameters = array();
	foreach ($reference as $k => $v)
	{
		switch ($k)
		{
			case 'volume':
			case 'issue':
			case 'spage':
			case 'epage':
			case 'year':
				$parameters[] = $k . '=' . $v;
				break;
			
			case 'title':
				$parameters[] = 'atitle' . '=' . $v;
				break;	
			
			case 'journal':			
				$parameters[] = 'title' . '=' . $v;
				break;	
			
			case 'authors':
				foreach ($v as $a)
				{
					$parameters[] = 'au' . '=' . $a->name;
				}
				break;
					
			default:
				break;
		}
	}
	
	post_process($reference);

	//echo 'http://direct.biostor.org/openurl?' . join('&', $parameters) . "\n";
	
	//echo reference_to_ris($reference);
	
	if ($debug)
	{
		print_r($reference);
	}
	
	
	if ($debug)
	{
		$citeproc = reference_to_citeprocjs($reference);
		
		//echo json_format(json_encode($citeproc));
		
		echo reference_to_ris($reference);
	}
	
	
	
	
	return $reference;
			
}




?>