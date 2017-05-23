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
    emit(null, data);
  }
}


//----------------------------------------------------------------------------------------
// Parse the template from hell (Zootaxa)
function parse_zootaxa(string, citation) {

  citation.journal = 'Zootaxa';
  citation.issn = ['1175-5326'];
  console.log("string=" + string);
  result = string.match(/zootaxa\|(\d+)\|(\d+)\|(\d+)\|(\d+)(\|(\d+))?(\|(\d+))?(\|(\d+))?/);

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
  result = string.match(/AEMNP\|(\d+)\|(\d+)\|(\d+)\|(\d+)/);

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
  // citation object
  var citation = {};
  citation.author = [];
  citation.parts = {};
  citation.parts['AUTHOR'] = [];
  citation.parts['JOURNAL'] = [];
  citation.parts['VOLUME-PAGINATION'] = [];
  var result = null;

  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    result = string.match(/''\]\](\s+\((\d+)\))?,?\s+(''')?(\d+)(''')?(\((.*)\))?:\s*(\d+)([-|–](\d+))?/);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['VOLUME-PAGINATION'].push(result[0]);

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
    result = string.match(/\]\]''(\s+\((\d+)\))?,?\s+(''')?(\d+)(''')?(\((.*)\))?:\s*(\d+)([-|–](\d+))?/);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['VOLUME-PAGINATION'].push(result[0]);

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
    result = string.match(/''\]\],\s*(.*),\s*(\d+)([-|–](\d+))?/);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['VOLUME-PAGINATION'].push(result[0]);

      citation.volume = result[1];
      citation.spage = result[2];
      citation.page = citation.spage;
      if (result[4]) {
        citation.epage = result[4];
        citation.page += '-' + citation.epage;
      }
    }
  }

  if (citation.parts['VOLUME-PAGINATION'].length == 0) {
    result = string.match(/''([^'][^']+)''\s+'''(\d+)\s*\((\d+)\)''':\s*(\d+)([-|–](\d+))?/);

    if (result) {
      console.log(JSON.stringify(result));

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[0]);

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

  // journal
  if (citation.parts['JOURNAL'].length == 0) {
    result = string.match(/''([^'][^']+)''[\.|,]?((\s+\w+)+)?\s*('''(\d+)'''(\((.*)\))?:\s*(\d+)[-|–](\d+))/);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[4]);

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
  if (citation.parts['JOURNAL'].length == 0) {
    result = string.match(/''([^'][^']+)''[\.|,]?\s*\((.*)\)\s*('''(\d+)''':\s*(\d+)[-|–](\d+))/);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[3]);

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
    result = string.match(/''(.*)''[\.|,]?\s+('''(\d+)''':\s*(\d+)[-|–](\d+))/);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[3]);

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
  // simple journal in italics with volume not in bold
  // e.g. ''Proceedings of the Zoological Society of London'' 1896: 1012–1028.
  if (citation.parts['JOURNAL'].length == 0) {
    result = string.match(/''(.*)''[\.|,]?\s+((\d+):\s*(\d+)[-|–](\d+))/);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[3]);

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
    result = string.match(/''(.*)''[\.|,]?\s+ser\.?\s+(\d+),?\s+((\d+):\s*(\d+)[-|–](\d+))/);
    //for (var i in result) {
    if (result) {

      citation.parts['JOURNAL'].push(result[1]);
      citation.parts['VOLUME-PAGINATION'].push(result[4]);

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
    result = string.match(/\[\[ISSN ([0-9]{4}-[0-9]{3}([0-9]|X))\|(?:\'\')(.*)(?:\'\')]\]/);
    //for (var i in result) {
    if (result) {
      citation.parts['JOURNAL'].push(result[0]);
      citation.issn = [result[1]];
      citation.journal = result[3];
   }
  }

  if (citation.parts['JOURNAL'].length == 0) {
    result = string.match(/(?:\'\')\[\[ISSN ([0-9]{4}-[0-9]{3}([0-9]|X))\|(.*)\]\](?:\'\')/);
    //for (var i in result) {
    if (result) {
      citation.parts['JOURNAL'].push(result[0]);
      citation.issn = [result[1]];
      citation.journal = result[3];
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
  result = string.match(/doi:(10\.\d{4,5}\/([a-z]|[0-9]|\[|\]|<|>|;|-|\(|\)){2,30})\b/);
  if (result) {
    citation.DOI = result[1];
    citation.parts['DOI'] = result[0];
    console.log(result[1]);
  }

  // year
  result = string.match(/\}\}[,|\.|;]?\s+[\(]?([0-9]{4})[\)]?/);
  if (result) {
    citation.year = result[1];
    citation.parts['YEAR'] = result[0];
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
  }



  if (0) {
    delete citation.parts;
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

  output(citation);

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