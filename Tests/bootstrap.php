<?php


function autoloadClass($class){
  $classFile = str_replace('\\', '/', $class).'.php';
  $classFile = str_replace('Annotations/', '', $classFile);
  if(file_exists($classFile)){
    require_once $classFile;
  }elseif(file_exists('src/'.$classFile)){
    require_once 'src/'.$classFile;
  }
}

spl_autoload_register('autoloadClass');
