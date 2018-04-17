<?php
  
  namespace App\Traits;
  use App\Helpers\Datatable;
  use Nette\Application\Responses\JsonResponse;
  

  /**
   * Trait DatatableTrait reprezentující ...
   *
   * @author    Zdeněk Houdek
   * @copyright © 2018, Proclient s.r.o.
   * @created   17.04.2018
   */
  trait DatatableTrait
  {
  
    public function actiongetData() {
      $table = $this->getTableName();
      $response    = [];
      $queryParams = Datatable::prepareQueryParams($this->getParameters());
  
      $totalCount = $this->database->table($table)->count();
      $groups      = $this->database->table($table)->select(
        'user_group_id, name'
      );
      if ($queryParams['order']) {$groups->order($queryParams['order']);}
      if ($queryParams['limit']) {$groups->limit($queryParams['limit']);}
        
  
      foreach ($groups as $group) {
        $response [] = [
          'user_group_id'     => $group->user_group_id,
          'name'   => $group->name,
        ];
      }
  
      return
      $this->sendResponse(new JsonResponse(
        [
          'iTotalRecords'=> $totalCount,
          'iTotalDisplayRecords'=> $totalCount,
          'data' => $response
        ]
      ));
    }
    
    protected function getTableName(){
      if (defined('self::_TABLE')) {
        return self::_TABLE;
      } else {
        $path          = explode(':', $this->getName());
        $presenter     = array_pop($path);
        $tableSingular = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1_', $presenter));
        $table         = substr($tableSingular, -1) == 's' ? $tableSingular : $tableSingular . 's';
  
        return $table;
      }
    }
  }