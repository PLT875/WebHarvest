<?php
/**
 * @file WebScraperTest.php
 * 
 * Test suite for src/WebScraper.php
 *
 * @author petertran
 */
require_once('./src/WebScraper.php');

class WebScraperTest extends PHPUnit_Framework_TestCase {
  private $testPageSource = "<!DOCTYPE html>
      <html>
        <body>
          <h1>Fruit List</h1>
            <div id=\"productInfo\">
              <ul>
                <li class=\"title\">Apple</li>
                <li class=\"unitPrice\">£1.00/unit</li>
                <li class=\"description\">Green</li>
              </ul>
              <ul>
                <li class=\"title\">Banana</li>
                <li class=\"unitPrice\">£2.00/unit</li>
                <li class=\"description\">Yellow</li>
              </ul>
              <ul>
                <li class=\"title\">Tomato</li>
                <li class=\"unitPrice\">£3.00/unit</li>
                <li class=\"description\">Red</li>
              </ul>
            </div>
        </body>
      </html>";
  
  /**
   * Test that all titles are returned in from $testPageSource.
   */
  public function testRetrieveNodeValuesFromDom() {
    $domDocument = new DomDocument();
    @$domDocument->loadHTML($this->testPageSource);
    $titleXPath = 'id(\'productInfo\')/ul/li[@class="title"]';
    $nodeValues = WebScraper::retrieveNodeValuesFromDom($domDocument, $titleXPath);
    $this->assertEquals($nodeValues[0], "Apple");
    $this->assertEquals($nodeValues[1], "Banana");  
    $this->assertEquals($nodeValues[2], "Tomato");  
  }
  
  /**
   * Test that all unit prices are returned in decimal format from $testPageSource.
   */
  public function testRetrieveNodeValuesFromDomWithClean() {
    $domDocument = new DomDocument();
    @$domDocument->loadHTML($this->testPageSource);
    $titleXPath = 'id(\'productInfo\')/ul/li[@class="unitPrice"]';
    $nodeValues = WebScraper::retrieveNodeValuesFromDom($domDocument, $titleXPath, '/[^0-9\.]/');
    $this->assertEquals($nodeValues[0], "1.00");
    $this->assertEquals($nodeValues[1], "2.00");  
    $this->assertEquals($nodeValues[2], "3.00");  
  }
  
  /**
   * Test that DOMElement is returned when searching for ID 'productLister in $testPageSource.
   */
  public function testRetrieveElementByIdFind() {
    $testFind = WebScraper::retrieveElementById($this->testPageSource, 'productInfo');
    $classOfTestFind = get_class($testFind);
    $this->assertEquals($classOfTestFind, "DOMElement");
  }
  
  /**
   * Test that NULL is returned when searching for ID 'product in $testPageSource.
   */
  public function testRetrieveElementByIdNoFind() {
    $testNoFind = WebScraper::retrieveElementByID($this->testPageSource, 'product');
    $this->assertEquals(is_null($testNoFind), TRUE);
  }
  
}
