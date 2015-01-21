<?php

///https://github.com/mikecurtis1/curtis/blob/master/entrez/Entrez.php

// http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=love
// http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=23516437,23514720,23514212&retmode=xml
// http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&query_key=1&WebEnv=NCID_1_1898369_165.112.9.34_5555_1364478312_1258629992&retstart=1&retmax=3&retmode=xml&rettype=Abstract

class Entrez
{
    private $_apiDb = '';
    private $_apiBaseUrl = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/';
    private $_apiSearchFunc = 'esearch.fcgi';
    private $_apiFetchFunc = 'efetch.fcgi';
    private $_hits = 0;
    private $_queryKey = '';
    private $_webEnv = '';
    private $_translation_stack = array();
    private $_ids = array();
    private $_results = array();

    public function __construct($db=''){
        $this->_apiDb = $db;
    }

    public function search($term=''){
        $url = $this->_apiBaseUrl.$this->_apiSearchFunc.'?usehistory=y&db='.urlencode($this->_apiDb).'&term='.urlencode($term);
        $xml = file_get_contents($url);
        $this->_setHits($xml);
        $this->_setQueryKey($xml);
        $this->_setWebEnv($xml);
        $this->_setTranslationStack($xml);
        $this->_setIds($xml);
        return $this->_hits;
    }

    private function _setHits($str=''){
        $this->_hits = intval($this->_getValue('Count',$str));
    }

    private function _setQueryKey($str=''){
        $this->_queryKey = $this->_getValue('QueryKey',$str);
    }

    private function _setWebEnv($str=''){
        $this->_webEnv = $this->_getValue('WebEnv',$str);
    }

    private function _setTranslationStack($str=''){
        $term_sets = $this->_getTag('TermSet',$str);
        foreach ( $term_sets as $i => $term_set ) {
            $term = $this->_getValue('Term',$term_set);
            $count = $this->_getValue('Count',$term_set);
            if ( $term !== '' && $count !== '' ) {
                $this->_translation_stack[$term] = $count;
            }
        }
    }

    public function getTranslationStack(){
        return $this->_translation_stack;
    }

    private function _setIds($str=''){
        $idlist = $this->_getTag('IdList',$str,FALSE);
        $this->_ids = $this->_getTag('Id',$str);
    }

    public function fetch($retstart=NULL,$retmax=NULL,$retmode='xml',$rettype='Abstract'){
        if ( $retstart !== NULL && $retmax !== NULL ) {
            $url = $this->_apiBaseUrl.$this->_apiFetchFunc.'?'.'&db='.urlencode($this->_apiDb).'&query_key='.urlencode($this->_queryKey).'&WebEnv='.urlencode($this->_webEnv).'&retstart='.$retstart.'&retmax='.$retmax.'&retmode='.$retmode.'&rettype='.$rettype;
        } else {
            $url = $this->_apiBaseUrl.$this->_apiFetchFunc.'?db='.urlencode($this->_apiDb).'&id='.implode(',',$this->_ids).'&retmode='.$retmode;
        }
        $xml = file_get_contents($url);
        $this->_setResults($xml);

        return $url;
    }

    private function _setResults($str=''){
        $articles = $this->_getTag('PubmedArticle',$str,FALSE);
        foreach ( $articles as $i => $article ) {
            $this->_results[$i]['pmid'] = $this->_getPMID($article);
            $this->_results[$i]['doi'] = $this->_getDOI($article);
            $this->_results[$i]['title'] = $this->_getValue('ArticleTitle',$article);
            $this->_results[$i]['description'] = implode(' ',$this->_getTag('AbstractText',$article));
            $this->_results[$i]['pagination'] = $this->_getValue('MedlinePgn',$article);
            $this->_results[$i]['issn'] = $this->_getJournalData($article,'ISSN');
            $this->_results[$i]['journal'] = $this->_getJournalData($article,'Title');
            $this->_results[$i]['volume'] = $this->_getJournalData($article,'Volume');
            $this->_results[$i]['issue'] = $this->_getJournalData($article,'Issue');
            $this->_results[$i]['pubyear'] = $this->_getJournalData($article,'Year');
            $this->_results[$i]['month'] = $this->_getJournalData($article,'Month');
            $this->_results[$i]['language'] = $this->_getJournalData($article,'Language');
            $this->_results[$i]['authors'] = $this->_getAuthorData($article);
        }
    }

    private function _getJournalData($str='',$field=''){
        $temp = $this->_getValue('Journal',$str);
        return $this->_getValue($field,$temp);
    }

    private function _getAuthorData($str=''){
        $authors = array();
        $author_list = $this->_getValue('AuthorList',$str);
        $temp = $this->_getTag('Author',$author_list);
        foreach ( $temp as $i => $author ) {
            $name = '';
            $lastname = $this->_getValue('LastName',$author);
            $forename = $this->_getValue('ForeName',$author);
            $initials = $this->_getValue('Initials',$author);
            if ( $lastname !== '' ) {
                $name .= $lastname.', ';
            }
            if ( $forename !== '' ) {
                $name .= $forename.'';
            }
            if ( $name !== '' ) {
                $authors[$i] = $name;
            }
        }

        return $authors;
    }

    private function _getDOI($str=''){
        $temp = $this->_getValue('ArticleIdList',$str);
        preg_match_all("/\<ArticleId IdType\=\"doi\"\>(.*?)\<\/ArticleId\>/s",$temp,$matches);
        if ( isset($matches[1][0]) ) {
            return $matches[1][0];
        } else {
            return '';
        }
    }

    private function _getPMID($str=''){
        $temp = $this->_getValue('ArticleIdList',$str);
        preg_match_all("/\<ArticleId IdType\=\"pubmed\"\>(.*?)\<\/ArticleId\>/s",$temp,$matches);
        if ( isset($matches[1][0]) ) {
            return $matches[1][0];
        } else {
            return '';
        }
    }

    private function _getValue($element='',$str=''){
        $temp = $this->_getTag($element,$str);
        if ( isset($temp[0]) ) {
            return trim($temp[0]);
        } else {
            return '';
        }
    }

    private function _getTag($tag='',$str='',$content_only=TRUE,$short_tag=FALSE,$attr=''){
        if ( $short_tag === FALSE ) {
            preg_match_all("/\<".$tag.".*?\>(.*?)\<\/".$tag."\>/s",$str,$matches);
        } else {
            preg_match_all("/\<".$tag.".*?".$attr."\=\"(.*?)\".*?\/\>/s",$str,$matches);
        }
        if ( $content_only === FALSE ) {
            if ( isset($matches[0]) ) {
                return $matches[0];
            }
        } elseif ( $content_only === TRUE ) {
            if ( isset($matches[1]) ) {
                return $matches[1];
            }
        } else {
            return array();
        }
    }

    public function getResults(){
        return $this->_results;
    }
}
?>