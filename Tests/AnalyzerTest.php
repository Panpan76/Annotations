<?php

namespace Annotations\Tests;

use PHPUnit\Framework\TestCase;

use Annotations\Analyzer;
use Annotations\Exceptions\AnalyzerException;
use Annotations\Exceptions\Exception;

use Annotations\Logguers\Logguer;

class AnalyzerTest extends TestCase{

  /**
   * @dataProvider providerForAnnotationsTypesDirectoryTest
   * @covers Annotations\Analyzer::setAnnotationsTypesDirectory
   * @covers Annotations\Analyzer::getAnnotationsTypesDirectory
   * @covers Annotations\Exceptions\AnalyzerException::__construct
   * @covers Annotations\Exceptions\Exception::__construct
   * @covers Annotations\Exceptions\Exception::setLogguer
   */
  public function testAnnotationsTypesDirectory(string $directory, $expected){
    $analyzer = new Analyzer();
    Exception::setLogguer(new Logguer());
    try{
      $analyzer->setAnnotationsTypesDirectory($directory);
      $this->assertEquals($expected, $analyzer->getAnnotationsTypesDirectory());
    }catch(AnalyzerException $e){
      $this->assertEquals($expected, $e->getCode());
    }
  }

  /**
   * @dataProvider providerForAnalyzeTest
   * @covers Annotations\Analyzer::analyze
   * @covers Annotations\Analyzer::getAnnotationsTypesClass
   * @covers Annotations\Analyzer::getFiles
   * @covers Annotations\Analyzer::isPhpFile
   */
  public function testAnalyze(string $annotation, $expected){
    $analyzer = new Analyzer();
    $analyzer->setAnnotationsTypesDirectory(__DIR__.'/TestsCase');
    $results = $analyzer->analyze($annotation);
    $this->assertEquals($expected, $results);
  }

  /**
   * @dataProvider providerForAnalyzeTest
   * @covers Annotations\Analyzer::analyze
   * @covers Annotations\Analyzer::getAnnotationsTypesClass
   * @covers Annotations\Analyzer::getFiles
   * @covers Annotations\Analyzer::isPhpFile
   */
  public function testAnalyzeFail(string $annotation, $expected){
    try{
      $analyzer = new Analyzer();
      $analyzer->setAnnotationsTypesDirectory('invalidDirectory');
      $results = $analyzer->analyze($annotation);
      $this->assertEquals($expected, $results);
    }catch(AnalyzerException $e){
      $this->assertEquals(AnalyzerException::NOT_DIRECTORY, $e->getCode());
    }
  }


  public function providerForAnnotationsTypesDirectoryTest(){
    $params1 = array('invalidDirectory', AnalyzerException::NOT_DIRECTORY);
    $params2 = array('Subfolder/phpFile.php', AnalyzerException::NOT_DIRECTORY);
    $params3 = array(__DIR__.'/TestsCase', realpath(__DIR__.'/TestsCase'));

    return array(
      $params1,
      $params2,
      $params3,
    );
  }


  public function providerForAnalyzeTest(){
    $params1 = array('* @param string $nom test', array(
      'parameters' => array(
        'nom' => array(
          'type' => 'string',
          'description' => 'test'
        )
      )
    ));

    return array(
      $params1,
    );
  }

}
