<?php

function getProjectName(){
  return 'Annotations';
}

function autoloadClass($class){
  $classFile = str_replace('\\', '/', $class).'.php';
  $classFile = str_replace(getProjectName().'/', '', $classFile);
  if(file_exists($classFile)){
    require_once $classFile;
  }
}

spl_autoload_register('autoloadClass');
