<?php

require_once(dirname(__FILE__) . '/parse.php');

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

$refs=array(
"* {{aut|Thomas, O.}} 1906. Notes on South American rodents. II. On the allocation of certain species hitherto referred respectively to ''Oryzomys'', ''Thomasomys'', and ''Rhipidomys''. ''Annals and Magazine of Natural History'' ser 7, 18: 442–448.");

$refs=array(
"* {{auth|O.|Thomas|r}} 1915: New African rodents and insectivores, mostly collected by Dr. C. Christy for the Congo Museum. [[ISSN 0022-2933|''Annals and magazine of natural history'']] (8), '''16'''(92): 146-152. {{doi|10.1080/00222931508693699}} [http://biodiversitylibrary.org/page/22131243 BHL] <includeonly>[http://species.wikimedia.org/wiki/Template:Thomas,_1915 reference page]</includeonly>&nbsp;<noinclude>");

$refs=array(
"* {{a|Oldfield Thomas|Thomas, O.}} 1912. Three small mammals from SouthAmerica. ''Annals and Magazine of Natural History'' ser. 8, 9:408-410.");

$refs=array(
"* {{aut|Thomas, O.}} 1904 [1905]. On ''Hylochoerus'', the forest pig of Central Africa. ''Proceedings of the Zoological Society of London'' 1904(2): 193–199.");

$refs=array("* {{aut|Thomas, O.}} 1903. LXXII.— On the species of the genus ''Rhinopoma''. [[ISSN 0022-2933|''Annals and magazine of natural history'']], 7th ser. 11: 496–499. {{BHL|page/19368047}}");

$refs=array("* {{a|William Warren|Warren, W.}} 1902. New African Drepanulidae, Thyrididae, Epiplemidae, and Geometridae in the Tring Museum. [[ISSN 0950-7655|''Novitates Zoologicae'']] '''9''': 487-536. ");

$refs = array("* {{aut|Thomas, O.}} 1902. On some new forms of ''Otomys''. Annals and Magazine of Natural History, ser. 7, 10: 311–314.");

// * {{aut|Thomas, O.}} 1888. Catalogue of the Marsupialia and Monotremata in the collection of the British Museum (Natural History). ''British Museum'' (Natural History), London, 401 pp., 33 pls.

$refs=array("* {{aut|Salazar-Bravo, J.}}, {{aut|Pardiñas, U.F.J.}} & {{aut|D'Elía, G.}} 2013. A phylogenetic appraisal of Sigmodontinae (Rodentia, Cricetidae) with emphasis on phyllotine genera: systematics and biogeography. ''[[ISSN 0300-3256|Zoologica Scripta]]''  42(3): 250–261. {{doi|10.1111/zsc.12008}}");

$refs=array("* {{aut|D’Elía, G.}}, {{aut|Pardiñas, U.F.J.}}, {{aut|Teta, P.}} & {{aut|Patton, J.L.}} 2007. Definition and diagnosis of a new tribe of sigmodontine rodents (Cricetidae: Sigmodontinae), and a revised classification of the subfamily. ''[[ISSN 0717-652X|Gayana]]'' 71: 187–194.");

$refs=array("* {{aut|D’Elía, G.}}, {{aut|Pardiñas, U.F.J.}}, {{aut|Teta, P.}} & {{aut|Patton, J.L.}} 2007. Definition and diagnosis of a new tribe of sigmodontine rodents (Cricetidae: Sigmodontinae), and a revised classification of the subfamily. ''[[ISSN 0717-652X|Gayana]]'' 71: 187–194. {{doi|10.4067/S0717-65382007000200007}}");

$refs=array("* {{a|Pablo Rodrigues Gonçalves|Gonçalves, P.R.}} & {{a|João A. de Oliveira|Oliveira, J.A.}} 2014. An integrative appraisal of the diversification in the Atlantic forest genus ''Delomys'' (Rodentia: Cricetidae: Sigmodontinae) with the description of a new species. {{zootaxa|2014|3760|1|1|38|1}} <includeonly>");

$refs=array("* {{aut|Almeida, A.O. & A. Anker}}, 2011: ''Alpheus rudolphi'' spec. nov., a new snapping shrimp from northeastern Brazil (Crustacea: Decapoda: Alpheidae). ''Zoologische Mededelingen'' '''85 (1)''': 1-10.");

$refs=array("* {{aut|Banner, A.H.}}; {{aut|Banner, D.M.}} 1980/1: Contributions to the knowledge of the alpheid shrimp of the Pacific Ocean. Part XIX. On ''Alpheus randalli'', a new species of the Edwardsii group found living in association with a gobiid fish. [[ISSN 0030-8870|''Pacific science'']], '''34'''(4): 401–405. [http://scholarspace.manoa.hawaii.edu/handle/10125/1638 handle]");

$refs=array("* {{aut|McClure, M.R.}} 1995: ''Alpheus angulatus'', a new species of snapping shrimp from the Gulf of Mexico and northwestern Atlantic, with a redescription of ''A. heterochaelis'' Say, 1818 (Decapoda: Caridea: Alpheidae). [[ISSN 0006-324X|''Proceedings of the Biological Society of Washington'']], '''108'''(1): 84-97. [http://decapoda.nhm.org/pdfs/26177/26177.pdf PDF] <includeonly>[http://species.wikimedia.org/wiki/Template:McClure,_1995 reference page]</includeonly>&nbsp;<noinclude>");

$refs=array("* {{aut|Vacelet, J.}} , {{aut|Pérez, T.}} , {{aut|Allewaert, C.}} ; {{aut|Reveillaud, J.}} , {{aut|Banaigs, B.}} & {{aut|Vanreusel, A.}}, 2012: Relevance of an integrative approach for taxonomic revision in sponge taxa: case study of the shallow-water Atlanto-Mediterranean Hexadella species (Porifera: Ianthellidae: Verongida). Invertebrate Systematics, 26(3): 230-248. Abstract: {{doi|10.1071/IS11044}}");

$refs=array("*{{a|Ernst Suffert|Suffert, E.}} 1904. Neue afrikanische Tagfalter aus dem kön. zool. Museum, Berlin, und meiner Sammlung. ''Deutsche entomologische Zeitschrift [Iris]'' 17: 12–107. [http://biodiversitylibrary.org/page/12683007 BHL].  <includeonly>[http://species.wikimedia.org/wiki/Template:Suffert,E,_1904a reference page]</includeonly>&nbsp;<noinclude>");

$refs=array("* {{a|Magdi S. el-Hawagry|el-Hawagry, M.S.}}, {{a|Mahmoud S. Abdel-Dayem|Abdel-Dayem, M.S.}}, {{a|Ali A. Elgharbawy|Elgharbawy, A.A.}} & {{a|Hathal M. Al-Dhafer|al-Dhafer, H.M.}} 2016. A preliminary account of the fly fauna in Jabal Shada al-A’la Nature Reserve, Saudi Arabia, with new records and biogeographical remarks (Diptera, Insecta). [[ISSN 1313-2989|''ZooKeys'']] 636: 107-139. {{doi|10.3897/zookeys.636.9905}}. <includeonly>[http://species.wikimedia.org/wiki/Template:El-Hawagry_et_al.,_2016 Reference page.]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}}Find all Wikispecies pages which cite this reference.]* Date of publication: 24 November 2016* {{ZooBankRef|A09BEDD8-A29C-4885-AF8D-A250B26B073D}}* No new names[[Category:Reference templates]]</noinclude>");

// * ''Proceedings of the Biological Society of Washington.'' 38 p.91

$refs=array("* {{a|Luiz Fernando Ribeiro|Ribeiro, L.F.}}, {{a|Marcos R. Bornschein|Bornschein, M.R.}}, {{a|Ricardo Belmonte-Lopes|Belmonte-Lopes, R.}}, {{a|Carina R. Firkowski|Firkowski, C.R.}}, {{a|Sergio A.A. Morato|Morato, S.A.A.}} & {{a|Marcio R. Pie|Pie, M.R.}} 2015. Seven new microendemic species of ''Brachycephalus'' (Anura: Brachycephalidae) from southern Brazil. ''PeerJ'' 3 (e1011): 1–35. {{doi|10.7717/peerj.1011}} [https://peerj.com/articles/1011/ Full article] <includeonly>");

$refs=array("* {{aut|Savage, J. M. & C. Guyer}} 1993: Infrageneric classification and species composition of the anole genera, ''Anolis, Ctenonotus, Dactyloa, Norops'' and ''Semiurus'' (Sauria: Iguanidae) ''Amphibia-Reptilia'', '''10'''(2): 105-116.");

$refs=array("* {{aut|Smith, H.M., Burley, E, & Fritts, T.}} 1968: A new anisolepid ''Anolis'' (Reptilia: Lacertilia) from México. ''Journal of Herpetology'', '''2''': 147-151.
");

$refs = array("* {{a|George Albert Boulenger|Boulenger, G.A.}} 1882. ''Catalogue of the Batrachia Salientia s. Ecaudata in the Collection of the British Museum''. Second Edition. London: Taylor and Francis. Pp. i–viii + 1–127, with pls. i–ix. {{BHL|page/8398324}}");

$refs=array("* {{aut|Boulenger, G.A.}} 1893. Concluding report on the reptiles and batrachians obtained in Burma by Signor L. Fea dealing with the collection made in Pegu and the Karin Hills in 1887–88. ''Annali del Museo Civico di Storia Naturale di Genova'' (2) 13: 304–347. [http://biodiversitylibrary.org/page/29845507 BHL]");

$refs=array("* {{auth|A.|Anker|r}} 2010: On two snapping shrimps, ''Alpheus baccheti'' n. sp. and ''A. coetivensis'' Coutière from the Tuamotu Islands, French Polynesia (Crustacea, Decapoda). {{zootaxa|2010|2492|49|62}}");

$refs=array("* {{auth|A.|Anker|r}}; {{aut|Hurt, C.}}; {{aut|Knowlton, N.}} 2009: Description of cryptic taxa within the ''Alpheus bouvieri'' A. Milne-Edwards, 1878 and ''A. hebes'' Kim and Abele, 1988 species complexes (Crustacea: Decapoda: Alpheidae). {{zootaxa|2009|2153|1|23}}");

$refs=array("* {{a|Tomoyuki Komai|Komai, T.}} 2015. A new species of the snapping shrimp genus ''Alpheus'' (Crustacea: Decapoda: Caridea: Alpheidae) from Japan, associated with the innkeeper worm ''Ikedosoma elegans'' (Annelida: Echiura: Echiuridae). {{zootaxa|2015|4058|1|101|111|5}}. ");

$refs=array("* {{aut|[[Arthur Anker|Anker, A.]]}}; {{aut|[[Tomoyuki Komai|Komai, T.]]}}; {{aut|[[Ivan N. Marin|Marin, I.N.]]}} 2015: A new echiuran-associated snapping shrimp (Crustacea: Decapoda: Alpheidae) from the Indo-West Pacific. {{zootaxa|2015|3914|4|441|455|4}}. <includeonly>[http://species.wikimedia.org/wiki/Template:Anker_et_al.,_2015 reference page]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}} find all Wikispecies pages which cite this reference]==Nomenclatural acts==* Date of publication: 29 January, 2015* {{ZooBankRef|7B4A8A8B-DABD-4DF4-92B0-C9F61110C10B}} (erroneously gives year as 2014)===New names (1)===* ''[[Alpheus echiurophilus]]''[[Category:Reference templates]]</noinclude>");

$refs=array("* {{aut|Boulenger, G.A.}} 1909. Catalogue of the fresh-water fishes of Africa in the British Museum (Natural History). London. 1: i–xii + 1–373. [http://biodiversitylibrary.org/page/9384492 BHL]");

$refs=array("* {{aut|Theodorova (Feodorova), T.A.}} 2011. Phylogenetic relations of the South African species of ''Caroxylon'' sect. ''Caroxylon'' and ''Tetragonae'' (Chenopodiaceae) based on the morphology and nrITS Sequences. ''Turczaninowia'' 14(3): 74-75 (69–76, russ. with engl. summary).");
$refs=array(
"* {{aut|Köhler, Gunther; Ponce, Marcos; Sunyer, Javier & Batista, Abel}} 2007: Four new species of anoles (genus ''Anolis'') from the Serranía de Tabasará, west-central Panama (Squamata: Polychrotidae). ''Herpetologica'', '''63'''(3): 375-391. [http://www.jstor.org/stable/4497970 JSTOR]",
"* {{aut|Holáňová, V.}}; {{aut|Rehák, I.}}; {{aut|Frynta, D.}} 2012: ''Anolis sierramaestrae'' sp. nov. (Squamata: Polychrotidae) of the “''chamaeleolis''” species group from eastern Cuba. [[ISSN 1211-376X|''Acta Societatis Zoologicae Bohemicae'']], '''76'''(1–2): 45-52. ");

$refs=array("* {{aut|Ayala-Varela, F.P.}}; {{aut|Torres-Carvajal, O.}} 2010: A new species of dactyloid anole (Iguanidae, Polychrotinae, ''Anolis'') from the southeastern slopes of the Andes of Ecuador. [http://pensoftonline.net/zookeys/index.php/journal/index ''ZooKeys'',] '''53''': 59–73. ISSN: 1313-2970 (online) ISSN: 1313-2989 (print) {{doi|10.3897/zookeys.53.456}}");

$refs=array("* {{aut|Köhler, G. & J.R. McCranie}} 2001: Two new species of anoles from northern Honduras (Squamata: Polychrotidae). ''Senckenbergiana biologica'' '''81''': 235-245.");

$refs=array("* {{a|Lewis Leonard Forman|Forman, L.L.}} 1975. The tribe Triclisieae Diels in Asia, the Pacific and Australia. The Menispermaceae of Malesia and adjacent areas: VIII. ''[[ISSN 0075-5974|Kew Bulletin]]'' 30: 77–100. [http://www.jstor.org/stable/4102876 JSTOR] <includeonly>[http://species.wikimedia.org/wiki/Template:Forman,_1975 Reference page]</includeonly>");

$refs=array("* {{a|Adolf Engler|Engler, A.}} 1893. Ochnaceae africanae. ''[[ISSN 0006-8152|Botanische Jahrbücher für Systematik, Pflanzengeschichte und Pflanzengeographie]]'' 17: 75–82. [http://biodiversitylibrary.org/page/{{#if:{{{1|}}}|{{{1}}}|42960803}} BHL] <includeonly>[http://species.wikimedia.org/wiki/Template:Engler,_1893 reference page]</includeonly>&nbsp;<noinclude>");

$refs=array("* {{aut|[[F.W. Gess|Gess, F.W.]]}} 2011: The genus ''Quartinia'' Ed. André, 1884 (Hymenoptera, Vespidae, Masarinae) in Southern Africa. Part IV. New and little known species with complete venation. [[ISSN 1070-9428|''Journal of Hymenoptera research'']], '''21''': 1-39. {{doi|10.3897/JHR.21.870}} [http://zoobank.org/?id=urn:lsid:zoobank.org:pub:AE1D69FB-FB3A-4ECF-B3E0-8ED5B5E9AE5B ZooBank] ");

$refs = array("* {{a|Hsuan-Ching Ho|Ho, H-C.}}, {{a|David G. Smith|Smith, D.G.}}, {{a|John E. McCosker|McCosker, J.E.}} & {{a|Wouter Holleman|Holleman, W.}} 2015. Systematics and biodiversity of eels (orders Anguilliformes and Saccopharyngiformes) of Taiwan. {{zootaxa|2015|4060|1|1|189|1}}. [http://www.mapress.com/zootaxa/2015/f/zt04060p002.pdf Full article (PDF)] ISBN 978-1-77557-857-4 (paperback); ISBN 978-1-77557-858-1 (Online edition)  <includeonly>&nbsp;[http://species.wikimedia.org/wiki/Template:Ho_et_al.,_2015c reference page]</includeonly>&nbsp;<noinclude> ** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}} find all Wikispecies pages which cite this reference] * Date of publication: 24 December 2015 * {{ZooBankRef|31544246-0856-4985-B809-03CD73B858D7}}");

$refs=array(
"* {{aut|McCosker, J.E.}} 2006. A new species of sand eel, ''Yirrkala moorei'' (Anguilliformes: Ophichthidae), from the South Pacific. ''Proceedings of the California Academy of Sciences'' (Series 4) 57: 373–377.",
"* {{aut|McCosker, J.E.}} 2007. ''Luthulenchelys heemstraorum'', a new genus and species of snake eel (Anguilliformes: Ophichthidae) from KwaZulu-Natal, with comments on ''Ophichthus rutidoderma'' ({{aut|Bleeker}}, 1853) and its synonyms. [[ISSN 1684-4130|''Smithiana - Publications in Aquatic Biodiversity, Bulletin'']] 7: 3–7. [http://www.saiab.ac.za/products/Smithiana_bul7.pdf PDF of entire bulletin]",
"* {{aut|McCosker, J.E.}} 2008. ''Trachyscorpia osheri'' and ''Idiastion hageyi'', two new species of deepwater scorpionfishes (Scorpaeniformes: Sebastidae, Scorpaenidae) from the Galápagos Islands. ''Proceedings of the California Academy of Sciences'' (Series 4) 59(3): 113–123.",
"* {{aut|McCosker, J.E.}} 2010. Deepwater Indo-Pacific species of the snake-eel genus ''Ophichthus'' (Anguilliformes: Ophichthidae), with the description of nine new species. ''Zootaxa'' 2505: 1–39."
);

$refs=array(
"* {{aut|McCosker, J.E.}} 2006. A new species of sand eel, ''Yirrkala moorei'' (Anguilliformes: Ophichthidae), from the South Pacific. ''Proceedings of the California Academy of Sciences'' (Series 4) 57: 373–377.",
"* {{aut|McCosker, J.E.}} 2008. ''Trachyscorpia osheri'' and ''Idiastion hageyi'', two new species of deepwater scorpionfishes (Scorpaeniformes: Sebastidae, Scorpaenidae) from the Galápagos Islands. ''Proceedings of the California Academy of Sciences'' (Series 4) 59(3): 113–123.");

$refs=array(
"* {{a|John E. McCosker|McCosker, J.E.}} & {{a|Hsuan-Ching Ho|Ho, H.-C.}} 2015. New species of the snake eels ''Echelus'' and ''Ophichthus'' (Anguilliformes: Ophichthidae) from Taiwan. {{zootaxa|2015|4060|1|71|85|11}}. [http://www.mapress.com/zootaxa/2015/f/z04060p085f.pdf Preview (PDF)] [http://www.mapress.com/zootaxa/2015/f/zt04060p085.pdf Full article (PDF)] <includeonly>&nbsp;[http://species.wikimedia.org/wiki/Template:McCosker_%26_Ho,_2015 reference page]</includeonly>&nbsp;<noinclude>");

$refs=array("* {{a|Ernst Hartert|Hartert, E.}} 1931. Types of birds in the Tring Museum. D. Gregory M. Mathews's types of Australian birds. III. [[ISSN 0950-7655|''Novitates Zoologicae'']] 37. 35–52.");

$refs=array(
"* {{a|Ernst Hartert|Hartert, E.}} 1901. Aus den Wanderjahren eines Naturforschers. V. Kapital. Die Fauna der Canarischen Inseln. [[ISSN 0950-7655|''Novitates Zoologicae'']] 8. 304–335. {{BHL|item/22660#page/345/mode/1up}}",
"* {{a|Ernst Hartert|Hartert, E.}} 1901. William Doherty. Obituary. [[ISSN 0950-7655|''Novitates Zoologicae'']] 8. 494–506.",
"* {{a|Ernst Hartert|Hartert, E.}} 1901. [Mr. Ernst Hartert exhibited and described–]. Bulletin of the British Ornithologists' Club 11. 71."
);

$refs=array(
"* {{a|Ernst Hartert|Hartert, E.}}, {{aut|Paludan, K.}}, {{aut|Lord Rothschild}} & {{aut|Stresemann, E.}}. 1936. Die Vögel des Weyland-Gebirges und seines Vorlandes. Mitteilungen aus dem Zoologisches Museum in Berlin 21. 165–240."
);

$refs=array(
"* {{a|Bret M. Whitney|Whitney, B.M.}} & {{a|José Álvarez Alonso|Alonso, J.A}} (2005). A new species of gnatcatcher from white-sand forests of northern amazonian peru with revision of the ''Polioptila guianensis'' complex. ''The Wilson Bulletin'' 117(2):113-127. doi:10.1676/04-064. ISSN 0043-5643. [http://www.bioone.org/doi/abs/10.1676/04-064 Abstract]<includeonly>[http://species.wikimedia.org/wiki/Template:Whitney_&_Alonso,_2005 reference page]</includeonly>&nbsp;<noinclude>**[http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}} find all Wikispecies pages which cite this reference]* Date of publication: 2005* New name: ''[[Polioptila clementsi]]''[[Category:Reference templates]]</noinclude>"
);

$refs=array(
"* {{a|Eugène Simon|Simon, E.}} 1906. Description d'une nouvelle espèce de Phoneyusa découverte au Fouta-Djalon par M. A. Chevalier. ''Bulletin du Museum D'Histoire Naturelle, Paris'', 1906: 189–190.",
"* {{a|Eugène Simon|Simon, E.}} 1911. Araneae. In Jeannel, R. & P. de Peyerimhof, Récoltes entomologiques dans les Reni-Snassen (Maroc orientale). ''Annales de la Société entomologique de France'', 80: 414–419."
);

$refs=array(
"* {{a|Eugène Simon|Simon, E.}} 1905. Etude sur les arachnides recueillis en Patagonie par le Dr. Fillipo Silvestri. ''Boll. Mus. zool. anat. comp. Torino'' 20(511): 1–17."
);

$refs=array(
"* {{a|Eugène Simon|Simon, E.}} 1911. Araneae et Opiliones (Troisième série). In Biospeologica. XXIII. Arch. zool. expér. gen. (5) 9(2): 177–206."
);

$refs=array(
"* {{auth|P.|Bleeker|r}}, 1863: Mémoire sur les poissons de la côte de Guinée. ''Natuurkundige Verhandelingen van de Hollandsche Maatschappij der Wetenschappen te Haarlem'' (Ser. 2) v. 18 (1862): 1–136, Pls. 1–28.  <includeonly>[http://species.wikimedia.org/wiki/Template:Bleeker,_1863a reference page]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}} find all Wikispecies pages which cite this reference][[Category:Reference templates]]</noinclude>");

$refs=array("*{{aut|[[Hans Fruhstorfer|Fruhstorfer, H.]]}}, 1916e. Neue Lokalrassen indischer Tagfalter. ''Ent. Rundsch''. '''33'''(5): [http://archive.org/stream/entomologischeru3234191517stut#page/n117/mode/1up 24-25]. <includeonly>[http://species.wikimedia.org/wiki/Template:Fruhstorfer,_1916e reference page]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}} find all Wikispecies pages which cite this reference][[Category:Reference templates]]</noinclude>");
$refs=array("{{aut|[[Hans Fruhstorfer|Fruhstorfer, H.]]}}, 1919d. Revision der Gattung ''[[Poritia]]'' auf Grund der Morphologie der Generatsionsorgane. ''Arch. Naturgesch''. (A) '''83''' (2): [http://biodiversitylibrary.org/item/47767#page/269/mode/1up 77-101]. <includeonly>[http://species.wikimedia.org/wiki/Template:Fruhstorfer,_1919d reference page]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}} find all Wikispecies pages which cite this reference][[Category:Reference templates]]</noinclude>");
$refs=array("*{{aut|[[Hans Fruhstorfer|Fruhstorfer, H.]]}}, 1919b. ''[[Spalgis]]'' eine bicontinentale Lycaenid gattung. ''Arch. Naturgesch''. '''83''' (A) (1): [http://biodiversitylibrary.org/item/47767#page/83/mode/1up 73-76], 1 fig. <includeonly>[http://species.wikimedia.org/wiki/Template:Fruhstorfer,_1919b reference page]</includeonly>&nbsp;<noinclude>** [http://species.wikimedia.org/wiki/Special:WhatLinksHere/Template:{{BASEPAGENAMEE}} find all Wikispecies pages which cite this reference][[Category:Reference templates]]</noinclude>");
foreach ($refs as $ref)
{
	parse_wikispecies($ref, true);
}



?>