<?php

namespace Annotations\Tests\TestsCase;

require_once 'ExempleInterface.php';

class ExempleInformation implements ExempleInterface{
  /**
   *
   * @BDD('user_ID', type="int")
   */
  private $autre;
  private $id;

  /**
   * [test description]
   * @param  string           $exemple [description]
   * @param  AnnoationsParser $parser  [description]
   * @return [type]                    [description]
   */
  public function doNothing(string $test = ''):void{}
}
