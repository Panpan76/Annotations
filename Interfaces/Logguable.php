<?php

namespace Annotations\Interfaces;

interface Logguable{
  public function setLogFile(string $file):void;
  public function log(string $message, string $type = 'I'):void;
}
