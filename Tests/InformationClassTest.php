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
   * @covers Annotations\InformationClass::getNamespace
   * @covers Annotations\InformationClass::getClassname
   * @covers Annotations\InformationClass::getParent
   * @covers Annotations\InformationClass::getInterfaces
   * @covers Annotations\InformationClass::getMethods
   * @covers Annotations\InformationClass::getAttributes
   * @covers Annotations\InformationClass::getFilepath
   */
  public function testInformationClass(string $class, $expected){
    $analyzer = new Analyzer();
    $informations = new InformationClass($class, $analyzer);
    $this->assertInstanceOf(InformationClass::class, $informations);
    $this->assertEquals('Annotations\Tests\TestsCase', $informations->getNamespace());
    $this->assertEquals('Annotations\Tests\TestsCase\ExempleInformation', $informations->getClassname());
    $this->assertEquals(null, $informations->getParent());
    $this->assertTrue(is_array($informations->getInterfaces()));
    $this->assertTrue(is_array($informations->getMethods()));
    $this->assertTrue(is_array($informations->getAttributes()));
    $this->assertEquals(realpath(__DIR__.'/TestsCase/ExempleInformation.php'), $informations->getFilepath());

  }



  public function providerForInformationClassTest(){
    require_once __DIR__.'/TestsCase/ExempleInformation.php';
    $params1 = array('Annotations\Tests\TestsCase\ExempleInformation', false);

    return array(
      $params1,
    );
  }


}
