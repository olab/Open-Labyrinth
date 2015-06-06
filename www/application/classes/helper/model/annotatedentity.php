<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 20/6/2014
 * Time: 1:43 μμ
 */

class Helper_Model_AnnotatedEntity extends Kohana_Object{

    public static  $semanticTypes = array(
        "T053"=>array("id"=>"Behavior","label"=>"Behavior"),
        "T052"=>array("id"=>"Activity","label"=>"Activity"),
        "T056"=>array("id"=>"DailyOrRecreationalActivity","label"=>"Daily or Recreational Activity"),
        "T051"=>array("id"=>"Event","label"=>"Event"),
        "T064"=>array("id"=>"GovernmentalOrRegulatoryActivity","label"=>"Governmental or Regulatory Activity"),
        "T055"=>array("id"=>"IndividualBehavior","label"=>"Individual Behavior"),
        "T066"=>array("id"=>"MachineActivity","label"=>"Machine Activity"),
        "T057"=>array("id"=>"OccupationalActivity","label"=>"Occupational Activity"),
        "T054"=>array("id"=>"SocialBehavior","label"=>"Social Behavior"),
        "T017"=>array("id"=>"AnatomicalStructure","label"=>"Anatomical Structure"),
        "T029"=>array("id"=>"BodyLocationOrRegion","label"=>"Body Location or Region"),
        "T023"=>array("id"=>"BodyPart,Organ,OrOrganComponent","label"=>"Body Part, organ, or organ Component"),
        "T030"=>array("id"=>"BodySpaceOrJunction","label"=>"Body Space or Junction"),
        "T031"=>array("id"=>"BodySubstance","label"=>"Body Substance"),
        "T022"=>array("id"=>"BodySystem","label"=>"Body System"),
        "T025"=>array("id"=>"Cell","label"=>"Cell"),
        "T026"=>array("id"=>"CellComponent","label"=>"Cell Component"),
        "T018"=>array("id"=>"EmbryonicStructure","label"=>"Embryonic Structure"),
        "T021"=>array("id"=>"FullyFormedAnatomicalStructure","label"=>"Fully Formed Anatomical Structure"),
        "T024"=>array("id"=>"Tissue","label"=>"Tissue"),
        "T116"=>array("id"=>"AminoAcid,Peptide,OrProtein","label"=>"Amino Acid, Peptide, or Protein"),
        "T195"=>array("id"=>"Antibiotic","label"=>"Antibiotic"),
        "T123"=>array("id"=>"BiologicallyActiveSubstance","label"=>"Biologically Active Substance"),
        "T122"=>array("id"=>"BiomedicalOrDentalMaterial","label"=>"Biomedical or Dental Material"),
        "T118"=>array("id"=>"Carbohydrate","label"=>"Carbohydrate"),
        "T103"=>array("id"=>"Chemical","label"=>"Chemical"),
        "T120"=>array("id"=>"ChemicalViewedFunctionally","label"=>"Chemical Viewed Functionally"),
        "T104"=>array("id"=>"ChemicalViewedStructurally","label"=>"Chemical Viewed Structurally"),
        "T200"=>array("id"=>"ClinicalDrug","label"=>"Clinical Drug"),
        "T111"=>array("id"=>"Eicosanoid","label"=>"Eicosanoid"),
        "T196"=>array("id"=>"Element,Ion,OrIsotope","label"=>"Element, Ion or Isotope"),
        "T126"=>array("id"=>"Enzyme","label"=>"Enzyme"),
        "T131"=>array("id"=>"HazardousOrPoisonousSubstance","label"=>"Hazardous or Poisonous Substance"),
        "T125"=>array("id"=>"Hormone","label"=>"Hormone"),
        "T129"=>array("id"=>"ImmunologicFactor","label"=>"Immunologic Factor"),
        "T130"=>array("id"=>"Indicator,Reagent,OrDiagnosticAid","label"=>"Indicator, Reagent or DiagnosticAid"),
        "T197"=>array("id"=>"InorganicChemical","label"=>"Inorganic Chemical"),
        "T119"=>array("id"=>"Lipid","label"=>"Lipid"),
        "T124"=>array("id"=>"NeuroreactiveSubstanceOrBiogenicAmine","label"=>"Neuroreactive Substance or Biogenic Amine"),
        "T114"=>array("id"=>"NucleicAcid,Nucleoside,OrNucleotide","label"=>"Nucleic Acid, Nucleoside or Nucleotide"),
        "T109"=>array("id"=>"OrganicChemical","label"=>"organic Chemical"),
        "T115"=>array("id"=>"OrganophosphorusCompound","label"=>"organophosphorus Compound"),
        "T121"=>array("id"=>"PharmacologicSubstance","label"=>"Pharmacologic Substance"),
        "T192"=>array("id"=>"Receptor","label"=>"Receptor"),
        "T110"=>array("id"=>"Steroid","label"=>"Steroid"),
        "T127"=>array("id"=>"Vitamin","label"=>"Vitamin"),
        "T185"=>array("id"=>"Classification","label"=>"Classification"),
        "T077"=>array("id"=>"ConceptualEntity","label"=>"Conceptual Entity"),
        "T169"=>array("id"=>"FunctionalConcept","label"=>"Functional Concept"),
        "T102"=>array("id"=>"GroupAttribute","label"=>"Group Attribute"),
        "T078"=>array("id"=>"IdeaOrConcept","label"=>"Idea or Concept"),
        "T170"=>array("id"=>"IntellectualProduct","label"=>"Intellectual Product"),
        "T171"=>array("id"=>"Language","label"=>"Language"),
        "T080"=>array("id"=>"QualitativeConcept","label"=>"Qualitative Concept"),
        "T081"=>array("id"=>"QuantitativeConcept","label"=>"Quantitative Concept"),
        "T089"=>array("id"=>"RegulationOrLaw","label"=>"Regulation or Law"),
        "T082"=>array("id"=>"SpatialConcept","label"=>"Spatial Concept"),
        "T079"=>array("id"=>"TemporalConcept","label"=>"Temporal Concept"),
        "T203"=>array("id"=>"DrugDeliveryDevice","label"=>"Drug Delivery Device"),
        "T074"=>array("id"=>"MedicalDevice","label"=>"Medical Device"),
        "T075"=>array("id"=>"ResearchDevice","label"=>"Research Device"),
        "T020"=>array("id"=>"AcquiredAbnormality","label"=>"Acquired Abnormality"),
        "T190"=>array("id"=>"AnatomicalAbnormality","label"=>"Anatomical Abnormality"),
        "T049"=>array("id"=>"CellOrMolecularDysfunction","label"=>"Cell or Molecular Dysfunction"),
        "T019"=>array("id"=>"CongenitalAbnormality","label"=>"Congenital Abnormality"),
        "T047"=>array("id"=>"DiseaseOrSyndrome","label"=>"Disease or Syndrome"),
        "T050"=>array("id"=>"ExperimentalModelOfDisease","label"=>"Experimental Model Of Disease"),
        "T033"=>array("id"=>"Finding","label"=>"Finding"),
        "T037"=>array("id"=>"InjuryOrPoisoning","label"=>"Injury or Poisoning"),
        "T048"=>array("id"=>"MentalOrBehavioralDysfunction","label"=>"Mental or Behavioral Dysfunction"),
        "T191"=>array("id"=>"NeoplasticProcess","label"=>"Neoplastic Process"),
        "T046"=>array("id"=>"PathologicFunction","label"=>"Pathologic Function"),
        "T184"=>array("id"=>"SignOrSymptom","label"=>"Sign or Symptom"),
        "T087"=>array("id"=>"AminoAcidSequence","label"=>"Amino Acid Sequence"),
        "T088"=>array("id"=>"CarbohydrateSequence","label"=>"Carbohydrate Sequence"),
        "T028"=>array("id"=>"GeneOrGenome","label"=>"Gene or Genome"),
        "T085"=>array("id"=>"MolecularSequence","label"=>"Molecular Sequence"),
        "T086"=>array("id"=>"NucleotideSequence","label"=>"Nucleotide Sequence"),
        "T083"=>array("id"=>"GeographicArea","label"=>"Geographic Area"),
        "T100"=>array("id"=>"AgeGroup","label"=>"Age Group"),
        "T011"=>array("id"=>"Amphibian","label"=>"Amphibian"),
        "T008"=>array("id"=>"Animal","label"=>"Animal"),
        "T194"=>array("id"=>"Archaeon","label"=>"Archaeon"),
        "T007"=>array("id"=>"Bacterium","label"=>"Bacterium"),
        "T012"=>array("id"=>"Bird","label"=>"Bird"),
        "T204"=>array("id"=>"Eukaryote","label"=>"Eukaryote"),
        "T099"=>array("id"=>"FamilyGroup","label"=>"Family Group"),
        "T013"=>array("id"=>"Fish","label"=>"Fish"),
        "T004"=>array("id"=>"Fungus","label"=>"Fungus"),
        "T096"=>array("id"=>"Group","label"=>"Group"),
        "T016"=>array("id"=>"Human","label"=>"Human"),
        "T015"=>array("id"=>"Mammal","label"=>"Mammal"),
        "T001"=>array("id"=>"Organism","label"=>"organism"),
        "T101"=>array("id"=>"PatientOrDisabledGroup","label"=>"Patient or Disabled Group"),
        "T002"=>array("id"=>"Plant","label"=>"Plant"),
        "T098"=>array("id"=>"PopulationGroup","label"=>"Population Group"),
        "T097"=>array("id"=>"ProfessionalOrOccupationalGroup","label"=>"Professional or Occupational Group"),
        "T014"=>array("id"=>"Reptile","label"=>"Reptile"),
        "T010"=>array("id"=>"Vertebrate","label"=>"Vertebrate"),
        "T005"=>array("id"=>"Virus","label"=>"Virus"),
        "T071"=>array("id"=>"Entity","label"=>"Entity"),
        "T168"=>array("id"=>"Food","label"=>"Food"),
        "T073"=>array("id"=>"ManufacturedObject","label"=>"Manufactured Object"),
        "T072"=>array("id"=>"PhysicalObject","label"=>"Physical Object"),
        "T167"=>array("id"=>"Substance","label"=>"Substance"),
        "T091"=>array("id"=>"BiomedicalOccupationOrDiscipline","label"=>"Biomedical Occupation or Discipline"),
        "T090"=>array("id"=>"OccupationOrDiscipline","label"=>"Occupation or Discipline"),
        "T093"=>array("id"=>"HealthCareRelatedOrganization","label"=>"Health Care Related organization"),
        "T092"=>array("id"=>"Organization","label"=>"organization"),
        "T094"=>array("id"=>"ProfessionalSociety","label"=>"Professional Society"),
        "T095"=>array("id"=>"Self-HelpOrReliefOrganization","label"=>"Self-Help or Relief organization"),
        "T038"=>array("id"=>"BiologicFunction","label"=>"Biologic Function"),
        "T069"=>array("id"=>"EnvironmentalEffectOfHumans","label"=>"Environmental Effect Of Humans"),
        "T068"=>array("id"=>"Human-CausedPhenomenonOrProcess","label"=>"Human-Caused Phenomenon or Process"),
        "T034"=>array("id"=>"LaboratoryOrTestResult","label"=>"Laboratory or Test Result"),
        "T070"=>array("id"=>"NaturalPhenomenonOrProcess","label"=>"Natural Phenomenon or Process"),
        "T067"=>array("id"=>"PhenomenonOrProcess","label"=>"Phenomenon or Process"),
        "T043"=>array("id"=>"CellFunction","label"=>"Cell Function"),
        "T201"=>array("id"=>"ClinicalAttribute","label"=>"Clinical Attribute"),
        "T045"=>array("id"=>"GeneticFunction","label"=>"Genetic Function"),
        "T041"=>array("id"=>"MentalProcess","label"=>"Mental Process"),
        "T044"=>array("id"=>"MolecularFunction","label"=>"Molecular Function"),
        "T032"=>array("id"=>"OrganismAttribute","label"=>"organism Attribute"),
        "T040"=>array("id"=>"OrganismFunction","label"=>"organism Function"),
        "T042"=>array("id"=>"OrganOrTissueFunction","label"=>"organ or Tissue Function"),
        "T039"=>array("id"=>"PhysiologicFunction","label"=>"Physiologic Function"),
        "T060"=>array("id"=>"DiagnosticProcedure","label"=>"Diagnostic Procedure"),
        "T065"=>array("id"=>"EducationalActivity","label"=>"Educational Activity"),
        "T058"=>array("id"=>"HealthCareActivity","label"=>"Health Care Activity"),
        "T059"=>array("id"=>"LaboratoryProcedure","label"=>"Laboratory Procedure"),
        "T063"=>array("id"=>"MolecularBiologyResearchTechnique","label"=>"Molecular Biology Research Technique"),
        "T062"=>array("id"=>"ResearchActivity","label"=>"Research Activity"),
        "T061"=>array("id"=>"TherapeuticOrPreventiveProcedure","label"=>"Therapeutic or Preventive Procedure"),

    );

    private $input;

    public function __construct(){


    }

    private $entities;

    public static function normalize($text){
        $re = '/<.*>/U';
        preg_match_all($re, $text, $matches,PREG_OFFSET_CAPTURE);


        foreach ($matches[0] as $match) {
            $length= strlen($match[0]);

            $offset = $match[1];
            $replacement =  str_repeat(" ",$length);
            $text = substr_replace($text,$replacement,$offset, $length);

        }


        return $text;




    }

    public function load($text){

        $normalized_text = self::normalize($text);

        $url = "http://data.bioontology.org/annotator?text=$normalized_text&max_level=0&longest_only=false&include=prefLabel,semanticType";

        $key = "1bea74bb-234e-4bd2-b141-86c70591ea46";

        $url .= "&apikey=$key&format=json";

        $params = array(
            "text"=>$normalized_text,
            "max_level"=>0,
            "longest_only" => "false",
            "include" => "prefLabel,semanticType",
            "apikey"=>$key,
            "format"=>"json",
        );

        $request = Request::factory($url)
            ->method('POST')
            ->post($params);

        $response = $request->execute();



        $this->input = json_decode($response->body());

    }


    public function parse(){

        $found = array();
//var_dump($this->input);die;
        foreach($this->input as $element){

            $annotation = $element->annotations[0];
            $position = $annotation->from;
            $class = $element->annotatedClass;
            if(isset($found[$position])){
                //check for existing semantic term, replace only if we found a semantic term we didn't have already
                if(isset($found[$position]["strongTyped"])&& $found[$position]["strongTyped"]==true){
                    continue;
                }
                else{
                    if(!isset($class->semanticType)){
                        continue;
                    }
                }
            }

            $previousItem = end($found);

            if($annotation->from-1<$previousItem['to'] && !(
                    $annotation->from-1==$previousItem['from']
                    &&
                    $annotation->to==$previousItem['to']
                ))continue;

            $found[$position] = array();

            $found[$position]['uri'] = $class->{"@id"};
            $found[$position]['from'] = $annotation->from-1;
            $found[$position]['to'] = $annotation->to;
            $found[$position]['label'] = $class->prefLabel;
            $found[$position]['text'] = strtolower($annotation->text);
            //var_dump($class);die;
            if(isset($class->semanticType)){

                    var_dump($class)


                $found[$position]['semanticType'] = $class->semanticType[0];
                if(isset(self::$semanticTypes[$class->semanticType[0]])){
                    $found[$position]['semanticType'] ="schema:". self::$semanticTypes[$class->semanticType[0]]['id'];
                }
                $found[$position]["strongTyped"] = true;
            }

            else{
                $found[$position]['semanticType'] = $class->{"@type"};
            }

        }

        $this->entities= $found;


    }

    public function output(){

        $out = array("Resources"=>$this->entities);
        return $out;

    }


    public static function generateDummyEntities(){
        $types = array();
        foreach (self::$semanticTypes as $type) {
            $types [$type['id']] =
                    array(
                        'ancestors' =>
                            array(
                                0 => 'Thing',
                            ),
                        'comment' => '',
                        'comment_plain' => '',
                        'id' => $type['id'],
                        'label' => $type['label'],
                        'properties' =>
                            array(

                                0 => 'name',
                            ),
                        'specific_properties' =>
                            array(
                            ),
                        'subtypes' =>
                            array(),

                        'url' => 'http://schema.org/'.$type['id'],
                        'level' => 1,

            );


        }
        return array("properties" =>
            array(
                "name"=> array (
                    'comment' => 'The name of the item.',
                    'comment_plain' => 'The name of the item.',
                    'domains' =>
                        array (
                            0 => 'Thing',
                        ),
                    'id' => 'name',
                    'label' => 'Name',
                    'ranges' =>
                        array (
                            0 => 'Text',
                        ),
                )
            ),"types"=>$types);

    }

} 