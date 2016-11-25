<?php

// Exploring code to parse Wikispecies citations


//$string = "*{{aut|[[Arthur Gardiner Butler|Butler, A. G.]]}}, 1876e. Revision of the heterocerous Lepidoptera of the family Sphingidae. ''Trans. zool. Soc. Lond.'' '''9''': [http://www.biodiversitylibrary.org/item/96891#page/697/mode/1up 511-644]. <includeonly>[http://species.wikimedia.org/wiki/Template:Butler,_1876e reference page]</includeonly>&nbsp;<noinclude>";

//$string = "*{{aut|Innoué, H., Kennett, R. D. and Kitching, I. J.}}, 1996. Sphingidae. ''In'' {{aut|Pinratana, A.}} (Ed.), ''Moths of Thailand.'' '''2'''. vi, 149pp., 44 pls. Chok Kai Press, Bangkok.";

//$string = "*{{a|Alan Charles Cassidy|Cassidy, A.C.}}, {{a|Michael G. Allen|Allen, M.G.}} & {{a|Tony W. Harman|Harman, T.}}, 2002. The generic position of ''[[Morwennius decoratus|Smerinthus decoratus]]'' Moore (Lepidoptera, Sphingidae). ''Trans. Lep. Soc. Japan'', '''53'''(4): 225-228 [http://ci.nii.ac.jp/naid/110007630977 PDF].<includeonly>[http://species.wikimedia.org/wiki/Template:Cassidy,_2002 reference page]</includeonly>&nbsp;<noinclude>";

//$string = "* {{a|William Warren|Warren, W.}} 1896. New species of Drepanulidae, Thyrididae, Uraniidae, Epiplemidae, and Geometridae in the Tring Museum. [[ISSN 0950-7655|''Novitates Zoologicae'']] 3: 335–419.";

//$string = "*{{aut|[[Herman Dewitz|Dewitz,  H.]]}}, 1877. Tagschmetterlinge von  Portorico. ''Stett. Ent. Zeit.'' '''38''': [http://www.biodiversitylibrary.org/item/106807#page/241/mode/1up 233-245]. <includeonly>[http://species.wikimedia.org/wiki/Template:Dewitz,H,_1877 reference page]</includeonly>&nbsp;<noinclude>";

// need to handle zootaxa template (sigh)

$string = "* {{a|Fernanda Bocalini|Bocalini, F.}} & {{a|Luís F. Silveira|Silveira, L.F.}} 2016. A taxonomic revision of the Musician Wren, ''Cyphorhinus arada'' (Aves, Troglodytidae), reveals the existence of six valid species endemic to the Amazon basin. {{zootaxa|2016|4193|3|541|564|5}}.<includeonly>[http://species.wikimedia.org/wiki/Template:Bocalini_%26_Silveira,_2016 Reference page.]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}} Find all Wikispecies pages which cite this reference.]* Date of publication: 17 November 2016* {{ZooBankRef|BC978EDA-7013-4BA0-9B7B-C6C565368347}}* No new names[[Category:Reference templates]]</noinclude>";

//$string = "* {{a|Embrik Strand|Strand, E.}} 1906. Diagnosen nordafrikanischer, hauptsächlich von Carlo Freiherr von Erlanger gesammelter Spinnen. [[ISSN 0044-5231|''Zoologischer anzeiger'']] 30: 604–637, 655–690. {{BHL|page/30259306}} <includeonly>[http://species.wikimedia.org/wiki/Template:Strand,_1906 Reference page.]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}} Find all Wikispecies pages which cite this reference.][[Category:Reference templates]]</noinclude>";

// oh deaf
$string = "*Funston, A. Michele (2001). \"[http://www.botanicus.org/page/642367 A New Species and New Combinations in ''Roldana'' (Asteraceae: Senecioneae) from Mexico.]\" ''[[ISSN 1055-3177|Novon]]'' '''11'''(3):{{{1|304-308}}}. {{doi|10.2307/3393034}}. ([[Special:WhatLinksHere/Template:10.2307/3393034|view all citings]])<noinclude>[[category:reference templates|{{PAGENAME}}]]</noinclude>
";


function escape_pattern($pattern)
{
	$pattern = str_replace('}', '\}', $pattern);
	$pattern = str_replace('[', '\[', $pattern);
	$pattern = str_replace(']', '\]', $pattern);
	$pattern = str_replace("'", "\'", $pattern);
	
	return $pattern;
}

function parse_wikispecies($string)
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
			:
			\s+(?<spage>[0-9]+)
			([-|–](?<epage>\d+))?
			)						
			/x", $string, $m))
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

			
	
	// 	Authors----------------------------------------------------------------------------
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

	// authors with aut template
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
			. '[a-z]?\.?\s+(?<title>.*)\s+'
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

	if (preg_match('/\[(?<reference>http:\/\/species.wikimedia.org\/wiki\/Template:.*)\s+Reference page\.?\]/i', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['WIKISPECIES'] = $m[0];		
		$reference->wikispecies = $m['reference'];
	}
	
	// cinii
	// [http://ci.nii.ac.jp/naid/110009885334/en 
	if (preg_match('/\[http:\/\/ci.nii.ac.jp\/naid\/(?<cinii>\d+)[\/]?/', $string, $m))
	{
		$reference->matched[] = __LINE__;		
		$reference->parts['CINII'] = $m[0];		
		$reference->cinii = $m['cinii'];
	}		
	
	print_r($reference);

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

	echo 'http://direct.biostor.org/openurl?' . join('&', $parameters) . "\n";
			
}

// examples

$refs = array(
"* {{a|William Warren|Warren, W.}} 1898. New species and genera of the families Thyrididae, Uraniidae, Epiplemidae, and Geometridae. [[ISSN 0950-7655|''Novitates Zoologicae'']] 5: 5–41.",
"* {{a|William Warren|Warren, W.}} 1898. New species and genera of the families Drepanulidae, Thyrididae, Uraniidae, Epiplemidae, and Geometridae from the Old-World regions. [[ISSN 0950-7655|''Novitates Zoologicae'']] 5: 221–258.",
"* {{a|William Warren|Warren, W.}} 1899. New species and genera of the families Drepanulidae, Thyrididae, Uraniidae, Epiplemidae, and Geometridae from the Old-World regions. [[ISSN 0950-7655|''Novitates Zoologicae'']] 6(1): 1–66."
);

$refs=array(
"* {{a|Neung-Ho Ahn|Ahn, N.H.}}; {{a|Masumi Murase|Murase, M.}}; {{a|Yutaka Arita|Arita, Y.}}, {{a|Toshiya Hirowatari|Hirowatari, T.}} 2014 A new species of the genus ''Proleucoptera'' Busck (Lepidoptera, Lyonetiidae) from the Nasu Imperial Villa, Japan [[ISSN 0024-0974|''Transactions of the Lepidopterological Society of Japan'']], '''65'''(4): 137-141. [http://ci.nii.ac.jp/naid/110009885334/en Abstract and full article] <includeonly>[http://species.wikimedia.org/wiki/Template:Ahn_et_al.,_2014 reference page]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}}find all Wikispecies pages which cite this reference]</noinclude><noinclude>[[Category:Reference templates]]</noinclude>"
);

// WTF 
$refs=array(
"{{BibForm|Strobl, G. 1898|Die Dipteren von Steiermark. IV. Theil. Nachträge zum III. Theil. ''Mittheilungen des Naturwissenschaftlichen Vereines für Steiermark'', '''34'''(1897), 277–297.|Strobl,_1898|{{{1|}}}}} {{BHL|item/45394#page/393}} {{ZooBank|pub:F2DC23DD-9FE1-4410-B0BA-21CFCE03C533}}<noinclude>{{BibForm1|Strobl,_1898}}[[Category:Sciaridae references]]</noinclude>",

"{{BibForm|Edwards, F.W. 1925|XXII. British fungus-gnats (Diptera, Mycetophilidae). With a revised generic classification of the family. ''The Transactions of the Entomological Society of London'', '''1925'''(3-4), 505–670.|Edwards,_1925|{{{1|}}}}} [http://www.online-keys.net/sciaroidea/1921_30/Edwards_1925_Myceophilidae__revised_generic_classification_~1.pdf PDF]<noinclude>{{BibForm1|Edwards,_1925}}[[Category:Sciaridae references]]</noinclude>"
);

foreach ($refs as $ref)
{
	parse_wikispecies($ref);
}



?>