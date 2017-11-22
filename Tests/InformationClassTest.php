<?php

namespace Annotations\Tests;

use PHPUnit\Framework\TestCase;

use Annotations\Analyzer;
use Annotations\InformationClass;

class InformationClassTest extends TestCase{

  /**
   * @dataProvider providerForInformationClassTest
   * @covers Annotations\InformationClass::__construct
   * @covers Annotations\InformationClass::setInterfaces
   * @covers Annotations\InformationClass::setMethods
   * @covers Annotations\InformationClass::setAttributes
   * @covers Annotations\InformationClass::parseCommentary
   */
  public function testInformationClass(string $class, $expected){
    $analyzer = new Analyzer();
    $informations = new InformationClass($class, $analyzer);
    $this->assertInstanceOf(InformationClass::class, $informations);
  }



  public function providerForInformationClassTest(){
    require_once __DIR__.'/TestsCase/ExempleInformation.php';
    $params1 = array('Annotations\Tests\TestsCase\ExempleInformation', false);

    return array(
      $params1,
    );
  }


}
