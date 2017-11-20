<?php

namespace Annotations\Exceptions;

use \Annotations\Interfaces\Logguable;

class Exception extends \Exception{
  protected $title;
  protected $description;
  protected $type;
  protected $code;

  private static $logguer = null;


  /**
   * Constructor of the class
   *
   * @param string $title       Title of the exception
   * @param string $description Description of the exception
   * @param string $type        Type of the exception
   * @param int    $code        Code of the exception
   */
  public function __construct($title = '', $description = '', $type = 'UNDEFINED', $code = 0){
    $this->title        = $title;
    $this->description  = $description;
    $this->type         = $type;
    $this->code         = $code;

    if(!is_null(self::$logguer)){
      // Get the class that throws the exception
      $exceptionClass = get_called_class();

      $file     = $this->getFile(); // Get the file that throws the exception
      $line     = $this->getLine(); // Get the line that throws the exception
      $message  = "$exceptionClass [$this->title] : $this->description ($file, ligne $line)"; // On prépare le message à logguer
      $logFile  = null;
      if(defined("$exceptionClass::LOG_FILE")){
        $logFile = $exceptionClass::LOG_FILE;
      }
      self::$logguer->setLogFile($logFile);
      self::$logguer->log($message, $this->type);
    }
  }

  /**
   * Define the logguer use for the exceptions
   *
   * @param Logguable $logguer Logguer to use
   */
  public static function setLogguer(Logguable $logguer):void{
    self::$logguer = $logguer;
  }

  /**
   * Get the description of the exception
   *
   * @codeCoverageIgnore
   * @return string Description of the exception
   */
  public function getDescription():string{
    return $this->description;
  }

  /**
   * To display the exception
   *
   * @codeCoverageIgnore
   * @return string
   */
  public function display():string{
    $str = "<div class='alert alert-danger' role='alert'>
              Exception catched : <strong>{$this->title}</strong><br />
              {$this->description}
            </div>";
    $str .= "<table class='table'>
              <tr>
                <th colspan='2'>Trace :</th>
              </tr>";

    foreach($this->getTrace() as $trace){
      $params = array();
      foreach($trace['args'] as $param){
        if(is_array($param)){
          $params[] = 'array(...)';
        }elseif(is_object($param)){
          $params[] = 'Object<'.get_class($param).'>';
        }else{
          $params[] = "'$param'";
        }
      }
      $params = implode(', ', $params);
      $file = '';
      if(isset($trace['file'])){
        $file = "{$trace['file']} : {$trace['line']}";
      }
      $class = '';
      if(isset($trace['class'])){
        $class = "{$trace['class']}{$trace['type']}";
      }
      $str .= "<tr>
                <td>{$file}</td>
                <td>{$class}{$trace['function']}({$params})</td>
              </tr>";
    }
    $str .= "</table>";
    return $str;
  }

}
