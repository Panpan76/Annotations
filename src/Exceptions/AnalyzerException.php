<?php

namespace Annotations\Exceptions;

class AnalyzerException extends Exception{
  const LOG_FILE = 'analyzer';

  const NOT_DIRECTORY = 0;
  const NOT_FILE      = 1;

  public function __construct($description, $code){
    $this->description  = $description;
    $this->code         = $code;

    switch($this->code){
      case self::NOT_DIRECTORY:
        $this->title  = "The specify entry is not a directory";
        $this->type   = "E";
        break;
      case self::NOT_FILE:
        $this->title  = "The specify entry is not a file";
        $this->type   = "E";
        break;
    }

    parent::__construct($this->title, $this->description, $this->type, $this->code);
  }
}
