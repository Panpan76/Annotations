<?php

namespace Annotations\Types;

class fileWithAnnotations{
  /**
   * Commentary block
   *
   * @param string $str String test
   */
  public function doNothing(string $str):void{}

  public static function checkAnnotation(){}
}
