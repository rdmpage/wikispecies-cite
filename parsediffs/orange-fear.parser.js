//----------------------------------------------------------------------------------------
// If we are adding a string to a regular expression patern then we need to escape various
// characters
function escape_pattern(pattern) {

  console.log('before=', pattern);

  pattern = pattern.replace(/{/g, '\\{');
  pattern = pattern.replace(/}/g, '\\}');

  pattern = pattern.replace(/\[/g, '\\[');
  pattern = pattern.replace(/\]/g, '\\]');

  pattern = pattern.replace(/\(/g, '\\(');
  pattern = pattern.replace(/\)/g, '\\)');

  pattern = pattern.replace(/\'/g, "\\'");
  pattern = pattern.replace(/\+/g, '\\+');
  pattern = pattern.replace(/\//g, '\\/');

  pattern = pattern.replace(/\|/g, '\\|');
  pattern = pattern.replace(/\*/g, '\\*');

  console.log('after=', pattern);

  return pattern;
}

//----------------------------------------------------------------------------------------
function output(data) {
  if (1) {
    $('#output').html(JSON.stringify(data, null, 2));
  } else {
    // emit(null, data);
  }
}


//----------------------------------------------------------------------------------------
// Parse the template from hell (Zootaxa)
function parse_zootaxa(string, citation) {

  citation.journal = 'Zootaxa';
  citation.issn = ['1175-5326'];
  console.log("string=" + string);
  var result = string.match(/zootaxa\|(\d+)\|(\d+)\|(\d+)\|(\d+)(\|(\d+))?(\|(\d+))?(\|(\d+))?/);

  // For years 2013 and above use: <tt>&#123;{zootaxa|year|volume number|fascicle number|start page|end page|article number|PDF}}</tt>
  // for open access [<tt>PDFo</tt> for no preview].<br /> For years 2012 and below use: <tt>&#123;{zootaxa|year|volume number|start page|end page}}</tt>.

  if (result) {
    //console.log('zootaxa=' + JSON.stringify(result));
    citation.parts['JOURNAL'].push('{{' + result[0] + '}}');

    switch (result[1]) {
      case "2009":
        citation.volume = result[2];
        citation.spage = result[3];
        citation.page = citation.spage;
        citation.epage = result[4];
        citation.page += '-' + citation.epage;

        // PDF link to abstract
        // http://mapress.com/zootaxa/{{{1}}}/f/z0{{{2}}}p{{{4}}}f.pdf
        citation.pdf = 'http://www.mapress.com/zootaxa/' + result[1] + '/f/z0' + citation.volume + 'p' + ("000" + citation.epage).slice(-3) + 'f.pdf';

        // PDF to actual article (there's a switch for open access or not...)

        break;

        // {{zootaxa|year|volume number|fascicle number|start page|end page|article number|PDF}}
      case '2014':
      case '2015':
      case '2016':
      case '2017':
        citation.volume = result[2];
        citation.issue = result[3];
        citation.spage = result[4];
        citation.page = citation.spage;
        citation.epage = result[6];
        citation.page += '-' + citation.epage;
        citation['article-number'] = result[8];

        // DOI
        citation.DOI = '10.11646/zootaxa.' + citation.volume + '.' + citation.issue + '.' + citation['article-number'];
        break;

      default:
        break;
    }
  }

  return citation;
}

//----------------------------------------------------------------------------------------
// {{AEMNP|53|2|633|648}}
function parse_aemnp(string, citation) {

  citation.journal = 'Acta Entomologica Musei Nationalis Pragae';
  citation.issn = ['0374-1036'];
  //console.log("string=" + string);
  var result = string.match(/AEMNP\|(\d+)\|(\d+)\|(\d+)\|(\d+)/);

  if (result) {
    //console.log('AEMNP=' + JSON.stringify(result));
    citation.parts['JOURNAL'].push('{{' + result[0] + '}}');

    citation.volume = result[1];
    citation.issue = result[2];
    citation.spage = result[3];
    citation.page = citation.spage;
    citation.epage = result[4];
    citation.page += '-' + citation.epage;

    // 53_2/53_2_633.pdf
    citation.pdf = 'http://www.aemnp.eu/PDF/' + citation.volume + '_' + citation.issue + '/' + citation.volume + '_' + citation.issue + '_' + citation.spage + '.pdf';

  }

  return citation;
}

//----------------------------------------------------------------------------------------
// parse a wiki link of the form [[<name>|<link>]]
function parse_link(string) {
  var link = {};

  var result = string.match(/^\[\[(.*)\|(.*)\]\]$/);

  if (result) {
    link.name = result[1];
    link.link = result[2];
  }

  return link;
}

//----------------------------------------------------------------------------------------
function parse_reference(string) {
 var debug = false;
  
  // citation object
  var citation = {};
  citation.author = [];
  citation.parts = {};
  citation.parts['AUTHOR'] = [];
  citation.parts['JOURNAL'] = [];
  citation.parts['VOLUME-PAGINATION'] = [];
  
  citation.matched = {};
  
  var result = null;
  var pattern = null;

  
  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    pattern = /''\]\](\s+\((\d+)\))?,?\s+(''')?(\d+)(''')?(\((.*)\))?:\s*(\d+)([-|–](\d+))?/;    
    result = string.match(pattern);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['VOLUME-PAGINATION'].push(result[0]);
      
      citation.matched['VOLUME-PAGINATION'] = pattern.toString();   

      citation.journal = result[1];
      citation.volume = result[4];

      if (result[7]) {
        citation.issue = result[7];
      }

      citation.spage = result[8];
      citation.page = citation.spage;
      if (result[10]) {
        citation.epage = result[10];
        citation.page += '-' + citation.epage;
      }
      //emit(i, result[i]);
    }
  }

  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    pattern = /\]\]''(\s+\((\d+)\))?,?\s+(''')?(\d+)(''')?(\((.*)\))?:\s*(\d+)([-|–](\d+))?/;
    result = string.match(pattern);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['VOLUME-PAGINATION'].push(result[0]);
      
      citation.matched['VOLUME-PAGINATION'] = pattern.toString();  

      citation.journal = result[1];
      citation.volume = result[4];
      citation.spage = result[8];
      citation.page = citation.spage;
      if (result[10]) {
        citation.epage = result[10];
        citation.page += '-' + citation.epage;
      }
    }
  }

  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    // '']], 34, 152–178.
    pattern = /''\]\],\s*(.*),\s*(\d+)([-|–](\d+))?/;
    result = string.match(pattern);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['VOLUME-PAGINATION'].push(result[0]);
      
      citation.matched['VOLUME-PAGINATION'] = pattern.toString();        

      citation.volume = result[1];
      citation.spage = result[2];
      citation.page = citation.spage;
      if (result[4]) {
        citation.epage = result[4];
        citation.page += '-' + citation.epage;
      }
    }
  }
  
  // '']], 126 (3): 515–532
  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    // '']], 34, 152–178.
    pattern = /''\]\],\s*(\d+)(\s*\((.*)\))?:\s*(\d+)([-|–](\d+))?/;
    result = string.match(pattern);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['VOLUME-PAGINATION'].push(result[0]);
      
      citation.matched['VOLUME-PAGINATION'] = pattern.toString();        

      citation.volume = result[1];
      
      if (result[3]) {
        citation.issue = result[3];
      }
      citation.spage = result[4];
      citation.page = citation.spage;
      if (result[5]) {
        citation.epage = result[5];
        citation.page += '-' + citation.epage;
      }
    }
  }  

  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    pattern = /''([^'][^']+)''\s+'''(\d+)\s*\((\d+)\)''':\s*(\d+)([-|–](\d+))?/;
    result = string.match(pattern);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[0]);
      
      citation.matched['VOLUME-PAGINATION'] = pattern.toString();       

      citation.journal = result[1];
      citation.volume = result[2];
      citation.issue = result[3];
      citation.spage = result[4];
      citation.page = citation.spage;
      if (result[6]) {
        citation.epage = result[6];
        citation.page += '-' + citation.epage;
      }
    }
  }
  
  // ''Invertebrate Systematics'' 17(1): 129-141.
  // ''Journal of Crustacean Biology'',  7 (3): 541–553.
  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    pattern = /''([^'][^']+)'',?\s+(\d+)(\s*\((\d+)\))?:\s*(\d+)([-|–](\d+))?/;
    result = string.match(pattern);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[0]);
      
      citation.matched['VOLUME-PAGINATION'] = pattern.toString();       

      citation.journal = result[1];
      citation.volume = result[2];
      if (result[3]) {
        citation.issue = result[4];
      }
      citation.spage = result[5];
      citation.page = citation.spage;
      if (result[7]) {
        citation.epage = result[7];
        citation.page += '-' + citation.epage;
      }
    }
  }  
  
  // https://species.wikimedia.org/w/index.php?title=Songthela&action=edit&section=4
  // '' (A), (33): 145–151.
  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    pattern = /''([^'][^']+)''\s+\(([A-Z])\),?\s+\(?(\d+)\)?:\s*(\d+)([-|–](\d+))?/;
    result = string.match(pattern);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[0]);
      
      citation.matched['VOLUME-PAGINATION'] = pattern.toString();       

      citation.journal = result[1];
      citation.series = result[2];
      citation.journal += ' ' + citation.series;
      citation.volume = result[3];
      citation.spage = result[4];
      citation.page = citation.spage;
      if (result[6]) {
        citation.epage = result[6];
        citation.page += '-' + citation.epage;
      }
    }
  }  
  
  // '' No. 2978: 1–42.
  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    pattern = /''([^'][^']+)''\s+No\.\s+\(?(\d+)\)?:\s*(\d+)([-|–](\d+))?/;
    result = string.match(pattern);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['VOLUME-PAGINATION'].push(result[0]);
      
      citation.matched['VOLUME-PAGINATION'] = pattern.toString();       

      citation.journal = result[1];
      citation.volume = result[2];
      citation.spage = result[3];
      citation.page = citation.spage;
      if (result[5]) {
        citation.epage = result[5];
        citation.page += '-' + citation.epage;
      }
    }
  }   

  // journal
  if (citation.parts['JOURNAL'].length == 0) {
    pattern = /''([^'][^']+)''[\.|,]?((\s+\w+)+)?\s*('''(\d+)'''(\((.*)\))?:\s*(\d+)[-|–](\d+))/;

    result = string.match(pattern);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[4]);
      
      citation.matched['JOURNAL'] = pattern.toString();            

      citation.journal = result[1];
      citation.volume = result[5];
      if (result[7]) {
        citation.issue = result[7];
      }

      citation.spage = result[8];
      citation.page = citation.spage;
      if (result[9]) {
        citation.epage = result[9];
        citation.page += '-' + citation.epage;
      }
      //emit(i, result[i]);
    }
  }
  
  // journal
  // ''Verslagen en Mededeelingen der Koninklijke Akademie van Wetenschappen. Afdeeling Natuurkunde'' (2), '''8''': 372–376
if (citation.parts['JOURNAL'].length == 0) {
    pattern = /''([^'][^']+)''[\.|,]?\s*\((\d+)\),?\s*('''(\d+)'''(\((.*)\))?:\s*(\d+)[-|–](\d+))/;

    result = string.match(pattern);
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[4]);
      
      citation.matched['JOURNAL'] = pattern.toString();            

      citation.journal = result[1];
      citation.series = result[2];
      citation.journal += ' series ' + citation.series;
      citation.volume = result[4];

      citation.spage = result[7];
      citation.page = citation.spage;
      if (result[8]) {
        citation.epage = result[8];
        citation.page += '-' + citation.epage;
      }
      //emit(i, result[i]);
    }
  }



  // journal
  if (citation.parts['JOURNAL'].length == 0) {
    pattern = /''([^'][^']+)''[\.|,]?\s*\((.*)\)\s*('''(\d+)''':\s*(\d+)[-|–](\d+))/;

    result = string.match(pattern);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[3]);
      
      citation.matched['JOURNAL'] = pattern.toString();      

      citation.journal = result[1];

      // non-standard CSL
      citation.series = result[2];
      citation.journal += ' series ' + citation.series;

      citation.volume = result[4];
      citation.spage = result[5];
      citation.page = citation.spage;
      if (result[6]) {
        citation.epage = result[6];
        citation.page += '-' + citation.epage;
      }
      //emit(i, result[i]);
    }
  }

  // journal
  if (citation.parts['JOURNAL'].length == 0) {
   pattern = /''(.*)''[\.|,]?\s+('''(\d+)''':\s*(\d+)[-|–](\d+))/;
   result = string.match(pattern);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[3]);
      
      citation.matched['JOURNAL'] = pattern.toString();

      citation.journal = result[1];
      citation.volume = result[3];
      citation.spage = result[4];
      citation.page = citation.spage;
      if (result[5]) {
        citation.epage = result[5];
        citation.page += '-' + citation.epage;
      }
      //emit(i, result[i]);
    }
  }

  
  // journal
  // * {{aut|Thomas, O.}} 1904. On mammals from northern Angola collected by Dr. W. J. Ansorge. ''Annals and Magazine of Natural History'' ser 7, 13: 405–421.
  if (citation.parts['JOURNAL'].length == 0) {
    pattern = /''(.*)''[\.|,]?\s+ser\.?\s+(\d+),?\s+((\d+):\s*(\d+)[-|–](\d+))/;
    result = string.match(pattern);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[4]);
      
      citation.matched['JOURNAL'] = pattern.toString();      

      citation.journal = result[1];

      if (result[2]) {
        citation.series = result[2];
        citation.journal += ' series ' + citation.series;
      }

      citation.volume = result[4];
      citation.spage = result[5];
      citation.page = citation.spage;
      if (result[6]) {
        citation.epage = result[6];
        citation.page += '-' + citation.epage;
      }
      //emit(i, result[i]);
    }
  }

  // Journal with ISSN
  if (citation.parts['JOURNAL'].length == 0) {
    pattern = /\[\[ISSN ([0-9]{4}-[0-9]{3}([0-9]|X))\|(?:\'\')(.*)(?:\'\')]\]/;
    result = string.match(pattern);
    //for (var i in result) {
    if (result) {
      citation.parts['JOURNAL'].push(result[0]);
      
      citation.matched['JOURNAL'] = pattern.toString();
      
      citation.issn = [result[1]];
      citation.journal = result[3];
   }
  }

  if (citation.parts['JOURNAL'].length == 0) {
    pattern = /(?:\'\')\[\[ISSN ([0-9]{4}-[0-9]{3}([0-9]|X))\|(.*)\]\](?:\'\')/;
    result = string.match(pattern);
    //for (var i in result) {
    if (result) {
      citation.parts['JOURNAL'].push(result[0]);
      
      citation.matched['JOURNAL'] = pattern.toString();
      
      citation.issn = [result[1]];
      citation.journal = result[3];
    }
  }
  
  // journal
  // simple journal in italics with volume not in bold
  // e.g. ''Proceedings of the Zoological Society of London'' 1896: 1012–1028.
  if (citation.parts['JOURNAL'].length == 0) {
    pattern = /''([^'][^']+)''[\.|,]?\s+((\d+):\s*(\d+)[-|–](\d+))/;
    result = string.match(pattern);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[3]);
                      
      citation.matched['JOURNAL'] = pattern.toString();

      citation.journal = result[1];
      citation.volume = result[3];
      citation.spage = result[4];
      citation.page = citation.spage;
      if (result[5]) {
        citation.epage = result[5];
        citation.page += '-' + citation.epage;
      }
      //emit(i, result[i]);
    }
  }  
  

  // templates (authors, identifiers, etc.)
  result = matchRecursive(string, "{{...}}");
  for (var i in result) {
    console.log(result[i]);
    if (match = result[i].match(/doi\|(.*)/)) {
      citation.DOI = match[1];
    }

    if (match = result[i].match(/^a\|(.*)\|(?:.*)/)) {
      var name = {};
      name.literal = match[1];
      citation.author.push(name);
      console.log(match[1]);

      citation.parts['AUTHOR'].push(match[0]);
    }

    if (match = result[i].match(/aut\|(.*)/)) {
      var name = {};

      var link = parse_link(match[1]);
      if (link.name) {
        name.literal = link.name;
        name.WIKISPECIES = link.link;
      } else {
        name.literal = match[1];
      }

      citation.author.push(name);
      citation.parts['AUTHOR'].push(match[0]);
      console.log(match[1]);
    }

    if (match = result[i].match(/auth\|(.*)\|(.*)\|[r|s]/)) {
      var name = {};
      name.given = match[1];
      name.family = match[2];
      citation.author.push(name);
      citation.parts['AUTHOR'].push(match[0]);
      console.log(match[1]);
    }

    if (match = result[i].match(/zootaxa\|(.*)$/)) {
      //alert('zootaxa');
      console.log("match=" + JSON.stringify(match));
      citation = parse_zootaxa(match[0], citation);
    }

    if (match = result[i].match(/AEMNP\|(.*)$/)) {
      //alert('zootaxa');
      console.log("match=" + JSON.stringify(match));
      citation = parse_aemnp(match[0], citation);
    }

  }

  // doi:
  pattern = /doi:(10\.\d{4,5}\/([a-z]|[A-Z]|[0-9]|\[|\]|<|>|;|-|\(|\)|\.){2,30})\b/i;
  result = string.match(pattern);
  if (result) {
    citation.DOI = result[1];
    citation.parts['DOI'] = result[0];
    citation.matched['DOI'] = pattern.toString();
    console.log(result[1]);
  }

  // year
  pattern = /\}\}[,|\.|;]?\s+[\(]?([0-9]{4})[\)]?/;
  result = string.match(pattern);
  if (result) {
    citation.year = result[1];
    citation.parts['YEAR'] = result[0];
    citation.matched['YEAR'] = pattern.toString();
    console.log(result[1]);
  }

  if (!citation.parts.TITLE && citation.parts.YEAR && citation.parts['JOURNAL'].length > 0) {
    var pattern = escape_pattern(citation.parts.YEAR)
      +
      '[a-z]?(?:\\d)?(?:\\([0-9]{4}\\))?[\.|:]?\\s+(.*)\\s+' +
      '(?:\'\')?' +
      escape_pattern(citation.parts['JOURNAL'][0]);

    result = string.match(pattern);
    if (result) {
      citation.title = result[1];
    }
  }
  
  // CiNii
  pattern = /\[http:\/\/ci.nii.ac.jp\/naid\/(\d+)[\/]?/;
  result = string.match(pattern);
  if (result) {
    citation.CINII = result[1];
    citation.parts['CINII'] = result[0];
    citation.matched['CINII'] = pattern.toString();
    console.log(result[1]);
  }  
  
  // Handle
  // [http://digitallibrary.amnh.org/dspace/handle/2246/5065 Handle.]
  pattern = /\[http:\/\/(?:.*)\/(\d+\/\d+)\s+(Handle\.)?(\(Abstract\))?/i;
  result = string.match(pattern);
  if (result) {
    citation.HANDLE = result[1];
    citation.parts['HANDLE'] = result[0];
    citation.matched['HANDLE'] = pattern.toString();
    console.log(result[1]);
  }  
  
  // ISBN
  pattern = /\{\{ISBN\|((\d+\-)+[0-9X])\}\}/i;
  result = string.match(pattern);
  if (result) {
    citation.ISBN = result[1];
    citation.parts['ISBN'] = result[0];
    citation.matched['ISBN'] = pattern.toString();
    console.log(result[1]);
  }   

  // JSTOR
  pattern = /\[http[s]?:\/\/(?:www\.)?jstor.org\/(?:pss|stable)\/(\d+)/;
  result = string.match(pattern);
  if (result) {
    citation.JSTOR = result[1];
    citation.parts['JSTOR'] = result[0];
    citation.matched['JSTOR'] = pattern.toString();
    console.log(result[1]);
  }    
 
  // Wikispecies link
  pattern = /\[http:\/\/species.wikimedia.org\/wiki\/(Template:.*)\s*Reference page\.?\]/i;
  result = string.match(pattern);
  if (result) {
    citation.WIKISPECIES = result[1];
    citation.WIKISPECIES = citation.WIKISPECIES.replace(/\s+$/, '');
    citation.parts['WIKISPECIES'] = result[0];
    citation.matched['WIKISPECIES'] = pattern.toString();
    console.log(result[1]);
  }    
  
  // ZooBank
  // {{ZooBankRef|BB30465E-865B-4938-98B1-378EF64F31E3}}
  pattern = /\{\{ZooBankRef\|(.*)\}\}/i;
  result = string.match(pattern);
  if (result) {
    citation.ZOOBANK = result[1];
    citation.parts['ZOOBANK'] = result[0];
    citation.matched['ZOOBANK'] = pattern.toString();
    console.log(result[1]);
  } 
  
  // cleanup

  if (citation.title) {
    //citation.title.replace(/\s''/g, ' <i>');
    //citation.title.replace(/\\'\\'(\b)/g, '</i>$1');

    //r = citation.title.match(/([\s|,|\.])''/g);
    //if (r) { alert(JSON.stringify(r)); }

    // italics
    citation.title = citation.title.replace(/([\s|,|\.|\(])''/g, '$1<i>');
    citation.title = citation.title.replace(/^''/g, '<i>');
    citation.title = citation.title.replace(/''([\s|,|\.|:|\)])/g, '</i>$1');
    citation.title = citation.title.replace(/''$/g, '</i>');
    citation.title = citation.title.replace(/\.$/g, '');
    citation.title = citation.title.replace(/\. -$/, '');
  }
  
  if (citation.epage) {
    citation.epage = citation.epage.replace(/^–/, '');
  } 
                                            
  if (citation.page) {
    citation.page = citation.page.replace(/-–/, '-');
  }                                             

  if (!debug) {
    delete citation.parts;
    delete citation.matched;
  }

  // citeproc conversion
  if (citation.year) {
    citation.issued = {};
    citation.issued['date-parts'] = [];
    citation.issued['date-parts'].push([citation.year]);

  }
  if (citation.journal) {
    citation['container-title'] = citation.journal;
  }


  citation.id = "ITEM-1";
  //citation.type = "journal-article";
  citation.type = "article-journal";

  return citation;
}

function format_citation(citation) {

  bibdata = {};
  bibdata["ITEM-1"] = citation;

  // This defines the mechanism by which we get hold of the relevant data for
  // the locale and the bibliography. 
  // 
  // In this case, they are pretty trivial, just returning the data which is
  // embedded above. In practice, this might involving retrieving the data from
  // a standard URL, for instance. 
  var sys = {
    retrieveItem: function(id) {
      return bibdata[id];
    },

    retrieveLocale: function(lang) {
      return locale[lang];
    }
  }

  // instantiate the citeproc object
  var citeproc = new CSL.Engine(sys, chicago_author_date);

  // This is the citation object. Here, we have hard-coded this, so it will only
  // work with the correct HTML. 
  var citation_object = {
    // items that are in a citation that we want to add. in this case,
    // there is only one citation object, and we know where it is in
    // advance. 
    "citationItems": [{
      "id": "ITEM-1"
    }],
    // properties -- count up from 0
    "properties": {
      "noteIndex": 0
    }

  }

  citeproc.appendCitationCluster(citation_object)[0][1];

  $('#csl').html(citeproc.makeBibliography()[1][0]);

}

  function run_tests () {
  
    var html = '';
    html += '<table>';  
    
	var keys = ['author', 'title', 'container-title', 'volume', 'issue', 'page', 'year', 'DOI', 'CINII', 'HANDLE', 'WIKISPECIES'];
	
	var tests = [];
	
	// a test
	tests.push({
	  input : "* {{aut|Bleeker, P.}} 1851. Visschen van Billiton. ''Natuurkundig Tijdschrift voor Nederlandsch Indië'' 1: 478–479.",
	  output : {
"author": [
{
  "literal": "Bleeker, P."
}
],
"journal": "Natuurkundig Tijdschrift voor Nederlandsch Indië",
"volume": "1",
"spage": "478",
"page": "478-479",
"epage": "479",
"year": "1851",
"title": "Visschen van Billiton",
"issued": {
"date-parts": [
  [
	"1851"
  ]
]
},
"container-title": "Natuurkundig Tijdschrift voor Nederlandsch Indië",
"id": "ITEM-1",
"type": "article-journal"
}
	});
    
  tests.push({
    input : "* {{aut|[[František Moravec|Moravec, F.]]}} & {{aut|[[Kazuya Nagasawa|Nagasawa, K.]]}} (2002) Redescription of ''Raphidascaris gigi'' Fujita, 1928 (Nematoda: Anisakidae), a parasite of freshwater fishes in Japan. ''Systematic Parasitology'' 52: 193–198. {{doi|10.1023/a:1015785602488}}",
    output : {
  "author": [
    {
      "literal": "František Moravec",
      "WIKISPECIES": "Moravec, F."
    },
    {
      "literal": "Kazuya Nagasawa",
      "WIKISPECIES": "Nagasawa, K."
    }
  ],
  "journal": "Systematic Parasitology",
  "volume": "52",
  "spage": "193",
  "page": "193-198",
  "epage": "198",
  "DOI": "10.1023/a:1015785602488",
  "year": "2002",
  "title": "Redescription of Raphidascaris gigi Fujita, 1928 (Nematoda: Anisakidae), a parasite of freshwater fishes in Japan",
  "issued": {
    "date-parts": [
      [
        "2002"
      ]
    ]
  },
  "container-title": "Systematic Parasitology",
  "id": "ITEM-1",
  "type": "article-journal"
}
    
  });
    
  tests.push({
    input : "* {{a|Norman I. Platnick|Platnick, N.I.}} 1990. Spinneret Morphology and the Phylogeny of Ground Spiders (Araneae, Gnaphosoidea). ''[[ISSN 0003-0082|American Museum Novitates]]'' No. 2978: 1–42. [http://digitallibrary.amnh.org/dspace/handle/2246/5065 Handle.]",
    output : {
  "author": [
    {
      "literal": "Norman I. Platnick",
      "family": "",
      "given": ""
    }
  ],
  "journal": "American Museum Novitates",
  "volume": "2978",
  "spage": "1",
  "page": "1-42",
  "epage": "42",
  "issn": [
    "0003-0082"
  ],
  "year": "1990",
  "title": "Spinneret Morphology and the Phylogeny of Ground Spiders (Araneae, Gnaphosoidea)",
  "HANDLE": "2246/5065",
  "issued": {
    "year": 1990
  },
  "container-title": "American Museum Novitates",
  "id": "ITEM-1",
  "type": "article-journal",
  "page-first": "1"
}
  });

    
    
    	
  //-------------------------------------------------------------------------
	// parse the test cases
	var parsing_results = [];	
	for (var i in tests) {
		parsing_results[i] = parse_reference(tests[i].input);
	}
	
	// make comparison
	for (var i in tests) {
		var ok = true;
    
    
	
		for (var k in keys) {
			var key = keys[k];
			var test_value = tests[i][keys[k]];
			
			if (test_value) {
				if (typeof test_value === 'object') {
					test_value = JSON.stringify(test_value);
				}
				
				if (parsing_results[i][key]) {
					var parsed_value = parsing_results[i][key];
					if (typeof parsed_value === 'object') {
						parsed_value = JSON.stringify(parsed_value);
					}  
					
					if (test_value != parsed_value) {
						ok = false; // values are different, so objects are not the same
					}
				} else {
					ok = false; // parsed result doesn't have this key, so objects are different
				}
			}
		}
    
    html += '<tr'; 
    
    if (ok) {
      html += ' style="background-color:green;color:white;"';
    } else {
      html += ' style="background-color:red;color:white;"';      
    }
    html += '>';
    html += '<td>'; 
    html += i + '  ' + tests[i].input;
    html += '</td>';      
    
    html += '<td>';  
		if (ok) {
      html += "passed";
		} else {
      html += "failed";
		}  
    html += '</td>';    
    html += '</tr>';
	}
    
    html += '</table>';
    
    $('#tests').html(html);
}

