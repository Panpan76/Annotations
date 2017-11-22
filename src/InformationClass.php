<?php

namespace Annotations;

class InformationClass{
  private $filepath;

  private $namespace;
  private $classname;
  private $parent;
  private $interfaces;
  private $methods;
  private $attributes;

  private $analyzer;

  /**
   *
   * @param mixed $class Classname or object to ba analyzed
   */
  public function __construct($class, ?Analyzer $analyzer = null){
    $this->analyzer = $analyzer;

    $reflectionClass = new \ReflectionClass($class);
    $this->filepath = $reflectionClass->getFileName();
    $this->namespace = $reflectionClass->getNamespaceName();
    $this->classname = $reflectionClass->getName();
    $this->parent = $reflectionClass->getParentClass();
    $this->setInterfaces($reflectionClass);
    $this->setMethods($reflectionClass);
    $this->setAttributes($reflectionClass);
  }

  public function getNamespace(){
    return $this->namespace;
  }

  public function getClassname(){
    return $this->classname;
  }

  public function getParent(){
    return $this->parent;
  }

  public function getInterfaces(){
    return $this->interfaces;
  }

  public function getMethods(){
    return $this->methods;
  }

  public function getAttributes(){
    return $this->attributes;
  }

  public function getFilepath(){
    return $this->filepath;
  }

  private function setInterfaces(\ReflectionClass $reflectionClass){
    foreach($reflectionClass->getInterfaces() as $interface){
      $this->interfaces[$interface->getName()] = new self($interface->getName(), $this->analyzer);
    }
  }

  private function setMethods(\ReflectionClass $reflectionClass){
    foreach($reflectionClass->getMethods() as $method){
      $methodName                 = $method->getName();
      $this->methods[$methodName] = array();

      foreach($method->getParameters() as $parameter){
        $infosParameter = array(
          'name' => $parameter->getName(),
          'type' => $parameter->getType()->__toString(),
        );
        if($parameter->isOptional()){
          $infosParameter['default'] = $parameter->getDefaultValue();
        }
        $this->methods[$methodName]['args'][$parameter->getPosition()] = $infosParameter;
      }

      if(!is_null($method->getReturnType())){
        $this->methods[$methodName]['returnType'] = $method->getReturnType()->__toString();
      }

      $this->methods[$methodName] = array_merge($this->methods[$methodName], $this->parseCommentary($method->getDocComment()));
    }
  }

  private function setAttributes(\ReflectionClass $reflectionClass){
    foreach($reflectionClass->getProperties() as $property){
      $this->attributes[$property->getName()] = $this->parseCommentary($property->getDocComment());
    }
  }

  private function parseCommentary(string $commentary):array{
    $annotations = array();
    if(!is_null($this->analyzer)){
      foreach(explode("\n", $commentary) as $line){
        if(preg_match('/\*\s?@/', $line)){
          foreach($this->analyzer->analyze($line) as $key => $infos){
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
    }
    return $annotations;
  }
}
