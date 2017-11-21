<?php

namespace Annotations;

use Annotations\Exceptions\ParserException;

class Parser{
  protected const REGEX_BEGIN_COMMENTARY  = '/\/\*\*/';
  protected const REGEX_END_COMMENTARY    = '/\*\//';
  protected const REGEX_ANNOTATION        = '/\*\s@(.*)[\n\r]*/';
  protected const REGEX_ATTRIBUTE         = '/(public|protected|private)\s(static)?\s?\$(\w*)[^\w]*(.*);/';
  protected const REGEX_METHOD            = '/(public|protected|private)\s(static)?\s?function\s(\w+).*{/';

  protected const REGEX_NAMESPACE         = '/^namespace\s(.+);/'; // Pour match le namespace (un par fichier)
  protected const REGEX_CLASS             = '/class\s(\w+)\s?.*{/'; // Pour match la classe (une par fichier)

  protected const DEFAULT_TYPES_DIRECTORY = __DIR__.'/Types';
  private $annotationsTypesDirectory;

  /**
   * Parse the directory to fetch the annotations
   *
   * @param string $directory Directory to parse
   */
  public function parseDirectory(string $directory):array{
    $annotations = array();
    foreach($this->getFiles($directory) as $file){
      $annotations = array_merge($this->parseFile($file), $annotations);
    }
    return $annotations;
  }


  public function parseFile(string $file){
    $allAnnotations = array();
    $lines = $this->getLines($file); // The Generator
    foreach($lines as $line){
      if(preg_match(self::REGEX_NAMESPACE, $line, $matches)){
        $namespace = $matches[1];
      }
      if(preg_match(self::REGEX_CLASS, $line, $matches)){
        $class = $matches[1];
      }
      if(preg_match(self::REGEX_METHOD, $line, $matches)){
        $methodName = $matches[3];
        $allAnnotations["$namespace\\$class"][$methodName] = $annotations;
      }
      $annotations = array();
      if(preg_match(self::REGEX_BEGIN_COMMENTARY, $line)){
        $annotations = $this->parseCommentary($lines);
      }
    }
    return $allAnnotations;
  }

  /**
   * Parse a commentary block
   *
   * @param  Generator $lines The Generator for the current file
   * @return array            Annotations in the commentary block
   */
  public function parseCommentary(\Generator $lines):array{
    $annotations = array(); // Init
    // Get the current line
    // While the line is not the end of the commentary block
    // Go to the next line and get it
    for($line = $lines->current(); !preg_match(self::REGEX_END_COMMENTARY, $line); $lines->next(), $line = $lines->current()){
      if(preg_match(self::REGEX_ANNOTATION, $line)){ // If it is an annotation, get it
        // If the key already exist, merge it
        foreach($this->analizeAnnotation($line) as $key => $infos){
          if(is_string($key)){
            if(!array_key_exists($key, $annotations)){
              $annotations[$key] = array();
            }
            $annotations[$key] = array_merge($annotations[$key], $infos);
          }else{
            $annotations[] = $infos;
          }
        }
      }
    }
    return $annotations;
  }

  public function analizeAnnotation(string $annotation):array{
    $results = array();
    foreach($this->getAnnotationsTypesClassInDirectory(self::DEFAULT_TYPES_DIRECTORY) as $class){
      $object = new $class();
      defined("$class::KEY") ? $results[$class::KEY] = $object->checkAnnotation($annotation) : $results[] = $object->checkAnnotation($annotation);
    }
    if(!is_null($this->annotationsTypesDirectory)){
      foreach($this->getAnnotationsTypesClassInDirectory($this->annotationsTypesDirectory) as $class){
        $object = new $class();
        defined("$class::KEY") ? $results[$class::KEY] = $object->checkAnnotation($annotation) : $results[] = $object->checkAnnotation($annotation);
      }
    }
    return $results;
  }

  /**
   * Configuration
   * Define where you have your own annotations type
   *
   * @param string $directory Directory that contains your own annotations types
   */
  public function setAnnotationsTypesDirectory(string $directory):void{
    if(!is_dir($directory)){
      throw new ParserException("'$directory' is not a directory", ParserException::NOT_DIRECTORY);
    }
    $this->annotationsTypesDirectory = $directory;
  }

  public function getAnnotationsTypesDirectory():?string{
    return $this->annotationsTypesDirectory;
  }

  /**
   * Get the files we want from the directory
   *
   * @param  string     $directory Directory to search files from
   * @return Generator             Get each files
   */
  public function getFiles(string $directory):\Generator{
    if(!is_dir($directory)){
      throw new ParserException("'$directory' is not a directory", ParserException::NOT_DIRECTORY);
      return;
    }
    foreach(array_diff(scandir($directory), array('.', '..')) as $element){
      $element = "$directory/$element";
      if(is_dir($element)){
        foreach($this->getFiles($element) as $file){
          yield $file;
        }
      }
      if($this->isParsable($element)){
        $element = realpath($element);
        yield $element;
      }
    }
  }

  /**
   * If the file can contains annotations
   *
   * @param  string $file File to test
   * @return bool         True if the file can contains annotations, otherway false
   */
  public function isParsable(string $file):bool{
    if(!is_file($file)){
      return false;
    }
    $infos = pathinfo($file);
    $bool = false;
    if(isset($infos['extension'])){
      $bool = $infos['extension'] === 'php';
    }
    return $bool;
  }

  /**
   * Generator that parse each lines from the input file
   *
   * @param  string     $file File to get lines from
   * @return Generator        Lines generator for the file
   */
  public function getLines(string $file):\Generator{
    if(!is_file($file)){
      throw new ParserException("'$file' is not a file", ParserException::NOT_FILE);
    }
    $lines = file($file);
    foreach($lines as $line){
      yield trim($line);
    }
  }

  public function getAnnotationsTypesClassInDirectory(string $directory):\Generator{
    foreach($this->getFiles($directory) as $file){
      require_once $file;
      $class = 'Annotations\Types\\'.basename(explode('.', $file)[0]); // Create the classname from the filename
      method_exists($class, 'checkAnnotation') ? yield $class : null;
    }
  }
}
