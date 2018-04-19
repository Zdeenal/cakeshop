<?php
  
  namespace App\Traits;
  use App\Helpers\Datatable;
  use Nette\Application\Responses\JsonResponse;
  use Nette\Database\Table\ActiveRow;
  use Nette\Database\Table\Selection;
  use Nette\Utils\Arrays;
  use Tracy\Dumper;


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
    protected $columnsToPrefix = [];
    protected $columnsWithOperators = [];
    
    protected $actions = [];
    
  
    protected function beforeRender() {
      parent::beforeRender();
      $this->template->datatableTemplate = $this->webDir->getAppPath() . 'modules/common/components/datatable/datatable.latte';
    }
  
    /**
     * @return mixed
     */
    public function actiongetData() {
      $table = $this->getTable();
      $response    = [];
      $queryParams = Datatable::prepareQueryParams(
        $this->getParameters(),
        $this->getDataColumns(),
        $this->columnsToPrefix ,
        $this->getTable(),
        $this->columnsWithOperators
      );
      $items      = $this->database->table($table);
      $totalCount = $items->count();
      $primaryKey = $items->getPrimary();
      if ($queryParams['order']) {$items->order($queryParams['order']);}
      if ($queryParams['limit']) {$items->limit($queryParams['limit']);}
      if ($queryParams['where']) {$items->where($queryParams['where']['query'], ...$queryParams['where']['values']);}
        
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
          },explode('.', Datatable::getRealColumn($column, $this->getDataColumns())));
          $values[$column] = $value;
        }
        $values['DT_RowId'] = $item->$primaryKey;
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
      foreach ($columns as $column) {
        
        if (is_array($column)) {
          if (Arrays::get($column,'prefixTableName', FALSE)) {
            $this->columnsToPrefix[] = Arrays::get($column, 'column', FALSE) ? $column['column'] : $column;
          }
          
          if (Arrays::get($column,'operator', FALSE)) {
            $this->columnsWithOperators[$column['column']] = $column['operator'];
          }
        }
      }
    }
    
    
  
    /**
     * @return array
     */
    public function getColumns() {
      return $this->columns;
    }
  
    
  
    public function getDataColumns($flatten = FALSE, $includeActions = FALSE) {
      $columns = array_map(function($item){
        if(is_array($item) && Arrays::get($item, 'column')) {
          return $item['column'];
        } else {
          return $item;
        }}, $this->columns);
      
      if($includeActions && $this->actions) {
        $columns[] = '';
      }
      
      if ($flatten) {
        return array_map(function($item){
          $exploded = explode('.' , $item);
            return array_shift($exploded);
        }, $columns);
      }
      return $columns ? array_values($columns) : [];
    }
  
    public function getHeaders() {
      $columns = $this->columns ? array_keys($this->columns) : [];
      if ($this->actions) {
        $columns[] = 'Akce';
      }
      return $columns;
    }
  
  
    /**
     * @param array $actions
     */
    public function setDTActions($actions) {
      $this->actions = $actions;
    }
  
    /**
     * @return array
     */
    public function getActions() {
      return $this->actions;
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