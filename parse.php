<?php

require_once(dirname(__FILE__) . '/utils.php');

// Exploring code to parse Wikispecies citations


//----------------------------------------------------------------------------------------
function escape_pattern($pattern)
{
	$pattern = str_replace('}', '\}', $pattern);
	$pattern = str_replace('[', '\[', $pattern);
	$pattern = str_replace(']', '\]', $pattern);
	$pattern = str_replace("'", "\'", $pattern);
	
	return $pattern;
}

//----------------------------------------------------------------------------------------
function post_process(&$reference)
{
	$reference->title = preg_replace("/\{\{aut\|([^\}\}]+|(?R))*}}/u", '$1', $reference->title);

	$reference->title = preg_replace("/''([^'']+|(?R))''/u", '<i>$1</i>', $reference->title);
}

//----------------------------------------------------------------------------------------
function parse_wikispecies($string, $debug = false)
{
	$reference = new stdclass;
	//$reference->notes = $string;
	$reference->parts = array();


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
		if (preg_match("/
					''\]\]
					,?
					\s+
					(''')?
					(?<volume>\d+)
					(''')?
					(\((?<issue>.*)\))?
					:
					\s+(?<spage>[0-9]+)
					([-|–](?<epage>\d+))?				
					/xu", $string, $m))
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
			''(?<journal>[^'']+)''
			(\s+ser\s+(?<series>\d+))?
			,?
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
			$reference->parts['JOURNAL'] = "''" . $m['journal'] . "''";
			$reference->parts['VOLUME-PAGINATION'] = $m['volume_pagination'];

			$reference->journal = $m['journal'];
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
	
	// Journal----------------------------------------------------------------------------
	// Journal with ISSN 
	if (preg_match('/\[\[ISSN (?<issn>[0-9]{4}-[0-9]{3}([0-9]|X))\|\'\'(?<journal>.*)\'\'\]\]/', $string, $m))
	{
		$reference->matched[] = __LINE__;
		$reference->parts['JOURNAL'] = $m[0];
		
		$reference->journal = $m['journal'];
		$reference->issn = $m['issn'];
	}	

			
	
	// Authors----------------------------------------------------------------------------
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

	// authors with aut template and links
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
	
	// aut and no links
	// {{aut|Beutel, R.G.}}
	if (preg_match_all("/
		(\{\{aut\|([^\}\}]+|(?R))*\}\})
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
			$authorstrings = array();
			
			// may have multiple authors
			$authorstrings = preg_split('/\s+&\s+/', $a);
			foreach ($authorstrings as $aname)
			{
				$author = new stdclass;
				$author->name = $aname;
				$reference->authors[] = $author;
			}
		}
	
	}	

	// year
	if (preg_match('/\}\},?\s+(?<year>[0-9]{4})/', $string, $m))
	{
		$reference->matched[] = __LINE__;
		$reference->parts['YEAR'] = $m[0];
	
		$reference->year = $m['year'];
	}

	// title
	if (isset($reference->parts['YEAR']) && isset($reference->parts['JOURNAL']))
	{
		$pattern = 
			escape_pattern($reference->parts['YEAR'])
			. '[a-z]?[\.|:]?\s+(?<title>.*)\s+'
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
	
	// {{BHL|item/45394#page/393}} 
	if (preg_match_all("/
		(\{\{BHL\|([^\}\}]+|(?R))*\}\})
		/x", $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['BHL'] = $m[0];		
		$reference->bhl = strtolower($m[2][0]);
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
	
	
	
	// cinii
	// [http://ci.nii.ac.jp/naid/110009885334/en 
	if (preg_match('/\[http:\/\/ci.nii.ac.jp\/naid\/(?<cinii>\d+)[\/]?/', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['CINII'] = $m[0];		
		$reference->cinii = $m['cinii'];
	}		
	
	// PDF
	if (preg_match('/\[(?<pdf>http[s]?:\/\/(.*))\s+PDF\]/', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['PDF'] = $m[0];		
		$reference->pdf = $m['pdf'];
	}		

	if ($debug)
	{
		print_r($reference);
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

	//echo 'http://direct.biostor.org/openurl?' . join('&', $parameters) . "\n";
	
	//echo reference_to_ris($reference);
	
	post_process($reference);
	
	
	return $reference;
			
}


// test
if (1)
{
	// examples

	$refs = array(
	"* {{a|William Warren|Warren, W.}} 1898. New species and genera of the families Thyrididae, Uraniidae, Epiplemidae, and Geometridae. [[ISSN 0950-7655|''Novitates Zoologicae'']] 5: 5–41.",
	"* {{a|William Warren|Warren, W.}} 1898. New species and genera of the families Drepanulidae, Thyrididae, Uraniidae, Epiplemidae, and Geometridae from the Old-World regions. [[ISSN 0950-7655|''Novitates Zoologicae'']] 5: 221–258.",
	"* {{a|William Warren|Warren, W.}} 1899. New species and genera of the families Drepanulidae, Thyrididae, Uraniidae, Epiplemidae, and Geometridae from the Old-World regions. [[ISSN 0950-7655|''Novitates Zoologicae'']] 6(1): 1–66."
	);

	$refs=array(
	"* {{a|Neung-Ho Ahn|Ahn, N.H.}}; {{a|Masumi Murase|Murase, M.}}; {{a|Yutaka Arita|Arita, Y.}}, {{a|Toshiya Hirowatari|Hirowatari, T.}} 2014 A new species of the genus ''Proleucoptera'' Busck (Lepidoptera, Lyonetiidae) from the Nasu Imperial Villa, Japan [[ISSN 0024-0974|''Transactions of the Lepidopterological Society of Japan'']], '''65'''(4): 137-141. [http://ci.nii.ac.jp/naid/110009885334/en Abstract and full article] <includeonly>[http://species.wikimedia.org/wiki/Template:Ahn_et_al.,_2014 reference page]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}}find all Wikispecies pages which cite this reference]</noinclude><noinclude>[[Category:Reference templates]]</noinclude>"
	);

	/*
	// WTF 
	$refs=array(
	"{{BibForm|Strobl, G. 1898|Die Dipteren von Steiermark. IV. Theil. Nachträge zum III. Theil. ''Mittheilungen des Naturwissenschaftlichen Vereines für Steiermark'', '''34'''(1897), 277–297.|Strobl,_1898|{{{1|}}}}} {{BHL|item/45394#page/393}} {{ZooBank|pub:F2DC23DD-9FE1-4410-B0BA-21CFCE03C533}}<noinclude>{{BibForm1|Strobl,_1898}}[[Category:Sciaridae references]]</noinclude>",

	"{{BibForm|Edwards, F.W. 1925|XXII. British fungus-gnats (Diptera, Mycetophilidae). With a revised generic classification of the family. ''The Transactions of the Entomological Society of London'', '''1925'''(3-4), 505–670.|Edwards,_1925|{{{1|}}}}} [http://www.online-keys.net/sciaroidea/1921_30/Edwards_1925_Myceophilidae__revised_generic_classification_~1.pdf PDF]<noinclude>{{BibForm1|Edwards,_1925}}[[Category:Sciaridae references]]</noinclude>"
	);
	*/
	
//$refs=array("{{BibForm|Strobl, G. 1898|Die Dipteren von Steiermark. IV. Theil. Nachträge zum III. Theil. ''Mittheilungen des Naturwissenschaftlichen Vereines für Steiermark'', '''34'''(1897), 277–297.|Strobl,_1898|{{{1|}}}}} {{BHL|item/45394#page/393}} {{ZooBank|pub:F2DC23DD-9FE1-4410-B0BA-21CFCE03C533}}<noinclude>{{BibForm1|Strobl,_1898}}[[Category:Sciaridae references]]</noinclude>");	
	
//	$refs=array("{{BibForm|Edwards, F.W. 1925|XXII. British fungus-gnats (Diptera, Mycetophilidae). With a revised generic classification of the family. ''The Transactions of the Entomological Society of London'', '''1925'''(3-4), 505–670.|Edwards,_1925|{{{1|}}}}} [http://www.online-keys.net/sciaroidea/1921_30/Edwards_1925_Myceophilidae__revised_generic_classification_~1.pdf PDF]<noinclude>{{BibForm1|Edwards,_1925}}[[Category:Sciaridae references]]</noinclude>");

//$refs=array("* {{aut|Beutel, R.G.}}; {{aut|Roughley, R.E.}} 1988: On the systematic position of the family Gyrinidae (Coleoptera: Adephaga). ''Z. zool. Syst. Evolut.-forsch'', '''26''': 380-400.");

//$refs=array("* {{aut|Beutel, R.G.}} 1990: Phylogenetic analysis of the family Gyrinidae (Coleoptera) based on meso- and metathoracic characters. ''Quaestiones Entomologicae'', '''26''': 163-191.");

//$refs=array("{{BibForm|Stål, C. 1859|Till kännedomen om Reduviini. ''Ofversigt af Kongliga Svenska Vetenskaps-Akademiens Förhandlingar'', '''16''': 175-204, 363-378. {{BHL|item/54178#page/203/mode/1up}} |Stål, 1859|{{{1|}}}}} <noinclude>{{BibForm1|Stål, 1859}}</noinclude>");

/*
$refs=array(
"* {{aut|Monné, M.A. & M.L. Monné}}, 2011: New taxa in Neotropical Acanthocinini (Coleoptera, Cerambycidae, Lamiinae). ''Revista Brasileira de Entomologia'' '''55 (2)''': 172-178. Abstract and full article: {{doi|10.1590/S0085-56262011005000002}}.",
"* {{aut|Monné, M.L. & M.A. Monné}}, 2012: Novos táxons em Acanthocinini sul-americanos (Coleoptera, Cerambycidae). ''Revista Brasileira de Entomologia'' '''56 (3)''': 281-288. Full article: [http://www.scielo.br/pdf/rbent/v56n3/aop4512.pdf]."
);
*/

//$refs=array("*Funston, A. Michele (2001). \"[http://www.botanicus.org/page/642367 A New Species and New Combinations in ''Roldana'' (Asteraceae: Senecioneae) from Mexico.]\" ''[[ISSN 1055-3177|Novon]]'' '''11'''(3):{{{1|304-308}}}. {{doi|10.2307/3393034}}. ([[Special:WhatLinksHere/Template:10.2307/3393034|view all citings]])<noinclude>[[category:reference templates|{{PAGENAME}}]]</noinclude>");

$refs=array("* {{aut|Thomas, O.}} 1888. On a new and interesting annectant genus of Muridae, with remarks on the relations of the Old- and New-World members of the family. ''Proceedings of the Zoological Society of London'' 1888: 130–135.");

$refs=array(
"* {{aut|Thomas, O.}} 1892. On some new Mammalia from the East-Indian Archipelago. ''Annals and Magazine of Natural History'' ser 6, 9: 250–254.");

	foreach ($refs as $ref)
	{
		parse_wikispecies($ref, true);
	}
}


?>