<?php

namespace Annotations\Types;

use Annotations\Interfaces\CheckAnnotation;

class Parameter implements CheckAnnotation{
  const KEY = 'parameters';

  const REGEX_PARAM = '/param\s+([^\s]*)\s+(\$[^\s]*)\s+(.*)/';

  public function checkAnnotation(string $annotation):array{
    if(preg_match(self::REGEX_PARAM, $annotation, $matches)){
      return array(
        $matches[2] => array(
          'type'        => $matches[1],
          'description' => $matches[3]
        )
      );
    }
    return array();
  }
}
