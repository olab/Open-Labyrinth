<?php 
//generates Mapping between DBPedia Classes and Schema.org Classes
$output=array();
$output['name']='DBPedia/Schema.org Mapping';
$output['description']='Mapping between DBPedia Classes and Schema.org Classes';
$output['creator']='Ali Khalili';
$output['mapping']=array();

$uri='mappingToDBPedia.rdf';
$RDF = file_get_contents($uri);
$RDF = str_replace('rdf:', 'rdf_', $RDF);
$RDF = str_replace('owl:', 'owl_', $RDF);
$RDF = str_replace('rdfs:', 'rdfs_', $RDF);
$RDF = str_replace('xml:', 'xml_', $RDF);
$XML = simplexml_load_string($RDF);
foreach ($XML->owl_Class as $num => $description) {
  $flag=0;
  $attributes = $description->attributes();
  $pieces = explode("http://dbpedia.org/ontology/", $attributes['rdf_about']);
  $className=$pieces[1];
  $output['mapping'][$className]=array();
  $output['mapping'][$className]['about']=(string)$attributes['rdf_about'];
  foreach($description->children() as $child){
  	$attr = $child->attributes();
  	if($child->getName()=='rdfs_label' && $attr['xml_lang']=='en'){
  		$output['mapping'][$className]['text']=(string)$child;
  	}elseif($child->getName()=='rdfs_subClassOf'){
  		if (strpos($attr['rdf_resource'],'schema.org') !== false) {
  			$output['mapping'][$className]['relation']='SubClass';
  			$output['mapping'][$className]['target']=array();
  			$output['mapping'][$className]['target']['about']=(string)$attr['rdf_resource'];
  		 	$pieces = explode("http://schema.org/", $attr['rdf_resource']);
  		 	$output['mapping'][$className]['target']['name']=$pieces[1];
  		 	$flag=1;
		}
  	}elseif($child->getName()=='owl_equivalentClass'){
  	  	if (strpos($attr['rdf_resource'],'schema.org') !== false) {
  			$output['mapping'][$className]['relation']='Equivalent';
  			$output['mapping'][$className]['target']=array();
  			$output['mapping'][$className]['target']['about']=(string)$attr['rdf_resource'];
  		 	$pieces = explode("http://schema.org/", $attr['rdf_resource']);
  		 	$output['mapping'][$className]['target']['name']=$pieces[1]; 	
  		 	$flag=1;	 	
		} 		
  	}
  }
  //to show only DBPedia classes that have relation to Schema.org
  if(!$flag){
  	unset($output['mapping'][$className]);
  }
}
$fp = fopen('dbToSchema.json', 'w');
fwrite($fp, json_encode($output));
fclose($fp);
echo "Mapping is created as dbToSchema.json";
?>