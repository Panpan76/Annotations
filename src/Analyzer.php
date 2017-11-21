<?php

namespace Annotations;

class Analyzer{

  private const DEFAULT_TYPES_DIRECTORY = __DIR__.'/Types';
  private $annotationsTypesDirectory;

  public function analyze(string $annotation):array{
    $results = array();
    foreach($this->getAnnotationsTypesClass() as $class){
      $tmpResults = $class::checkAnnotation($annotation);
      if(defined("$class::KEY")){
        $results[$class::KEY] = $tmpResults;
      }else{
        $results[] = $tmpResults;
      }
    }
    return $results;
  }

  public function setAnnotationsTypesDirectory(string $directory):void{
    $this->annotationsTypesDirectory = $directory;
  }
  public function getAnnotationsTypesDirectory():?string{
    return $this->annotationsTypesDirectory;
  }

  public function getAnnotationsTypesClass():\Generator{
    foreach($this->getFiles(self::DEFAULT_TYPES_DIRECTORY) as $file){
      $class = 'Annotations\Types\\'.basename(explode('.', $file)[0]); // Create the classname from the filename
      method_exists($class, 'checkAnnotation') ? yield $class : null;
    }
    if(!is_null($this->annotationsTypesDirectory)){
      foreach($this->getFiles($this->annotationsTypesDirectory) as $file){
        $class = 'Annotations\Types\\'.basename(explode('.', $file)[0]); // Create the classname from the filename
        method_exists($class, 'checkAnnotation') ? yield $class : null;
      }
    }
  }

  /**
   * Get the files we want from the directory
   *
   * @param  string     $directory Directory to search files from
   * @return Generator             Get each files
   */
  private function getFiles(string $directory):\Generator{
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
      if($this->isPhpFile($element)){
        yield realpath($element);
      }
    }
  }

  /**
   * Check if the file is a PHP file
   *
   * @param  string $file File to test
   * @return bool
   */
  private function isPhpFile(string $file):bool{
    if(!is_file($file)){
      return false;
    }
    $infos = pathinfo($file);
    if(isset($infos['extension'])){
      return $infos['extension'] === 'php';
    }
    return false;
  }


}
