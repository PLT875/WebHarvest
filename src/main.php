<?php
/* @file Main script.
 * 
 * Retrieves product data from the main URL and links, outputting results formatted in JSON.
 * 
 * @author petertran
 * 
 */
require_once('WebScraper.php');

// Save the main URL as a page source and DOM document.
$mainURL = "http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html?langId=44&storeId=10151&catalogId=10122&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&applyfilter=Apply+filter";
$pageSource = WebScraper::retrievePageSourceSize($mainURL);
$domDocument = new DomDocument();
@$domDocument->loadHTML($pageSource['source']);

// Retrieve an array of product titles that appear on the main URL.
$titleXPath = 'id(\'productLister\')/ul/li/div/div/div/div/h3/a';
$productTitles = WebScraper::retrieveNodeValuesFromDom($domDocument, $titleXPath);

// Retrieve an array of product links that appear on the main URL.
$linkXPath = 'id(\'productLister\')/ul/li/div/div/div/div/h3/a/@href';
$productLinks = WebScraper::retrieveNodeValuesFromDom($domDocument, $linkXPath);

// Retrieve an array of product unit prices (decimal format) that appear on the main URL.
$pricesXPath = '//p[contains(@class,"pricePerUnit")]';
$productPrices = WebScraper::retrieveNodeValuesFromDom($domDocument, $pricesXPath, '/[^0-9\.]/');

// Retrieve the size of each product link found.
$linkSizes = array();
$productDescriptions = array();
foreach ($productLinks as $link) {
  $linkSource = WebScraper::retrievePageSourceSize($link);
  array_push($linkSizes, $linkSource['size']);
  
  // Retrieve an array of product descriptions that appear on the link.
  $domDocument = new DomDocument();
  @$domDocument->loadHTML($linkSource['source']);
  $descriptionXPath = 'id(\'information\')/productcontent/htmlcontent/div[1]/p[1]'; 
  $productDescription = WebScraper::retrieveNodeValuesFromDom($domDocument, $descriptionXPath);
  array_push($productDescriptions, $productDescription[0]);
}

// The product data attributes are all present on the main URL. So the productTitle 
// key can be used across the other arrays to build a complete product record.
$results = array();
foreach ($productTitles as $key => $value) {
  $product = array('title' => $productTitles[$key],
      'size' => $linkSizes[$key],
      'unit_price' => $productPrices[$key],
      'description' => $productDescriptions[$key]
    );
  
  array_push($results, $product);
}

// Calculate the total of all product unit prices.
$priceTotal = array_sum($productPrices);

$summary_keys = array('results', 'total');
$summary_values = array($results, $priceTotal);
$summary = array_combine($summary_keys, $summary_values);
$json_summary = json_encode($summary, JSON_PRETTY_PRINT);
echo $json_summary;

