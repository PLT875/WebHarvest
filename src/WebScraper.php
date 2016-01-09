<?php
/** 
 * @file WebScraper.php
 * 
 * Utility functions for web scraping.
 * 
 * @author petertran
 */
class WebScraper {
    
  /**
   * Function to retrieve the page source and size of a given URL.
   * 
   * @param String $url
   * 
   * @return Array $pageSourceSize
   * - String $source
   * - String $size (unit kb)
   */
  public static function retrievePageSourceSize($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $source = curl_exec($curl);
    $size = curl_getinfo($curl, CURLINFO_SIZE_DOWNLOAD) / 1000;
    $size .= "kb";
    curl_close($curl);
    $pageSourceSize = array('source' => $source, 'size' => $size);
    return $pageSourceSize;
  }
  
  /**
   * Function to retrieve cleaned node values from the DOM document using a given XPath query.
   * 
   * @param DomDocument $domDocument
   * @param String $queryXPath
   * @param String $pattern
   * - The preg_replace pattern, to remove unwanted characters from the fetched nodeValue.
   * 
   * @return Array $nodeValues
   */
  public static function retrieveNodeValuesFromDom($domDocument, $queryXPath, $pattern = NULL) {
    $nodeValues = array();
    $domXPath = new DOMXPath($domDocument);
    $domNodeList = $domXPath->query($queryXPath);
    foreach($domNodeList as $domAttr){
      $nodeValue = is_null($pattern) 
          ? trim($domAttr->nodeValue )
          : preg_replace($pattern, '', trim($domAttr->nodeValue));
      
      array_push($nodeValues, $nodeValue);
    }
   
    return $nodeValues;
  }
  
  /**
   * N.B. Not used.
   * 
   * Function to retrieve the DOM element of a page for a given element ID.
   * 
   * @param String $pageSource
   * @param type $id
   * 
   * @return DOMElement $domElement
   */
  public static function retrieveElementById($pageSource, $id) {
    $domDocument = new DOMDocument();
    @$domDocument->loadHTML($pageSource);
    $domElement = $domDocument->getElementById($id);
    return $domElement;
  }
  
}