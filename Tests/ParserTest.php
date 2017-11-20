<?php

namespace Annotations\Tests;

require_once __DIR__.'/../autoload.php';

use PHPUnit\Framework\TestCase;

use Annotations\Parser;
use Annotations\Exceptions\Exception;
use Annotations\Exceptions\ParserException;
use Logguers\Logguer;

class ParserTest extends TestCase{



  /**
   * This test must return true if :
   * - The input data is a file AND exists
   * - The file is a php file
   * - The file can be read
   *
   * @dataProvider providerForParsableTest
   * @covers Annotations\Parser::isParsable
   */
  public function testIsParsable(string $file, bool $expected){
    $parser = new Parser();
    $this->assertEquals($expected, $parser->isParsable($file));
  }

  /**
   * @dataProvider providerForGettingFilesTest
   * @covers Annotations\Parser::getFiles
   */
  public function testGetFiles(string $directory, array $expectedValues){
    $parser = new Parser();
    $n = 0;
    foreach($parser->getFiles($directory) as $file){
      $this->assertEquals($expectedValues[$n], $file);
      $n++;
    }
  }

  /**
   * @covers Annotations\Parser::getFiles
   * @covers Annotations\Exceptions\ParserException::__construct
   * @covers Annotations\Exceptions\Exception::__construct
   * @covers Annotations\Exceptions\Exception::setLogguer
   * @covers Logguers\Logguer::__construct
   * @covers Logguers\Logguer::setLogFile
   * @covers Logguers\Logguer::log
   */
  public function testExceptionGetFiles(){
    Exception::setLogguer(new Logguer());
    $parser = new Parser();
    try{
      foreach($parser->getFiles('DirectoryThatDoesNotExists') as $file){
        echo $file;
      }
    }catch(ParserException $e){
      $this->assertEquals(ParserException::NOT_DIRECTORY, $e->getCode());
    }
  }

  /**
   * @dataProvider providerForGettingLinesTest
   * @covers Annotations\Parser::getLines
   */
  public function testGetLines(string $file, array $expectedValues){
    $parser = new Parser();
    $n = 0;
    foreach($parser->getLines($file) as $line){
      $this->assertEquals($expectedValues[$n], $line);
      $n++;
    }
  }

  /**
   * @covers Annotations\Parser::getLines
   * @covers Annotations\Exceptions\ParserException::__construct
   * @covers Annotations\Exceptions\Exception::__construct
   * @covers Annotations\Exceptions\Exception::setLogguer
   * @covers Logguers\Logguer::__construct
   * @covers Logguers\Logguer::setLogFile
   * @covers Logguers\Logguer::log
   */
  public function testExceptionGetLines(){
    Exception::setLogguer(new Logguer());
    $parser = new Parser();
    try{
      foreach($parser->getLines('FileThatDoesNotExists') as $line){
        echo $line;
      }
    }catch(ParserException $e){
      $this->assertEquals(ParserException::NOT_FILE, $e->getCode());
    }
  }


  /**
   * @covers Annotations\Parser::setAnnotationsTypesDirectory
   * @covers Annotations\Parser::getAnnotationsTypesDirectory
   */
  public function testSetAnnotationsTypesDirectory(){
    $directory = __DIR__.'/TestsCase/Subfolder';
    $parser = new Parser();
    $parser->setAnnotationsTypesDirectory($directory);
    $this->assertEquals($directory, $parser->getAnnotationsTypesDirectory());
  }

  /**
   * @covers Annotations\Parser::setAnnotationsTypesDirectory
   * @covers Annotations\Exceptions\ParserException::__construct
   * @covers Annotations\Exceptions\Exception::__construct
   * @covers Annotations\Exceptions\Exception::setLogguer
   * @covers Logguers\Logguer::__construct
   * @covers Logguers\Logguer::setLogFile
   * @covers Logguers\Logguer::log
   */
  public function testExceptionSetAnnotationsTypesDirectory(){
    Exception::setLogguer(new Logguer());
    $parser = new Parser();
    try{
      $parser->setAnnotationsTypesDirectory('DirectoryThatDoesNotExists');
    }catch(ParserException $e){
      $this->assertEquals(ParserException::NOT_DIRECTORY, $e->getCode());
    }
  }


  /**
   * @dataProvider providerForParsingFileTest
   * @covers Annotations\Parser::parseDirectory
   * @covers Annotations\Parser::parseFile
   * @covers Annotations\Parser::parseCommentary
   * @covers Annotations\Parser::analizeAnnotation
   */
  public function testParseDirectory(string $directory, array $expected){
    $parser = new Parser();
    $parser->setAnnotationsTypesDirectory($directory);
    $this->assertEquals($expected, $parser->parseDirectory($directory));
  }


  /**
   * @dataProvider providerForGettingAnnotationsTypesClassInDirectory
   * @covers Annotations\Parser::getAnnotationsTypesClassInDirectory
   */
  public function testGetAnnotationsTypesClassInDirectory(string $directory, array $expectedValues){
    $parser = new Parser();
    $n = 0;
    foreach($parser->getAnnotationsTypesClassInDirectory($directory) as $file){
      $this->assertEquals($expectedValues[$n], $file);
      $n++;
    }
  }


  public function providerForParsableTest(){
    $directory = __DIR__.'/TestsCase/';
    $params1 = array($directory,                          false);  // Directory that exists
    $params2 = array($directory.'phpFile.php',            true);   // File that exists
    $params3 = array($directory.'directory_not_existing', false);  // Directory that doesn't exists
    $params4 = array($directory.'file_not_existing.xml',  false);  // File that doesn't exists
    $params5 = array($directory.'file_without_extension', false);  // File that exists without extension
    $params6 = array($directory.'xmlFile.xml',            false);  // File that exists but isn't a PHP file


    return array(
      $params1,
      $params2,
      $params3,
      $params4,
      $params5,
      $params6,
    );
  }

  public function providerForGettingFilesTest(){
    $directory = __DIR__.'/TestsCase/';
    $params1 = array($directory, array($directory.'Subfolder/phpFile.php', $directory.'fileWithAnnotations.php', $directory.'phpFile.php'));

    return array(
      $params1,
    );
  }

  public function providerForGettingLinesTest(){
    $directory = __DIR__.'/TestsCase/';
    $params1 = array($directory.'phpFile.php', array('hello', 'World !'));

    return array(
      $params1,
    );
  }

  public function providerForParsingFileTest(){
    $directory = __DIR__.'/TestsCase/';
    $params1 = array(
      $directory,
      array(
        'Annotations\Types\fileWithAnnotations' => array(
          'doNothing' => array(
            'parameters' => array(
              '$str' => array(
                'type'        => 'string',
                'description' => 'String test'
              )
            ),
            null
          ),
          'checkAnnotation' => array()
        )
      )
    );

    return array(
      $params1,
    );
  }

  public function providerForGettingAnnotationsTypesClassInDirectory(){
    $directory = __DIR__.'/TestsCase/';
    $params1 = array($directory, array('Annotations\Types\fileWithAnnotations'));

    return array(
      $params1,
    );
  }

}
