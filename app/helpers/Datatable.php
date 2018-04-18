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
    
    static public function prepareQueryParams($datatablesRequest,$realColumns =[], $columnsToPrefix =[] , $tablename ='', $columnsWithOperator = []) {
      $columns = Arrays::get($datatablesRequest, 'tableColumns',[]);
      $params = [];
      $params['order'] = [];
      $params['where'] = [];
      $search = Arrays::get($datatablesRequest,['search', 'value'] , '');
      if ($search) {
        $query = [];
        $values = [];
        foreach ($realColumns as $realColumn) {
          $operator = array_key_exists($realColumn, $columnsWithOperator) ? $columnsWithOperator[$realColumn] : 'LIKE';
          $query[] = self::prefixTablename($realColumn, $columnsToPrefix, $tablename) . ' ' . $operator;
          $values[] = ($operator == 'LIKE' ? '%' : '') . $search . ($operator == 'LIKE' ? '%' : '');
        }
        
        $params['where'] = [
          'query' => implode(' ? OR ', $query) . ' ?',
          'values' => $values
        ];
      }
      foreach ($datatablesRequest['order'] as $order) {
        $column = $datatablesRequest['columns'][(int)$order['column']]['data'];
        if(in_array($column, $columns)) {
          $params['order'][] = self::prefixTablename(
            self::getRealColumn($column,$realColumns) . ' ' . strtoupper($order['dir']),
            $columnsToPrefix, $tablename
          );
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
  
    static function prefixTablename($column, $columnsToPrefix, $tablename) {
      if(in_array($column,$columnsToPrefix)) {
        return $tablename . '.' . $column;
      } else {
        return $column;
      }
    }
    
  }