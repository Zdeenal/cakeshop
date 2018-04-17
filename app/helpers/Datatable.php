<?php
  
  namespace App\Helpers;

  /**
   * Class Datatable  ...
   *
   * @author  Zdeněk Houdek
   * @created 16.04.2018
   */
  class Datatable
  {
    
    static public function prepareQueryParams($datatablesRequest) {
      $columns = $datatablesRequest['tableColumns'];
      $params = [];
      $params['order'] = [];
      foreach ($datatablesRequest['order'] as $order) {
        $column = $datatablesRequest['columns'][(int)$order['column']]['data'];
        if(in_array($column, $columns)) {
          $params['order'][] = $column . ' ' . strtoupper($order['dir']);
        }
      }
      $params['order'] = implode( ', ',$params['order']);
      $params['limit'] = $datatablesRequest['length'] . ', ' . $datatablesRequest['start'];
      return $params;
    }
    
  }