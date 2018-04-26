<?php
  
  namespace App\Traits;
  use App\Helpers\Datatable;
  use Nette\Application\Responses\JsonResponse;
  use Nette\Database\Table\ActiveRow;
  use Nette\Utils\Arrays;


  /**
   * Trait DatatableTrait reprezentující ...
   *
   * @author    Zdeněk Houdek
   * @copyright © 2018, Proclient s.r.o.
   * @created   17.04.2018
   */
  trait DatatableTrait
  {
    protected $id = '';
    
    protected $columns = [];
    protected $columnsToPrefix = [];
    protected $columnsWithOperators = [];
    protected $buttons = [];
    protected $actions = [];
    
  
    protected function beforeRender() {
      parent::beforeRender();
    }
  
    /**
     * @return mixed
     */
    public function actiongetData() {
      $response    = [];
      $queryParams = Datatable::prepareQueryParams(
        $this->getParameters(),
        $this->getDataColumns(),
        $this->columnsToPrefix ,
        $this->model->getTableName(),
        $this->columnsWithOperators
      );
      $items      = $this->model->getAllItems();
      $totalCount = $items->count();
      $primaryKey = $items->getPrimary();
      if ($queryParams['where']) {$items->where($queryParams['where']['query'], ...$queryParams['where']['values']);}
      $count = $items->count();
      if ($queryParams['order']) {$items->order($queryParams['order']);}
      if ($queryParams['limit']) {$items->limit($queryParams['limit'],$queryParams['offset']);}
      
      
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
      }
      return
      $this->sendResponse(new JsonResponse(
        [
          'iTotalRecords'=> $totalCount,
          'iTotalDisplayRecords'=> $count,
          'draw' => $this->getParameter('draw'),
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
      $html = '';
      foreach ($this->actions as $action) {
        $html .= '<a
         href="' . $action['action'] . '"
         class="ajax">
         ' . $action['button'] . '</a>';
      }
      
      return $html;
    }
  
    public function setDTButtons($buttons) {
      $this->buttons = $buttons;
    }
  
  
    public function getButtons() {
      return $this->buttons;
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
    
    
  
    public function getLanguage($country = 'czech') {
      
      return '../../admin_theme/vendor/datatables/languages/' . $country . '.json';
    
    }
  }