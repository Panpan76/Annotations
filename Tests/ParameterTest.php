<?php

namespace Annotations\Tests;

require_once __DIR__.'\..\autoload.php';

use PHPUnit\Framework\TestCase;

use Annotations\Types\Parameter;

class ParameterTest extends TestCase{

  /**
   * @dataProvider providerForCheckingAnnotationTest
   * @covers Annotations\Types\Parameter::checkAnnotation
   */
  public function testCheckAnnotation(string $annotation, array $expected){
    $parameter = new Parameter();
    $this->assertEquals($expected, $parameter->checkAnnotation($annotation));
  }


  public function providerForCheckingAnnotationTest(){
    $params1 = array('nothing match here',                  array());
    $params2 = array('param',                               array());
    $params3 = array('param string $str description test',  array(
      '$str' => array(
        'type'        => 'string',
        'description' => 'description test'
      )
    ));

    return array(
      $params1,
      $params2,
      $params3,
    );
  }



}
