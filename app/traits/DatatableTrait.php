<?php
  
  namespace App\Traits;
  use App\Helpers\Datatable;
  use Nette\Application\Responses\JsonResponse;
  use Nette\Database\Table\ActiveRow;
  use Tester\Dumper;


  /**
   * Trait DatatableTrait reprezentující ...
   *
   * @author    Zdeněk Houdek
   * @copyright © 2018, Proclient s.r.o.
   * @created   17.04.2018
   */
  trait DatatableTrait
  {
    protected $table = '';
    protected $id = '';
    protected $columns = [];
    
  
    protected function beforeRender() {
      parent::beforeRender();
      $this->template->datatableTemplate = $this->webDir->getAppPath() . 'modules/common/components/datatable/datatable.latte';
    }
    
    public function actiongetData() {
      $table = $this->getTable();
      $response    = [];
      $queryParams = Datatable::prepareQueryParams($this->getParameters(),$this->columns);
      $totalCount = $this->database->table($table)->count();
      $items      = $this->database->table($table);
      if ($queryParams['order']) {$items->order($queryParams['order']);}
      if ($queryParams['limit']) {$items->limit($queryParams['limit']);}
        
      $count = 0;
      foreach ($items as $item) {
        $values = [];
        foreach ($this->getParameters()['tableColumns'] as $column) {
          $value = '';
          $i = 1;
          array_map(function($part) use (&$i, &$value, $item, $column){
            if ($i > 1) {
              if ($value instanceof ActiveRow) {
                $value = $value->$part;
              }
            } else {
              $value = $item->$part;
            }
            ++$i;
          },explode('.', Datatable::getRealColumn($column, $this->columns)));
          $values[$column] = $value;
        }
        $response [] = $values;
        ++$count;
      }
      return
      $this->sendResponse(new JsonResponse(
        [
          'iTotalRecords'=> $totalCount,
          'iTotalDisplayRecords'=> $count,
          'data' => $response
        ]
      ));
    }
  
    protected function createComponentDatatable() {
      return new \App\Common\Components\Layout\Datatable();
    }
  
    /**
     * @param array $columns
     */
    public function setDTColumns($columns) {
      $this->columns = $columns;
    }
  
    /**
     * @return array
     */
    public function getColumns() {
      return $this->columns;
    }
  
    
  
    public function getDataColumns($flatten = FALSE) {
      if ($flatten) {
        return array_map(function($item){
          $exploded = explode('.' , $item);
          return array_shift($exploded);
        }, $this->columns);
      }
      return $this->columns ? array_values($this->columns) : [];
    }
  
    public function getHeaders() {
      return $this->columns ? array_keys($this->columns) : [];
    }
    
    /**
     * @param string $id
     */
    public function setDTId($id) {
      $this->id = $id;
    }
    /**
     * @return string
     */
    public function getId($withSelector = FALSE) {
      $id = $this->id ? $this->id : 'datatable' . time();
      if ($withSelector) {
        $id = substr($id,0,1) !== '#' ? '#' . $id : $id;
      }
      return $id;
    }
    
    public function setDTTable($table) {
      $this->table = $table;
    }
    protected function getTable(){
      if ($this->table) {
        return $this->table;
      } else {
        $path          = explode(':', $this->getName());
        $presenter     = array_pop($path);
        $tableSingular = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1_', $presenter));
        $table         = substr($tableSingular, -1) == 's' ? $tableSingular : $tableSingular . 's';
  
        return $table;
      }
    }
  }