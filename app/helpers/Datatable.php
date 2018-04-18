<?php
  namespace App\Helpers;
  use Nette\Utils\Arrays;

  /**
   * Class Datatable  ...
   *
   * @author  Zdeněk Houdek
   * @created 16.04.2018
   */
  class Datatable
  {
    
    static public function prepareQueryParams($datatablesRequest,$realColumns =[]) {
      $columns = Arrays::get($datatablesRequest, 'tableColumns',[]);
      $params = [];
      $params['order'] = [];
      foreach ($datatablesRequest['order'] as $order) {
        $column = $datatablesRequest['columns'][(int)$order['column']]['data'];
        if(in_array($column, $columns)) {
          $params['order'][] = self::getRealColumn($column,$realColumns) . ' ' . strtoupper($order['dir']);
        }
      }
      $params['order'] = implode( ', ',$params['order']);
      $params['limit'] = $datatablesRequest['length'] . ', ' . $datatablesRequest['start'];
      return $params;
    }
  
      static public function getRealColumn($column, $realColumns) {
      $result = $column;
      foreach ($realColumns as $realColumn) {
        $exploded = explode('.', $realColumn);
        if (array_shift($exploded) == $column) {
          $result = $realColumn;
          continue;
        }
      }
      return $result;
    }
    
  }