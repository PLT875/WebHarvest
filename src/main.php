<?php
/* @file Main script.
 * 
 * @author petertran
 * 
 */
require_once('WebScraper.php');
require_once('Product.php');

// Save the mainURL as a page source and DOM document.
$mainURL = "http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html?langId=44&storeId=10151&catalogId=10122&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&applyfilter=Apply+filter";
$pageSource = WebScraper::retrievePageSourceSize($mainURL);
$domDocument = new DomDocument();
@$domDocument->loadHTML($pageSource['source']);

// Search and retrieve an array of product titles on the mainURL.
$titleXPath = 'id(\'productLister\')/ul/li/div/div/div/div/h3/a';
$productTitles = WebScraper::retrieveNodeValuesFromDom($domDocument, $titleXPath);
//print_r("<pre>"); print_r($productTitles); print_r("</pre>");

// Search and retrieve an array of product links on the main URL.
$linkXPath = 'id(\'productLister\')/ul/li/div/div/div/div/h3/a/@href';
$productLinks = WebScraper::retrieveNodeValuesFromDom($domDocument, $linkXPath);
//print_r("<pre>"); print_r($productLinks); print_r("</pre>");

// Search and retrieve an array of product unit prices on the main URL.
// Remove non numeric and non decimal point characters in the unit prices.
$pricesXPath = '//p[contains(@class,"pricePerUnit")]';
$productPrices = WebScraper::retrieveNodeValuesFromDom($domDocument, $pricesXPath, '/[^0-9\.]/');
//print_r("<pre>"); print_r($productPrices); print_r("</pre>");

// Calculate total product unit prices.
$priceTotal = array_sum($productPrices);
//print_r("</pre>"); print_r($priceTotal); print_r("</pre>");

// Search and retrieve the size of each product link.
$linkSizes = array();
$productDescriptions = array();
foreach ($productLinks as $link) {
  $linkSource = WebScraper::retrievePageSourceSize($link);
  array_push($linkSizes, $linkSource['size']);
  
  // Search and retrieve the product description on the link.
  $domDocument = new DomDocument();
  @$domDocument->loadHTML($linkSource['source']);
  $descriptionXPath = 'id(\'information\')/productcontent/htmlcontent/div[1]/p[1]'; 
  $productDescription = WebScraper::retrieveNodeValuesFromDom($domDocument, $descriptionXPath);
  array_push($productDescriptions, $productDescription[0]);
}

//print_r("<pre>"); print_r($linkSizes); print_r("</pre>");
//print_r("<pre>"); print_r($productDescriptions); print_r("</pre>");


$b = array("result"=>array(array("a"=> 1, "b"=>2), array("a"=> 1, "b"=>2)), "a"=> 1);
$c = json_encode($b, JSON_PRETTY_PRINT);
print_r($c);

