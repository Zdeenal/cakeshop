<?php
  
  namespace App\Helpers;
  use Nette\Utils\Arrays;
  use Tracy\Dumper;

  /**
   * Class String  ...
   *
   * @author  Zdeněk Houdek
   * @created 25.04.2018
   */
  class Strings
  {
   
    public static function placeholders($variables, $input) {
      if (is_array($input)) {
        foreach ($variables as $key => $value) {
          foreach ($input as $inKey => $inValue) {
            $input[$inKey] = str_replace('{' . strtoupper($key) . '}', $value, $input[$inKey]);
          }
        }
      } else {
        foreach ($variables as $key => $value) {
          $input = str_replace('{' . strtoupper($key) . '}', $value, $input);
        }
      }
      return $input;
    }
  }