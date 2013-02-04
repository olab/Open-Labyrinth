<?php
/**
 * The default implementation to retrieve remote resources.
 *
 * @todo Shift all of the cache content to this class
 * @todo Consider an alternative implementation with HTTP_Request2 or something else.
 * @todo Introduce exceptions instead of exit()
 */
class Graphite_Retriever {

	public function __construct(Graphite $graph) {
		$this->graph = $graph;
	}

	/**
	 * Load the RDF from the given URI or URL.
	 */
	public function retrieve( $uri )
	{
		if( !isset($this->graph->cacheDir) ) { return null; }

		$filename = $this->graph->cacheDir."/".md5( $this->graph->removeFragment( $uri ) );

		if( !file_exists( $filename ) || filemtime($filename)+$this->graph->cacheAge < time() )
		{
			# decache if out of date, even if we fail to re cache.
			if( file_exists( $filename ) ) { unlink( $filename ); }
			$url = $uri;
			$ttl = 16;
			$mime = "";
			$old_user_agent = ini_get('user_agent');
			ini_set('user_agent', "PHP\r\nAccept: application/rdf+xml");
			while( $ttl > 0 )
			{
				$ttl--;
				# dirty hack to set the accept header without using curl
				if( !$rdf_fp = fopen($url, 'r') ) { break; }
				$meta_data = stream_get_meta_data($rdf_fp);
				$redir = 0;
				if( @!$meta_data['wrapper_data'] )
				{
					fclose($rdf_fp);
					continue;
				}
				foreach($meta_data['wrapper_data'] as $response)
				{
					if (substr(strtolower($response), 0, 10) == 'location: ')
					{
						$newurl = substr($response, 10);
						if( substr( $newurl, 0, 1 ) == "/" )
						{
							$parts = preg_split( "/\//",$url );
							$newurl = $parts[0]."//".$parts[2].$newurl;
						}
						$url = $newurl;
						$redir = 1;
					}
					if (substr(strtolower($response), 0, 14) == 'content-type: ')
					{
						$mime = preg_replace( "/\s*;.*$/","", substr($response, 14));
					}
				}
				if( !$redir ) { break; }
			}
			ini_set('user_agent', $old_user_agent);
			if( $ttl > 0 && $mime == "application/rdf+xml" && $rdf_fp )
			{
				# candidate for caching!
				if (!$cache_fp = fopen($filename, 'w'))
				{
					echo "Cannot write file ($filename)";
					exit;
				}

				while (!feof($rdf_fp)) {
					fwrite( $cache_fp, fread($rdf_fp, 8192) );
				}
				fclose($cache_fp);
			}
			@fclose($rdf_fp);
		}

		if( isset( $filename ) &&  file_exists( $filename ) )
		{
			return file_get_contents($filename);
		}

		return null;
	}
}
# (c)2010,2011,2012 Christopher Gutteridge / University of Southampton
# some extra features and bugfixes by Bart Nagel
# License: LGPL
# Version 1.5

# Requires ARC2 to be included.
# suggested call method:
#   include_once("arc/ARC2.php");
#   include_once("Graphite.php");

# Similar libraries
#  EasyRDF - http://code.google.com/p/easyrdf/
#  SimpleGraph - http://code.google.com/p/moriarty/wiki/SimpleGraph
#
# I've used function calls in common with EasyRDF, where it makes sense
# to do so. Easy RDF now uses our dump() style. We're one big happy linked
# data community!

# todo:
# hasRelationValue, hasRelation, filter

# Load ARC2 assuming it's not already been loaded. Requires ARC2.php to be 
# in the path.
if( !class_exists( "ARC2" ) )
{
}


class Graphite
{

	/**
	 * @var Graphite_Retriever $retriever
	 */
	protected $retriever;

	/**
	 * Create a new instance of Graphite. @see ns() for how to specify a namespace map and a list of pre-declared namespaces.
	 */
	public function __construct( $namespaces = array(), $uri = null )
	{
		$this->workAround4StoreBNodeBug = false;
		$this->t = array( "sp" => array(), "op"=>array() );
		foreach( $namespaces as $short=>$long )
		{
			$this->ns( $short, $long );
		}
		$this->ns( "foaf", "http://xmlns.com/foaf/0.1/" );
		$this->ns( "dc",   "http://purl.org/dc/elements/1.1/" );
		$this->ns( "dcterms",  "http://purl.org/dc/terms/" );
		$this->ns( "dct",  "http://purl.org/dc/terms/" );
		$this->ns( "rdf",  "http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
		$this->ns( "rdfs", "http://www.w3.org/2000/01/rdf-schema#" );
		$this->ns( "owl",  "http://www.w3.org/2002/07/owl#" );
		$this->ns( "xsd",  "http://www.w3.org/2001/XMLSchema#" );
		$this->ns( "cc",   "http://creativecommons.org/ns#" );
		$this->ns( "bibo", "http://purl.org/ontology/bibo/" );
		$this->ns( "skos", "http://www.w3.org/2004/02/skos/core#" );
		$this->ns( "geo",  "http://www.w3.org/2003/01/geo/wgs84_pos#" );
		$this->ns( "sioc", "http://rdfs.org/sioc/ns#" );
		$this->ns( "oo",   "http://purl.org/openorg/" );

		$this->loaded = array();
		$this->debug = false;
		$this->arc2config = null;

		$this->labelRelations = array(
			"skos:prefLabel", "rdfs:label", "foaf:name", "dct:title", "dc:title", "sioc:name" );
		$this->mailtoIcon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAALCAIAAAAvJUALAAAABGdBTUEAALGPC/xhBQAAAAlwSFlz
AAALEwAACxMBAJqcGAAAAAd0SU1FB9wCAhEsArM6LtoAAAF/SURBVBjTfZFNTxNhFIXf985QypTA
OLQdihMrHya6FFFqJBp+Gz+ABTFx45+w0QSCO5WFCisMUYMLhBZahdjp13vuveOCxGiiPnkWZ3FW
59iD7bXK/KLhnrFk/kWm1g9aRx8oTpL93c2gPFcIqRDavxqUqx/3X0XlEgkPl1eW3tQ32CYZDzJ0
/pD7Ssnbrae3794SHhAzw7na6sPXz9YHpqomLwxhFoZmOXjzOy8e36ktwzlmJgYUTobD2qOVd5tP
fqRjxispnPGKab+wU99Yun9PMFQ4BogBYQgjE7k2W/28Vz8+avrRg8bXxqfd5ws3b/S7vcsCAz7D
CSyRd3baLsXFyYnxL4d7B+9fxtNTcwvX8/nRxslpZSZWFYbzASh73y/OJybH2Tmy5mpSIUvTlbJP
lp2bisLmcbNYugLAF6CX8ghZq6IqxpicR0kSe0TKuJw7N+J1Ox2B8QGXphxFoQj/foiI/spBMNo6
a0PH/JNGOyzot9a5+S+Z6kW38xPpxe30BrwPeQAAAABJRU5ErkJggg==
';
		$this->telIcon = 'data:image/png;base64,
iVBORw0KGgoAAAANSUhEUgAAAA4AAAAOCAYAAAAfSC3RAAAABGdBTUEAALGPC/xhBQAAAAZiS0dE
AP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9wCBxIsM9d8YIsAAAItSURB
VCjPTZLLS5VhEMZ/7/ediyiek1csOSc9ZQqhQpAVWC3auGyh9BfUKjFw4aZVQW0SxD+hVeU+WklW
RkVQoEYoZXhMyeupMPV878y0+MSaxczDwMzzzMUBDA6NDPR0N4ymkmFOTTGD2MVmB1hEi+9nS8Pj
Y/cn3ODQyMD4nQuP9//su6nXc+Sb62k93ggYGGB6WAhGOuXs1r25a+HNG1efnS5UZx8+es7C11WW
ims0N9WQqUpjKmAKGGYCavjIu+1f/nyQSrrc1PQs6+slfBRR+rnD1lYJU8FUUBFU/GEUFcKQXMLU
SIaxDNNY1u7uPqoRKnYg2TAslmyGmRKoKifyDQQuTtRmK2nN16FeUBVMfcwuMUYFDBJmSqY6TeDA
VKmqTFKVTqDiwQw9ZPy3KDMliOmFky31gLL0fYN3H76gKqh6THw8wn9zYkaAGSaeS+faqM1UYqK8
ejvPx5lFnAm7e7ssLK5QLu9jEsXNMBKYoSokAug9W+Dp5CzlyPPizTzLq5tsl/6wtvGb5qYsne1N
tBUaQY3AMEw9IhFtrfVc7ClQkQrZ2yvz6fMKqz+2URWWljeZnJ7HIg8YgXgtIh4TISqX6Wpvou9y
B3U1FaSTAQGgqjgHNZkKDMGLFcOWjt7lK2cS/RKpw+ITZKtTdJ06Sm1NJZulHXwk5I8dob+vk1TC
2ZOXdt0BDA3fHuguhKNBQA4Md/CmzjnCwIGLH70cWXHmmw2PPbg78Rex1nK3Gk8UNQAAAABJRU5E
rkJggg==
';

		$this->firstGraphURI = null;
		if( $uri )
		{
			$this->load( Graphite::asString($uri) );
		}

		$this->bnodeprefix = 0;
		$this->setRetriever(new Graphite_Retriever($this));
	}

	public function setRetriever(Graphite_Retriever $retriever) {
		$this->retriever = $retriever;
	}

	/**
	 * Graphite uses ARC2 to parse RDF, which isn't as fast as using a compiled library. I may add support for <a href='http://www4.wiwiss.fu-berlin.de/bizer/rdfapi/'>RAP</a> or something similar. When Graphite loads a triple it indexes it by both subject &amp; object, which also takes a little time. To address this issue, freeze and thaw go some way to help speed things up. freeze takes a graph object, including all the namespaces set with ns() and saves it to disk as a serialised PHP object, which is much faster to load then a large RDF file. It's ideal in a situation where you want to build a site from a single RDF document which is updated occasionally. <a href='https://github.com/cgutteridge/Graphite/blob/master/examples/freeze.php'>This example</a> is a command line script you can modify to load and freeze a graph.
	 */
	public function freeze( $filename )
	{
		$fh = fopen($filename, 'w') or die("can't open file");
		fwrite($fh, serialize( $this ) );
		fclose($fh);
	}

	/**
	 * Graphite uses ARC2 to parse RDF, which isn't as fast as using a compiled library. I may add support for <a href='http://www4.wiwiss.fu-berlin.de/bizer/rdfapi/'>RAP</a> or something similar. When Graphite loads a triple it indexes it by both subject &amp; object, which also takes a little time. To address this issue, freeze and thaw go some way to help speed things up. freeze takes a graph object, including all the namespaces set with ns() and saves it to disk as a serialised PHP object, which is much faster to load then a large RDF file. It's ideal in a situation where you want to build a site from a single RDF document which is updated occasionally. <a href='https://github.com/cgutteridge/Graphite/blob/master/examples/freeze.php'>This example</a> is a command line script you can modify to load and freeze a graph.
	 */
	public static function thaw( $filename )
	{
		return unserialize( join( "", file( $filename )));
	}

	public static function __set_state($data) // As of PHP 5.1.0
	{
		$graph = new Graphite;
		$graph->bnodeprefix = $data['bnodeprefix'];
		$graph->firstGraphURI = $data['firstGraphURI'];
		$graph->loaded = $data['loaded'];
		$graph->ns = $data['ns'];
		$graph->workAround4StoreBNodeBug = $data["workAround4StoreBNodeBug"];
		$graph->t = $data["t"];
		return $graph;
	}

	/**
	 * $dir should be a directory the webserver has permission to read and write to. Any RDF/XML documents which graphite downloads will be saved here. If a cache exists and is newer than $age seconds then load() will use the document in the cache directory in preference to doing an HTTP request. $age defaults to 24*60*60 - 24 hours. This including this function can seriously improve graphite performance! If you want to always load certain documents, load them before setting the cache.
	 *
	 * @todo Shift to Graphite_Retriever
	 */
	public function cacheDir( $dir, $age = 86400 ) # default age is 24 hours
	{
		$error = "";
		if( !file_exists( $dir ) ) { $error = "No such directory: $dir"; }
		elseif( !is_dir( $dir ) ) { $error = "Not a directory: $dir"; }
		elseif( !is_writable( $dir ) ) { $error = "Not writable: $dir"; }
		if( $error ) {
			print "<ul><li>Graphite cacheDir error: $error</li></ul>";
		}
		else
		{
			$this->cacheDir = $dir;
			$this->cacheAge = $age;
		}
	}

	public function setARC2Config( $config ) { $this->arc2config = $config; }
	public function setDebug( $boolean ) { $this->debug = $boolean; }

	/**
	 * Return a list of the relations currently used for $resource->label(), if called with a parameter then this should be an array to <strong>replace</strong> the current list. To just add additonal relation types to use as labels, use addLabelRelation($relation).
	 */
	public function labelRelations( $new = null )
	{
		$lr = $this->labelRelations;
		if( isset( $new ) ) { $this->labelRelations = $new; }
		return $lr;
	}

	/**
	 * Return a list of the relations currently used for $resource->label(), if called with a parameter then this should be an array to <strong>replace</strong> the current list. To just add additonal relation types to use as labels, use addLabelRelation($relation).
	 */
	public function addLabelRelation( $addition )
	{
		$this->labelRelations []= $addition;
	}

	/**
	 * Get or set the URL of the icon used for mailto: and tel: links in prettyLink(). If set to an empty string then no icon will be shown.
	 */
	public function mailtoIcon( $new = null )
	{
		$icon = $this->mailtoIcon;
		if( isset( $new ) ) { $this->mailtoIcon = $new; }
		return $icon;
	}

	/**
	 * Get or set the URL of the icon used for mailto: and tel: links in prettyLink(). If set to an empty string then no icon will be shown.
	 */
	public function telIcon( $new = null )
	{
		$icon = $this->telIcon;
		if( isset( $new ) ) { $this->telIcon = $new; }
		return $icon;
	}

	function removeFragment( $uri )
	{
		return preg_replace( "/#.*/", "", $uri );
	}

	function loaded( $uri )
	{
		if( !array_key_exists( $this->removeFragment( $uri ), $this->loaded ) )
		{
			return false;
		}
		return $this->loaded[$this->removeFragment( $uri )];
	}

	/**
	 * Load the RDF from the given URI or URL. Return the number of triples loaded.
	 */
	public function load( $uri, $aliases = array(), $map = array() )
	{
		$uri = $this->expandURI( Graphite::asString($uri) );

		if( substr( $uri,0,5 ) == "data:" )
		{
			$data = urldecode( preg_replace( "/^data:[^,]*,/","", $uri ) );
			$parser = ARC2::getTurtleParser( $this->arc2config );
			$parser->parse( $uri, $data );
		}
		else
		{
			if( $this->loaded( $uri ) !== false ) { return $this->loaded( $uri ); }

			$data = $this->retriever->retrieve($uri);

			if(!empty($data))
			{
				$parser = ARC2::getRDFXMLParser( $this->arc2config );
				$parser->parse( $uri, $data );
			}
			else
			{
				$opts = array();
 				if( isset($this->arc2config) ) { $opts =  $this->arc2config; }
				$opts['http_accept_header']= 'Accept: application/rdf+xml; q=0.9, text/turtle; q=0.8, */*; q=0.1';

				$parser = ARC2::getRDFParser($opts);
				# Don't try to load the same URI twice!

				if( !isset( $this->firstGraphURI ) )
				{
					$this->firstGraphURI = $uri;
				}
				$parser->parse( $uri );
			}
		}

		$errors = $parser->getErrors();
		$parser->resetErrors();
		if( sizeof($errors) )
		{
			if( $this->debug )
			{
				print "<h3>Error loading: $uri</h3>";
				print "<ul><li>".join( "</li><li>",$errors)."</li></ul>";
			}
			return 0;
		}
		$this->loaded[$this->removeFragment( $uri )] = $this->addTriples( $parser->getTriples(), $aliases, $map );
		return $this->loaded( $uri );
	}

	/**
	 * This uses one or more SPARQL queries to the given endpoint to get all the triples required for the description. The return value is the total number of triples added to the graph.
	 */
	function loadSPARQL( $endpoint, $query )
	{
		return $this->load( $endpoint."?query=".urlencode($query) );
	}

	/**
	 * Take a base URI and a string of turtle RDF and load the new triples into the graph. Return the number of triples loaded.
	 */
	function addTurtle( $base, $data )
	{
		$parser = ARC2::getTurtleParser( $this->arc2config );
		$parser->parse( $base, $data );
		$errors = $parser->getErrors();
		$parser->resetErrors();
		if( sizeof($errors) )
		{
			if( $this->debug )
			{
				print "<h3>Error loading turtle string</h3>";
				print "<ul><li>".join( "</li><li>",$errors)."</li></ul>";
			}
			return 0;
		}
		return $this->addTriples( $parser->getTriples() );
	}

	/**
	 * As for addTurtle but load a string of RDF XML
	 *
	 * @see addTurtle
	 */
	function addRDFXML( $base, $data )
	{
		$parser = ARC2::getRDFXMLParser( $this->arc2config );
		$parser->parse( $base, $data );
		$errors = $parser->getErrors();
		$parser->resetErrors();
		if( sizeof($errors) )
		{
			if( $this->debug )
			{
				print "<h3>Error loading RDFXML string</h3>";
				print "<ul><li>".join( "</li><li>",$errors)."</li></ul>";
			}
			return 0;
		}
		return $this->addTriples( $parser->getTriples() );
	}

	/**
	 * Replace bnodes shorthand with configured bnodeprefix in URI
	 *
	 * @param string $uri
	 */
	function addBnodePrefix( $uri )
	{
		return preg_replace( "/^_:/", "_:g" . $this->bnodeprefix . "-", $uri );
	}

	/**
	 * Add triples to the graph from an ARC2 datastrcture. This is the inverse of toArcTriples.
	 *
	 * @see ARC2
	 * @see toArcTriples
	 */
	function addTriples( $triples, $aliases = array(), $map = array() )
	{
		$this->bnodeprefix++;

		foreach( $triples as $t )
		{
			if( $this->workAround4StoreBNodeBug )
			{
				if( $t["s"] == "_:NULL" || $t["o"] == "_:NULL" ) { continue; }
			}
			$t["s"] = $this->addBnodePrefix( $this->cleanURI($t["s"]) );
			if( !isset($map[$t["s"]]) ) { continue; }
			$t["p"] = $this->cleanURI($t["p"]);
			if( $t["p"] != "http://www.w3.org/2002/07/owl#sameAs" ) { continue; }
			$aliases[$this->addBnodePrefix( $t["o"] )] = $t["s"];
		}
		foreach( $triples as $t )
		{
			$datatype = @$t["o_datatype"];
			if( @$t["o_type"] == "literal" && !$datatype ) { $datatype = "literal"; }
			$this->addTriple( $t["s"], $t["p"], $t["o"], $datatype, @$t["o_lang"], $aliases );
		}
		return sizeof( $triples );
	}

	/**
	 * Add a single triple directly to the graph. Only addCompressedTriple accepts shortended URIs, eg foaf:name.
	 *
	 * @see addTriple
	 */
	function addCompressedTriple( $s,$p,$o,$o_datatype=null,$o_lang=null,$aliases=array() )
	{
		$s = $this->expandURI( $s );
		$p = $this->expandURI( $p );
		$o = $this->expandURI( $o );
		if( isset( $o_datatype ) && $o_dataype != "literal" )
		{
			$o_datatype = $this->expandURI( $o_datatype );
		}
		$this->addTriple( $s,$p,$o,$o_datatype,$o_lang,$aliases );
	}

	/**
	 * Add a single triple directly to the graph.
	 *
	 * @see addCompressedTriple
	 */
	function addTriple( $s,$p,$o,$o_datatype=null,$o_lang=null,$aliases=array() )
	{
		if( $this->workAround4StoreBNodeBug )
		{
			if( $s == "_:NULL" || $o == "_:NULL" ) { return; }
		}
		$s = $this->addBnodePrefix( $this->cleanURI( $s ) );
		if( !isset($o_datatype) || $o_datatype == "" )
		{
			$o = $this->addBnodePrefix( $this->cleanURI( $o ) );
		}

		if( isset($aliases[$s]) ) { $s=$aliases[$s]; }
		if( isset($aliases[$p]) ) { $p=$aliases[$p]; }
		if( isset($aliases[$o]) ) { $o=$aliases[$o]; }

		if( isset( $o_datatype ) && $o_datatype )
		{
			if( $o_datatype == 'literal' ) { $o_datatype = null; }
			$this->t["sp"][$s][$p][] = array(
				"v"=>$o,
				"d"=>$o_datatype,
				"l"=>$o_lang );
		}
		else
		{
			$this->t["sp"][$s][$p][] = $o;
		}
		$this->t["op"][$o][$p][] = $s;
	}

	/**
	 * Returns all triples of which this resource is the subject in Arc2's internal triples format.
	 */
	public function toArcTriples()
	{
		$arcTriples = array();
		foreach( $this->allSubjects() as $s )
		{
			$arcTriples = array_merge( $arcTriples, $s->toArcTriples( false ) );
		}
		return $arcTriples;
	}

	/**
	 * Returns the serialization of the entire RDF graph in memory using one of Arc2's serializers. By default the RDF/XML serializer is used, but others (try passing "Turtle" or "NTriples") can be used - see the Arc2 documentation.
	 */
	public function serialize( $type = "RDFXML" )
	{
		$ns = $this->ns;
		unset( $ns["dct"] ); 
		// use dcterms for preference. duplicates seem to cause
		// bugs in the serialiser
		$serializer = ARC2::getSer( $type, array( "ns" => $ns ));
		return $serializer->getSerializedTriples( $this->toArcTriples() );
	}

	public function cleanURI( $uri )
	{
		if( !$uri ) { return; }
		return preg_replace( '/^(https?:\/\/[^:\/]+):80\//','$1/', $uri );
	}

	/**
	 * Utility method (shamelessly ripped off from EasyRDF). Returns the primary topic of the first URL that was loaded. Handy when working with FOAF.
	 */
	public function primaryTopic( $uri = null )
	{
		if( !$uri ) { $uri = $this->firstGraphURI; }
		if( !$uri ) { return new Graphite_Null($this->g); }

		return $this->resource( Graphite::asString($uri) )->get( "foaf:primaryTopic", "-foaf:isPrimaryTopicOf" );
	}

	/**
	 * Add an additional namespace alias to the Graphite instance.
	 *
	 * @param string $short Must be a valid xmlns prefix. urn, http, doi, https, ftp, mail, xmlns, file and data are reserved.
	 * @param string $long  Must be either a valid URI or an empty string.
	 *
	 * @todo URI validation.
	 * @see http://www.w3.org/TR/REC-xml-names/#ns-decl
	 * @throws InvalidArgumentException
	 */
	public function ns( $short, $long )
	{
		if (empty($short)) {
			throw new InvalidArgumentException("A valid xmlns prefix is required.");
		}

		if( preg_match( '/^(urn|doi|http|https|ftp|mailto|xmlns|file|data)$/i', $short ) )
		{
			throw new InvalidArgumentException("Setting a namespace called '$short' is just asking for trouble. Abort.");
		}
		$this->ns[$short] = $long;
	}

	/**
	 * Get the resource with given URI. $uri may be abbreviated to "namespace:value".
	 *
	 * @return Graphite_Resource
	 */
	public function resource( $uri )
	{
		$uri = $this->expandURI( Graphite::asString($uri) );
		return new Graphite_Resource( $this, $uri );
	}

	/**
	 * Return a list of all resources loaded, with the rdf:type given. eg. $graph-&gt;allOfType( "foaf:Person" )
	 */
	public function allOfType( $uri )
	{
		return $this->resource( $uri )->all("-rdf:type");
	}

	/**
	 * Translate a URI from the long form to any shorthand version known.
	 * IE: http://xmlns.com/foaf/0.1/knows => foaf:knows
	 */
	public function shrinkURI( $uri )
	{
		if( Graphite::asString($uri) == "" ) { return "* This Document *"; }
		foreach( $this->ns as $short=>$long )
		{
			if( substr( Graphite::asString($uri), 0, strlen($long) ) == $long )
			{
				return $short.":".substr( Graphite::asString($uri), strlen($long ));
			}
		}
		return Graphite::asString($uri);
	}

	/**
	 * Translate a URI from the short form to any long version known.
	 * IE:  foaf:knows => http://xmlns.com/foaf/0.1/knows
	 */
	public function expandURI( $uri )
	{
		if( preg_match( '/:/', Graphite::asString($uri) ) )
		{
			list( $ns, $tag ) = preg_split( "/:/", Graphite::asString($uri), 2 );
			if( isset($this->ns[$ns]) )
			{
				return $this->ns[$ns].$tag;
			}
		}
		return Graphite::asString($uri);
	}

	/**
	 * Return a list of all resources in the graph which are the subject of at least one triple.
	 */
	public function allSubjects()
	{
		$r = new Graphite_ResourceList( $this );
		foreach( $this->t["sp"] as $subject_uri=>$foo )
		{
			 $r[] = new Graphite_Resource( $this, $subject_uri );
		}
		return $r;
	}

	/**
	 * Return a list of all resources in the graph which are the object of at least one triple.
	 */
	public function allObjects()
	{
		$r = new Graphite_ResourceList( $this );
		foreach( $this->t["op"] as $object_uri=>$foo )
		{
			 $r[] = new Graphite_Resource( $this, $object_uri );
		}
		return $r;
	}

	/**
	 * Create a pretty HTML dump of the current resource. Handy for debugging halfway through hacking something.
	 *
	 * $options is an optional array of flags to modify how dump() renders HTML. dumpText() does the same think with ASCII indention instead of HTML markup, and is intended for debugging command-line scripts.
	 *
	 * "label"=> 1 - add a label for the URI, and the rdf:type, to the top of each resource box, if the information is in the current graph.
	 * "labeluris"=> 1 - when listing the resources to which this URI relates, show them as a label, if possible, rather than a URI. Hovering the mouse will still show the URI.</div>
	 * "internallinks"=> 1 - instead of linking directly to the URI, link to that resource's dump on the current page (which may or may not be present). This can, for example, make bnode nests easier to figure out.
	 */
	public function dump( $options=array() )
	{
		$r = array();
		foreach( $this->t["sp"] as $subject_uri=>$foo )
		{
			$subject = new Graphite_Resource( $this, $subject_uri );
			$r []= $subject->dump($options);
		}
		return join("",$r );
	}

	/**
	 * @see dump()
	 */
	public function dumpText( $options=array() )
	{
		$r = array();
		foreach( $this->t["sp"] as $subject_uri=>$foo )
		{
			$subject = new Graphite_Resource( $this, $subject_uri );
			$r []= $subject->dumpText($options);
		}
		return join("\n",$r );
	}

    /** @deprecated All graphite objects should implement __toString() */
	public function forceString( &$uri )
	{
		$uri = asString( $uri );
		return $uri;
	}
	
	public static function asString( $uri )
	{
		if( is_object( $uri ) ) { return $uri->toString(); }
		return $uri;
	}
}


function graphite_sort_list_cmp( $a, $b )
{
	global $graphite_sort_args;

	foreach( $graphite_sort_args as $arg )
	{
		$va = $a->get( $arg );
		$vb = $b->get( $arg );
		if($va < $vb) return -1;
		if($va > $vb) return 1;
	}
	return 0;
}





class Graphite_Node
{
	function __construct(Graphite $g )
	{
		$this->g = $g;
	}
	function isNull() { return false; }
	function has() { return false; }
	function get() { return new Graphite_Null($this->g); }
	function type() { return new Graphite_Null($this->g); }
	function label() { return "[UNKNOWN]"; }
	function hasLabel() { return false; }
	function all() { return new Graphite_ResourceList($this->g, array()); }
	function types() { return $this->all(); }
	function relations() { return $this->all(); }
	function load() { return 0; }
	function loadSameAs() { return 0; }
	function loadSameAsOrg($prefix) { return 0; }
	function loadDataGovUKBackLinks() { return 0; }

	function dumpText() { return "Non existant Node"; }
	function dump() { return "<div style='padding:0.5em; background-color:lightgrey;border:dashed 1px grey;'>Non-existant Node</div>"; }
	function nodeType() { return "#node"; }
	function __toString() { return "[NULL]"; }

    /** @deprecated Use __toString() or (string) instead */
	function toString() { return $this->__toString(); }
	function datatype() { return null; }
	function language() { return null; }

}
class Graphite_Null extends Graphite_Node
{
	function nodeType() { return "#null"; }
	function isNull() { return true; }
}
class Graphite_Resource extends Graphite_Node
{
	function __construct(Graphite $g, $uri )
	{
		$this->g = $g;
		$this->uri = Graphite::asString($uri);
	}

	public function get( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$l = $this->all( $args );
		if( sizeof( $l ) == 0 ) { return new Graphite_Null($this->g); }
		return $l[0];
	}

	public function getLiteral( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$l = $this->all( $args );
		if( sizeof( $l ) == 0 ) { return; }
		return Graphite::asString($l[0]);
	}
	# getString deprecated in favour of getLiteral
	public function getString( /* List */ ) { return $this->getLiteral( func_get_args() ); }

	public function getDatatype( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$l = $this->all( $args );
		if( sizeof( $l ) == 0 ) { return; }
		return $l[0]->datatype();
	}
	public function getLanguage( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$l = $this->all( $args );
		if( sizeof( $l ) == 0 ) { return; }
		return $l[0]->language();
	}

	public function allString( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$l = array();
		foreach( $this->all( $args ) as $item )
		{
			$l []= Graphite::asString($item);
		}
		return new Graphite_ResourceList($this->g,$l);
	}

	public function has(  /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		foreach( $args as $arg )
		{
			list( $set, $relation_uri ) = $this->parsePropertyArg( $arg );
			if( isset($this->g->t[$set][$this->uri])
			 && isset($this->g->t[$set][$this->uri][$relation_uri]) )
			{
				return true;
			}
		}
		return false;
	}

	public function all(  /* List */ )
	{
		$args = func_get_args();

		if (empty($args)) {
			return new Graphite_ResourceList($this->g, array());
		}

		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }
		$l = array();
		$done = array();
		foreach( $args as $arg )
		{
			list( $set, $relation_uri ) = $this->parsePropertyArg( $arg );
			if( !isset($this->g->t[$set][$this->uri])
			 || !isset($this->g->t[$set][$this->uri][$relation_uri]) )
			{
				continue;
			}

			foreach( $this->g->t[$set][$this->uri][$relation_uri] as $v )
			{
				if( is_array( $v ) )
				{
					$l []= new Graphite_Literal( $this->g, $v );
				}
				else if( !isset($done[$v]) )
				{
					$l []= new Graphite_Resource( $this->g, $v );
					$done[$v] = 1;
				}
			}
		}
		return new Graphite_ResourceList($this->g,$l);
	}

	public function relations()
	{
		$r = array();
		if( isset( $this->g->t["sp"][$this->uri] ) )
		{
			foreach( array_keys( $this->g->t["sp"][$this->uri] ) as $pred )
			{
				$r []= new Graphite_Relation( $this->g, $pred );
			}
		}
		if( isset( $this->g->t["op"][$this->uri] ) )
		{
			foreach( array_keys( $this->g->t["op"][$this->uri] ) as $pred )
			{
				$r []= new Graphite_InverseRelation( $this->g, $pred );
			}
		}

		return new Graphite_ResourceList($this->g,$r);
	}

	public function toArcTriples( $bnodes = true )
	{
		$arcTriples = array();
		$bnodes_to_add = array();

		$s = $this->uri;
		$s_type = "uri";
		if( preg_match( '/^_:/', $s ) )
		{
			$s_type = "bnode";
		}

		if (!empty($this->g->t["sp"][$s])) {

			foreach( $this->g->t["sp"][$s] as $p => $os )
			{
				$p = $this->g->expandURI( $p );
				$p_type = "uri";
				if( preg_match( '/^_:/', $p ) )
				{
					$p_type = "bnode";
				}

				foreach( $os as $o )
				{
					$o_lang = null;
					$o_datatype = null;
					if( is_array( $o ))
					{
						$o_type = "literal";
						if( isset( $o["l"] ) && $o["l"] )
						{
							$o_lang = $o["l"];
						}
						if( isset( $o["d"] ) )
						{
							$o_datatype = $this->g->expandURI( $o["d"] );
						}
						$o = $o["v"];
					}
					else
					{
						$o = $this->g->expandURI( $o );
						$o_type = "uri";
						if( preg_match( '/^_:/', $o ) )
						{
							$o_type = "bnode";
							$bnodes_to_add[] = $o;
						}
					}
					$triple = array(
						"s" => $s,
						"s_type" => $s_type,
						"p" => $p,
						"p_type" => $p_type,
						"o" => $o,
						"o_type" => $o_type,
					);
					$triple["o_datatype"] = $o_datatype;
					$triple["o_lang"] = $o_lang;

					$arcTriples[] = $triple;
				}
			}
		}

		if( $bnodes )
		{
			foreach( array_unique( $bnodes_to_add ) as $bnode )
			{
				$arcTriples = array_merge( $arcTriples, $this->g->resource( $bnode )->toArcTriples() );
			}
		}
		return $arcTriples;
	}

	public function serialize( $type = "RDFXML" )
	{
		$serializer = ARC2::getSer( $type, array( "ns" => $this->g->ns ) );
		return $serializer->getSerializedTriples( $this->toArcTriples() );
	}

	public function load()
	{
		return $this->g->load( $this->uri );
	}

	public function loadSameAsOrg( $prefix )
	{
		if (empty($this->uri)) {
			return 0;
		}

		$sameasorg_uri = "http://sameas.org/rdf?uri=".urlencode( $this->uri );
		$n = $this->g->load( $sameasorg_uri );
		$n+= $this->loadSameAs( $prefix );
		return $n;
	}

	function loadDataGovUKBackLinks()
	{
		$backurl = "http://backlinks.psi.enakting.org/resource/rdf/".$this->uri;
		return $this->g->load( $backurl, array(), array( $this->uri=>1 ) );
	}

	public function loadSameAs( $prefix=null )
	{
		$cnt = 0;
		foreach( $this->all( "owl:sameAs" ) as $sameas )
		{
			if( $prefix && substr( Graphite::asString($sameas), 0, strlen($prefix )) != $prefix )
			{
				continue;
			}
			$cnt += $this->g->load( Graphite::asString($sameas), array( Graphite::asString($sameas)=>$this->uri ) );
		}
		return $cnt;
	}

	public function type()
	{
		return $this->get( "rdf:type" );
	}

	public function types()
	{
		return $this->all( "rdf:type" );
	}

	public function isType( /* List */ )
	{
		$args = func_get_args();
		if (empty($args)) {
			return false;
		}

		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		foreach( $this->allString( 'rdf:type' ) as $type )
		{

			foreach( $args as $arg )
			{
				$uri = $this->g->expandURI( $arg );
				if( $uri == $type ) { return true; }
			}
		}

		return false;
	}

	public function hasLabel()
	{
		return $this->has( $this->g->labelRelations() );
	}
	public function label()
	{
		return $this->getLiteral( $this->g->labelRelations() );
	}

	public function link()
	{
		return "<a title='".$this->uri."' href='".$this->uri."'>".$this->uri."</a>";
	}
	public function prettyLink()
	{
		if( substr( $this->uri, 0, 4 ) == "tel:" )
		{
			$label = substr( $this->uri, 4 );
			if( $this->hasLabel() ) { $label = $this->label(); }
			$icon = "";
			$iconURL = $this->g->telIcon();
			if( $iconURL != "" );
			{
				$icon =
"<a title='".$this->uri."' href='".$this->uri."'><img style='padding-right:0.2em;' src='$iconURL' /></a>";
			}
			return
"<span style='white-space:nowrap'>$icon<a title='".$this->uri."' href='".$this->uri."'>$label</a></span>";

			# icon adapted from cc-by icon at http://pc.de/icons/
		}

		if( substr( $this->uri, 0, 7 ) == "mailto:" )
		{
			$label = substr( $this->uri, 7 );
			if( $this->hasLabel() ) { $label = $this->label(); }
			$icon = "";
			$iconURL = $this->g->mailtoIcon();
			if( $iconURL != "" );
			{
				$icon =
"<a title='".$this->uri."' href='".$this->uri."'><img style='padding-right:0.2em;' src='$iconURL' /></a>";
			}
			return
"<span style='white-space:nowrap'>$icon<a title='".$this->uri."' href='".$this->uri."'>$label</a></span>";
			# icon adapted from cc-by icon at http://pc.de/icons/
		}

		$label = $this->uri;
		if( $this->hasLabel() ) { $label = $this->label(); }
		return "<a title='".$this->uri."' href='".$this->uri."'>$label</a>";
	}

	public function dumpText()
	{
		$r = "";
		$plist = array();
		foreach( $this->relations() as $prop )
		{
			$olist = array();
			foreach( $this->all( $prop ) as $obj )
			{
				$olist []= $obj->dumpValueText();
			}
			$arr = "->";
			if( is_a( $prop, "Graphite_InverseRelation" ) ) { $arr = "<-"; }
			$plist []= "$arr ".$this->g->shrinkURI($prop)." $arr ".join( ", ",$olist );
		}
		return $this->g->shrinkURI($this->uri)."\n    ".join( ";\n    ", $plist )." .\n";
	}

	public function dump( $options = array() )
	{
		$r = "";
		$plist = array();
		foreach( $this->relations() as $prop )
		{
			$olist = array();
			$all = $this->all( $prop );
			foreach( $all as $obj )
			{
				$olist []= $obj->dumpValue($options);
			}
			if( is_a( $prop, "Graphite_InverseRelation" ) )
			{
				$pattern = "<span style='font-size:130%%'>&larr;</span> is <a title='%s' href='%s' style='text-decoration:none;color: green'>%s</a> of <span style='font-size:130%%'>&larr;</span> %s";
			}
			else
			{
				$pattern = "<span style='font-size:130%%'>&rarr;</span> <a title='%s' href='%s' style='text-decoration:none;color: green'>%s</a> <span style='font-size:130%%'>&rarr;</span> %s";
			}
			$prop = $prop->toString();
			$plist []= sprintf( $pattern, $prop, $prop, $this->g->shrinkURI($prop), join( ", ",$olist ));
		}
		$r.= "\n<a name='".htmlentities($this->uri)."'></a><div style='text-align:left;font-family: arial;padding:0.5em; background-color:lightgrey;border:dashed 1px grey;margin-bottom:2px;'>\n";
		if( isset($options["label"] ) )
		{
			$label = $this->label();
			if( $label == "[NULL]" ) { $label = ""; } else { $label = "<strong>$label</strong>"; }
			if( $this->has( "rdf:type" ) )
			{
				if( $this->get( "rdf:type" )->hasLabel() )
				{
					$typename = $this->get( "rdf:type" )->label();
				}
				else
				{
					$bits = preg_split( "/[\/#]/", @$this->get( "rdf:type" )->uri );
					$typename = array_pop( $bits );
					$typename = preg_replace( "/([a-z])([A-Z])/","$1 $2",$typename );
				}
				$r .= preg_replace( "/>a ([AEIOU])/i", ">an $1", "<div style='float:right'>a $typename</div>" );
			}
			if( $label != "" ) { $r.="<div>$label</div>"; }
		}
		$r.= " <!-- DUMP:".$this->uri." -->\n <div><a title='".$this->uri."' href='".$this->uri."' style='text-decoration:none'>".$this->g->shrinkURI($this->uri)."</a></div>\n";
		$r.="  <div style='padding-left: 3em'>\n  <div>".join( "</div>\n  <div>", $plist )."</div></div><div style='clear:both;height:1px; overflow:hidden'>&nbsp;</div></div>";
		return $r;
	}

	function __toString() {
		return !empty($this->uri) ? Graphite::asString($this->uri) : "";
	}

	function dumpValue($options=array())
	{
		$label = $this->dumpValueText();
		if( $this->hasLabel() && @$options["labeluris"] )
		{
			$label = $this->label();
		}
		$href = $this->uri;
		if( @$options["internallinks"] )
		{
			$href = "#".htmlentities($this->uri);
		}
		return "<a href='".$href."' title='".$this->uri."' style='text-decoration:none;color:red'>".$label."</a>";
	}
	function dumpValueText() { return $this->g->shrinkURI( $this->uri ); }
	function nodeType() { return "#resource"; }

	function prepareDescription()
	{
		return new Graphite_Description( $this );
	}

	protected function parsePropertyArg( $arg )
	{
		if( is_a( $arg, "Graphite_Resource" ) )
		{
			if( is_a( $arg, "Graphite_InverseRelation" ) )
			{
				return array( "op", Graphite::asString($arg) );
			}
			return array( "sp", Graphite::asString($arg) );
		}

		$set = "sp";
		if( substr( $arg,0,1) == "-" )
		{
			$set = "op";
			$arg = substr($arg,1);
		}
		return array( $set, $this->g->expandURI( "$arg" ) );
	}
}
class Graphite_Relation extends Graphite_Resource
{
	function nodeType() { return "#relation"; }
}

# A Graphite Description is an object to describe the routes of attributes
# which we wish to use to describe a specific resource, and to allow that
# to be nicely expressed in JSON.

class Graphite_Description
{
	var $graph;
	var $resource;
	var $routes = array();
	var $tree = array(
		"+" => array(),
		"-" => array() );
	# header, footer

	function __construct( $resource )
	{
		$this->graph = $resource->g;
		$this->resource = $resource;
	}

	function addRoute( $route )
	{
		$this->routes[$route] = true;
		$preds = preg_split( '/\//', $route );
		$treeptr = &$this->tree;
		foreach( $preds as $pred )
		{
			$dir = "+";
			if( substr($pred,0,1) == "-" ) { $pred = substr($pred,1); $dir = "-"; }
			if( !isset( $treeptr[$dir][$pred] ) )
			{
				$treeptr[$dir][$pred] = array( "+" => array(), "-" => array() );
			}
			$treeptr = &$treeptr[$dir][$pred];
		}
	}

	function toDebug()
	{
		$json = array();
		$this->_jsonify( $this->tree, $this->resource, $json );

		return print_r( $json, 1 );
	}

	function toJSON()
	{
		$json = array();
		$this->_jsonify( $this->tree, $this->resource, $json );

		return json_encode( $json );
	}

	function _jsonify( $tree, $resource, &$json )
	{
		foreach( $resource->relations() as $relation )
		{
			$code = $this->graph->shrinkURI( $relation );
			$jsonkey = $code;
			$dir = "+";
			if( $relation->nodeType() == "#inverseRelation" )
			{
				$dir = "-";
				$jsonkey = "$jsonkey of";
			}
			if( !isset($tree[$dir]["*"]) && !isset($tree[$dir][$code]) ) { continue; }

			foreach( $resource->all( $relation ) as $value )
			{
				if( is_a( $value, "Graphite_Literal" ) )
				{
					$json[$jsonkey][] = Graphite::asString($value);
				}
				else
				{
					$subjson = array();
					$uri = Graphite::asString($value);
					if( substr( $uri,0,2 ) != "_:" ) { $subjson["_uri"] = $uri; }
					if( isset( $tree[$dir][$code]) )
					{
						$this->_jsonify( $tree[$dir][$code], $value, $subjson );
					}
					if( isset( $tree[$dir]["*"]) )
					{
						$this->_jsonify( $tree[$dir]["*"], $value, $subjson );
					}
					$json[$jsonkey][] = $subjson;
				}
			}
		}
	}

	function toGraph()
	{
		$new_graph = new Graphite();
		$this->_tograph( $this->tree, $this->resource, $new_graph );
		return $new_graph;
	}

	function _tograph( $tree, $resource, &$new_graph )
	{
		foreach( $resource->relations() as $relation )
		{
			$code = $this->graph->shrinkURI( $relation );
			$dir = "+";
			if( $relation->nodeType() == "#inverseRelation" )
			{
				$dir = "-";
			}

			if( !isset($tree[$dir]["*"]) && !isset($tree[$dir][$code]) ) { continue; }

			foreach( $resource->all( $relation ) as $value )
			{
				if( is_a( $value, "Graphite_Literal" ) )
				{
					$datatype = $value->datatype();
					if( !isset($datatype) ) { $datatype='literal'; }
					$new_graph->addTriple(
						Graphite::asString($resource),
						Graphite::asString($relation),
						Graphite::asString($value),
						$datatype,
						$value->language() );
				}
				else
				{
					if( isset( $tree[$dir][$code]) )
					{
						$this->_tograph( $tree[$dir][$code], $value, $new_graph );
					}
					if( isset( $tree[$dir]["*"]) )
					{
						$this->_tograph( $tree[$dir]["*"], $value, $new_graph );
					}
					if( $dir == "+" )
					{
						$new_graph->addTriple(
							Graphite::asString($resource),
							Graphite::asString($relation),
							Graphite::asString($value) );
					}
					else
					{
						$new_graph->addTriple(
							Graphite::asString($value),
							Graphite::asString($relation),
							Graphite::asString($resource) );
					}
				}
			}
		}
	}

	function loadSPARQL( $endpoint, $debug = false )
	{
		$bits = $this->_toSPARQL( $this->tree, "", null, "" );
		$n = 0;
		foreach( $bits as $bit )
		{
			$sparql = "CONSTRUCT { ".$bit['construct']." } WHERE { ".$bit['where']." }";
			if( $debug || @$_GET["_graphite_debug"] ) {
				 print "<div style='padding: 1em'><tt>\n\n".htmlspecialchars($sparql)."</tt></div>\n\n";
			}
			$n+=$this->graph->loadSPARQL( $endpoint, $sparql );
		}
		return $n;
	}

	function _toSPARQL($tree, $suffix, $in_dangler = null, $sparqlprefix = "" )
	{
		$bits = array();
		if( !isset( $in_dangler ) )
		{
			$in_dangler = "<".Graphite::asString($this->resource).">";
		}

		$i = 0;
		foreach( $tree as $dir=>$routes )
		{
			if( sizeof($routes) == 0 ) { continue; }

			$pres = array();
			if( isset($routes["*"]) )
			{
				$sub = "?s".$suffix."_".$i;
				$pre = "?p".$suffix."_".$i;
				$obj = "?o".$suffix."_".$i;

				if( $dir == "+" )
				{
					$out_dangler = $obj;
					$sub = $in_dangler;
				}
				else # inverse
				{
					$out_dangler = $sub;
					$obj = $in_dangler;
				}

				$construct = "$sub $pre $obj . ";
				$where = "$sparqlprefix $sub $pre $obj .";
				if( isset( $routes["*"] ) )
				{
					$bits_from_routes = $this->_toSPARQL( $routes["*"], $suffix."_".$i, $out_dangler, "" );
					$i++;
					foreach( $bits_from_routes as $bit )
					{
						$construct .= $bit["construct"];
						$where .= " OPTIONAL { ".$bit["where"]." }";
					}
				}
				$bits []= array( "where"=>$where, "construct"=>$construct );

				foreach( $routes as $pred=>$route )
				{
					if( $pred == "*" ) { continue; }

					$pre = "<".$this->graph->expandURI( $pred ).">";

					$bits_from_routes = $this->_toSPARQL( $route, $suffix."_".$i, $out_dangler, "$sparqlprefix $sub $pre $obj ." );
					$i++;
					foreach( $bits_from_routes as $bit )
					{
						$bits []= $bit;
					}
				}
			}
			else
			{
				foreach( array_keys( $routes ) as $pred )
				{
					$sub = "?s".$suffix."_".$i;
					$pre = "<".$this->graph->expandURI( $pred ).">";
					$obj = "?o".$suffix."_".$i;

					if( $dir == "+" )
					{
						$out_dangler = $obj;
						$sub = $in_dangler;
					}
					else # inverse
					{
						$out_dangler = $sub;
						$obj = $in_dangler;
					}

					$bits_from_routes = $this->_toSPARQL( $routes[$pred],$suffix."_".$i, $out_dangler, "" );
					$i++;

					$construct = "$sub $pre $obj . ";
					$where = "$sparqlprefix $sub $pre $obj .";
					foreach( $bits_from_routes as $bit )
					{
						$construct .= $bit["construct"];
						$where .= " OPTIONAL { ".$bit["where"]." }";
					}

					$bits []= array( "where"=>$where, "construct"=>$construct );
				}
			}
		}

		return $bits;
	} # end _toSPARQL

	function getFormats()
	{
		return array(
			"json"=>"JSON",
			"nt"=>"RDF (Triples)",
			"ttl"=>"RDF (Turtle)",
			"rdf"=>"RDF (XML)",
			"rdf.html" => "RDF (RDF HTML Debug)",
		);
	}

	function handleFormat( $format )
	{
		if( $format == 'json' )
		{
			if( isset( $_GET['callback'] ) )
			{
				header( "Content-type: application/javascript" );
				print $_GET['callback']."( ".$this->toJSON()." );\n";
			}
			else
			{
				header( "Content-type: application/json" );
				print $this->toJSON();
			}

			return true;
		}

		if( $format == 'ttl' )
		{
			header( "Content-type: text/turtle" );
			print $this->toGraph()->serialize( "Turtle" );
			return true;
		}

		if( $format == 'nt' )
		{
			header( "Content-type: text/plain" );
			print $this->toGraph()->serialize( "NTriples" );
			return true;
		}

		if( $format == 'rdf' )
		{
			header( "Content-type: application/rdf+xml" );
			print $this->toGraph()->serialize( "RDFXML" );
			return true;
		}

		if( $format == 'rdf.html' )
		{
			header( "Content-type: text/html" );
			print $this->toGraph()->dump();
			return true;
		}

		if( $format == 'debug' )
		{
			header( "Content-type: text/plain" );
			print $this->toDebug();
			return true;
		}

		return false;
	}
}
class Graphite_InverseRelation extends Graphite_Relation
{
	function nodeType() { return "#inverseRelation"; }
}
class Graphite_Literal extends Graphite_Node
{
	function __construct(Graphite $g, $triple )
	{
		$this->g = $g;
		$this->setTriple($triple);
	}

	/**
	 * Modify the triple / value represented by this instance
	 *
	 * @param array $triple
	 */
	public function setTriple($triple) {
		$this->triple = $triple;
		$this->v = $triple["v"];
	}

	function __toString() {
		return isset($this->triple["v"]) ? Graphite::asString($this->triple['v']) : "";
	}
	function datatype() { return @$this->triple["d"]; }
	function language() { return @$this->triple["l"]; }

	function dumpValueText()
	{
		$r = '"'.$this->v.'"';
		if( isset($this->triple["l"]) && $this->triple["l"])
		{
			$r.="@".$this->triple["l"];
		}
		if( isset($this->triple["d"]) )
		{
			$r.="^^".$this->g->shrinkURI($this->triple["d"]);
		}
		return $r;
	}

	function dumpValueHTML()
	{
		$v = htmlspecialchars( $this->triple["v"],ENT_COMPAT,"UTF-8" );

		$v = preg_replace( "/\t/", "<span class='special_char' style='font-size:70%'>[tab]</span>", $v );
		$v = preg_replace( "/\n/", "<span class='special_char' style='font-size:70%'>[nl]</span><br />", $v );
		$v = preg_replace( "/\r/", "<span class='special_char' style='font-size:70%'>[cr]</span>", $v );
		$v = preg_replace( "/  +/e", "\"<span class='special_char' style='font-size:70%'>\".str_repeat(\"␣\",strlen(\"$0\")).\"</span>\"", $v );
		$r = '"'.$v.'"';

		if( isset($this->triple["l"]) && $this->triple["l"])
		{
			$r.="@".$this->triple["l"];
		}
		if( isset($this->triple["d"]) )
		{
			$r.="^^".$this->g->shrinkURI($this->triple["d"]);
		}
		return $r;
	}

	function nodeType()
	{
		if( isset($this->triple["d"]) )
		{
			return $this->triple["d"];
		}
		return "#literal";
	}

	function dumpValue()
	{
		return "<span style='color:blue'>".$this->dumpValueHTML()."</span>";
	}

	function link() { return $this->__toString(); }
	function prettyLink() { return $this->__toString(); }
}
/**
 * A list to manage Graphite_Resources.
 *
 * To print a nicely formatted list of names, linking to the URIs.
 *
 * print $list->sort( "foaf:name" )->prettyLink()->join( ", ").".";
 *
 * * Note about Graphite methods which can take a list of resources
 *
 * These methods work in a pretty cool way. To make life easier for you they can take a list of resources in any of the following ways.
 *
 * $resource->get() is used as an example, it applies to many other methods.
 *
 * $resource->get( $uri_string )
 * Such as "http://xmlns.com/foaf/0.1/name".
 * $resource->get( $short_uri_string )
 * using any namespace defined with $graph->ns() or just built in. eg. "foaf:name".
 * $resource->get( $resource )
 * An instance of Graphite_resource.
 * $resource->get( $thing, $thing, $thing, ... )
 * $resource->get( array( $thing, $thing, $thing, ... ) )
 * Where each thing is any of $uri_string, $short_uri_string or $resource.
 * $resource->get( $resourcelist )
 * An instance of Graphite_resourceList.
 * This should make it quick and easy to write readable code!
 */
class Graphite_ResourceList extends ArrayIterator
{

	function __construct(Graphite $g, $a=array() )
	{
		$this->g = $g;
		$this->a = $a;
		if( $a instanceof Graphite_ResourceList )
		{
			print "<li>Graphite warning: passing a Graphite_ResourceList as the array passed to new Graphite_ResourceList will make weird stuff happen.</li>";
		}
		parent::__construct( $this->a );
	}

	/**
	 * Returns a string of all the items in the resource list, joined with the given string.
	 * $str = $resourcelist->join( $joinstr );
	 */
	function join( $str )
	{
		$first = 1;
		$l = array();
		foreach( $this as $resource )
		{
			if( !$first ) { $l []= $str; }
			$l []= Graphite::asString($resource);
			$first = 0;
		}
		return join( "", $l );
	}

	/**
	 * Returns a string containing a dump of all the resources in the list. Options is an optional array, same parameters as $options on a dump() of an individual resource. dumpText() does the same thing but with ASCII indents rather than HTML markup.
	 *
	 * $dump = $resourcelist->dump( [$options] );
	 */
	function dump()
	{
		$l = array();
		foreach( $this as $resource )
		{
			$l [] = $resource->dump();
		}
		return join( "", $l );
	}

	/**
	 * Return a list with any duplicates removed, otherwise preserving current order.
	 *
	 * $new_resourcelist = $resourcelist->distinct();
	 */
	public function duplicate()
	{
		$l = array();
		foreach( $this as $resource ) { $l []= $resource; }
		return new Graphite_ResourceList($this->g,$l);
	}

	/**
	 * Return a copy of this resource list sorted by the given property or properties. If a resource has multiple values for a property then one will be used, as with $resource->get().
	 *
	 * $new_resourcelist = $resourcelist->sort( $property );
	 * $new_resourcelist = $resourcelist->sort( *resource list* );
	 */
	public function sort( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }


		/** @todo Remove global state */
		global $graphite_sort_args;
		$graphite_sort_args = array();
		foreach( $args as $arg )
		{
			if( $arg instanceof Graphite_Resource ) { $arg = Graphite::asString($arg); }
			$graphite_sort_args [] = $arg;
		}

		$l = array();
		foreach( $this as $resource ) { $l []= $resource; }
		usort($l, "graphite_sort_list_cmp" );
		return new Graphite_ResourceList($this->g,$l);
	}

	public function uasort( $cmp )
	{
		usort($this->a, $cmp );
	}

	/**
	 * Call $resource-&gt;get(...) on every item in this list and return a resourcelist of the results.
	 *
	 * $new_resourcelist = $resourcelist-&gt;get( $property );
	 * $new_resourcelist = $resourcelist-&gt;get( *resource list* );
	 */
	public function get( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$l = array();
		foreach( $this as $resource )
		{
			$l [] = $resource->get( $args );
		}
		return new Graphite_ResourceList($this->g,$l);
	}

	/**
	 * Call $resource->getLiteral(...) on every item in this list and return a resourcelist of the results.
	 *
	 * $string = $resource->getLiteral( $property );
	 * $string = $resource->getLiteral( *resource list* );
	 */
	public function getLiteral( /* List */)
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$l = array();
		foreach( $this as $resource )
		{
			$l [] = $resource->getLiteral( $args );
		}
		return new Graphite_ResourceList($this->g,$l);
	}
	/**
	 * @deprecated getString deprecated in favour of getLiteral
	 * @see getLiteral
	 */
	public function getString( /* List */ ) { return $this->getLiteral( func_get_args() ); }

	/**
	 * Call $resource->label() on every item in this list and return a resourcelist of the results.
	 *
	 * $new_resourcelist = $resourcelist->label();
	 */
	public function label()
	{
		$l = array();
		foreach( $this as $resource )
		{
			$l [] = $resource->label();
		}
		return new Graphite_ResourceList($this->g,$l);
	}

	/**
	 * Calls link() on each item in the resource list and returns it as an array. The array is an object which you can call join() on, so you can use:
	 *
	 * $array = $resourcelist->link();
	 */
	public function link()
	{
		$l = array();
		foreach( $this as $resource )
		{
			$l [] = $resource->link();
		}
		return new Graphite_ResourceList($this->g,$l);
	}

	/**
	 * Calls prettyLink() on each item in the resource list and returns it as an array. The array is an object which you can call join() on, so you can use:
	 *
	 * $array = $resourcelist->prettyLink();
	 */
	public function prettyLink()
	{
		$l = array();
		foreach( $this as $resource )
		{
			$l [] = $resource->prettyLink();
		}
		return new Graphite_ResourceList($this->g,$l);
	}

	/**
	 * Call $resource->load() on every item in this list and return the total triples from these resources. Careful, this could cause a large number of requests at one go!
	 *
	 * $n = $resourcelist->load();
	 */
	public function load()
	{
		$n = 0;
		foreach( $this as $resource )
		{
			$n += $resource->load();
		}
		return $n;
	}

	/**
	 * Call $resource->allString(...) on every item in this list and return a resourcelist of all the results. As with all(), duplicate resources and eliminated.
	 *
	 * $resource_list = $resource->allString( $property );
	 * $resource_list = $resource->allString( *resource list* );
	 */
	public function allString( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$l = array();
		$done = array();
		foreach( $this as $resource )
		{
			$all = $resource->all( $args );
			foreach( $all as $to_add )
			{
				if( isset($done[Graphite::asString($to_add)]) ) { continue; }
				$l []= Graphite::asString($to_add);
				$done[Graphite::asString($to_add)] = 1;
			}
		}
		return new Graphite_ResourceList($this->g,$l);
	}

	/**
	 * Call $resource->all(...) on every item in this list and return a resourcelist of all the results. Duplicate resources are eliminated.
	 *
	 * $new_resourcelist = $resourcelist->all( $property );
	 * $new_resourcelist = $resourcelist->all( *resource list* );
	 */
	public function all( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$l = array();
		$done = array();
		foreach( $this as $resource )
		{
			$all = $resource->all( $args );
			foreach( $all as $to_add )
			{
				if( isset($done[Graphite::asString($to_add)]) ) { continue; }
				$l []= $to_add;
				$done[Graphite::asString($to_add)] = 1;
			}
		}
		return new Graphite_ResourceList($this->g,$l);
	}

	/**
	 * Create a new resource list with the given resource or list of resources appended on the end of the current resource list.
	 *
	 * $new_resourcelist = $resourcelist->append( $resource );
	 * $new_resourcelist = $resourcelist->append( *resource list* );
	 */
	function append( $x /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$list = $this->duplicate();
		foreach( $args as $arg )
		{
			if( ! $arg instanceof Graphite_Resource ) { $arg = $this->g->resource( $arg ); }
			$list [] = $arg;
		}
		return $list;
	}

	function distinct()
	{
		$l= array();
		$done = array();
		foreach( $this as $resource )
		{
			if( isset( $done[Graphite::asString($resource)] ) ) { continue; }
			$l [] = $resource;
			$done[Graphite::asString($resource)]=1;
		}
		return new Graphite_ResourceList($this->g,$l);
	}

	/**
	 * Create a new resource list with the given resource or list of resources merged with the current list. Functionally the same as calling $resourcelist->append( ... )->distinct()
	 *
	 * $new_resourcelist = $resourcelist->union( $resource );
	 * $new_resourcelist = $resourcelist->union( *resource list* );
	 */
	function union( /* List */ )
	{
		$args = func_get_args();

		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$list = new Graphite_ResourceList($this->g);
		$done = array();
		foreach( $this as $resource )
		{
			if( isset( $done[Graphite::asString($resource)] ) ) { continue; }
			$list [] = $resource;
			$done[Graphite::asString($resource)]=1;
		}
		foreach( $args as $arg )
		{
			if( ! $arg instanceof Graphite_Resource ) { $arg = $this->g->resource( $arg ); }
			if( isset( $done[Graphite::asString($arg)] ) ) { continue; }
			$list [] = $arg;
			$done[Graphite::asString($arg)]=1;
		}
		return $list;
	}

	/**
	 * Create a new resource list with containing only the resources which are in both lists. Only returns one instance of each resource no matter how many duplicates were in either list.
	 *
	 * $new_resourcelist = $resourcelist->intersection( $resource );
	 * $new_resourcelist = $resourcelist->intersection( *resource list* );
	 */
	function intersection( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$list = new Graphite_ResourceList($this->g,array());
		$seen = array();

		foreach( $this as $arg )
		{
			if( ! $arg instanceof Graphite_Resource ) {
				$arg = $this->g->resource( $arg );
			}
			$seen[Graphite::asString($arg)]=1;
		}

		foreach( $args as $arg )
		{
			if( ! $arg instanceof Graphite_Resource ) {
				$arg = $this->g->resource( $arg );
			}
			if( ! isset($seen[Graphite::asString($arg)]) ) {
				continue;
			}
			$list [] = $arg;
		}
		return $list;
	}

	/**
	 * Create a new resource list with containing only the resources which are in $resourcelist but not in the list being passed in. Only returns one instance of each resource no matter how many duplicates   were in either list.
	 *
	 * $new_resourcelist = $resourcelist->except( $resource );
	 * $new_resourcelist = $resourcelist->except( *resource list* );
	 */
	function except( /* List */ )
	{
		$args = func_get_args();
		if( isset($args[0]) && $args[0] instanceof Graphite_ResourceList ) { $args = $args[0]; }
		if( isset($args[0]) && is_array( $args[0] ) ) { $args = func_get_arg( 0 ); }

		$list = new Graphite_ResourceList($this->g,array());
		$exclude = array();

		foreach( $args as $arg )
		{
			if( ! $arg instanceof Graphite_Resource ) { $arg = $this->g->resource( $arg ); }
			$exclude[Graphite::asString($arg)]=1;
		}
		foreach( $this as $arg )
		{
			if( ! $arg instanceof Graphite_Resource ) { $arg = $this->g->resource( $arg ); }
			if( isset($exclude[Graphite::asString($arg)]) ) { continue; }
			$list [] = $arg;
		}
		return $list;
	}

	/**
	 * Create a new resource list containing all resources in the current list of the given type.
	 *
	 * $resource_list = $resource->allOfType( $type_uri );
	 */
	function allOfType( $uri )
	{
		$list = new Graphite_ResourceList( $this->g, array() );
		foreach( $this as $item )
		{
			if( $item->isType( $uri ) )
			{
				$list [] = $item;
			}
		}
		return $list;
	}
}
