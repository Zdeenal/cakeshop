<?php
  
  namespace App\Helpers;
  use Nette\Utils\Arrays;

  /**
   * Class String  ...
   *
   * @author  Zdeněk Houdek
   * @created 25.04.2018
   */
  class Strings
  {
   
    public static function placeholders($variables, $input) {
      if (is_array($input) && Arrays::get($input,'message')) {
        
        foreach ($variables as $key => $value) {
          $input['message'] = str_replace('{' . strtoupper($key) . '}', $value, $input['message']);
        }
      } else {
        foreach ($variables as $key => $value) {
          $input = str_replace('{' . strtoupper($key) . '}', $value, $input);
        }
      }
      return $input;
    }
  }