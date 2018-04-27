<?php
  
  namespace App\Traits;
  use App\Helpers\Datatable;
  use App\Helpers\Strings;
  use Nette\Application\Responses\JsonResponse;
  use Nette\Database\Table\ActiveRow;
  use Nette\Utils\Arrays;


  /**
   * Trait DatatableTrait
   *
   * Extending presenter with datatable functionality
   *
   * @author    Zdeněk Houdek
   * @copyright © 2018, Proclient s.r.o.
   * @created   17.04.2018
   */
  trait DatatableTrait
  {
    /** @var string datatable id selector */
    protected $id = '';
  
    /** @var array columns with definitions */
    protected $columns = [];
    
    /** @var array columns that should be prefixed with table name */
    protected $columnsToPrefix = [];
    
    /** @var array columns with other operator then LIKE */
    protected $columnsWithOperators = [];
    
    /** @var array buttons definition */
    protected $buttons = [];
    
    /** @var array actions definition */
    protected $actions = [];
  
  
    /**
     * Returns payload with data for datatable js
     * @return mixed
     */
    public function actiongetData() {
      $response    = [];
      
      // Prepare paremeters for data selection [where, order, limit, offset]
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
      
      // Get values from database selection of Active rows
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
      
      // Send data
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
  
    /**
     * Create datatable component
     * @return \App\Common\Components\Layout\Datatable
     */
    protected function createComponentDatatable() {
      return new \App\Common\Components\Layout\Datatable();
    }
  
    /**
     * Parse columns definitions
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
     * Columns getter
     * @return array
     */
    public function getColumns() {
      return $this->columns;
    }
  
    /**
     * Returns data property values
     * @param bool $flatten
     * @param bool $includeActions
     *
     * @return array
     */
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
  
    /**
     * Returns headers
     * @return array
     */
    public function getHeaders() {
      $columns = $this->columns ? array_keys($this->columns) : [];
      if ($this->actions) {
        $columns[] = 'Akce';
      }
      return $columns;
    }
    
    /**
     * Actions setter
     * @param array $actions
     */
    public function setDTActions($actions) {
      $this->actions = $actions;
    }
  
    /**
     * Build action buttons Html
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
  
    /**
     * Buttons Setter
     * @param $buttons
     */
    public function setDTButtons($buttons) {
      $this->buttons = $buttons;
    }
  
    /**
     * Buttons getter
     * @return array
     */
    public function getButtons() {
      return $this->buttons;
    }
    
    /**
     * Id selector setter
     * @param string $id
     */
    public function setDTId($id) {
      $this->id = $id;
    }
    
    /**
     * Returns datatable id selector
     * @return string
     */
    public function getId($withSelector = FALSE) {
      $id = $this->id ? $this->id : 'datatable' . time();
      if ($withSelector) {
        $id = substr($id,0,1) !== '#' ? '#' . $id : $id;
      }
      return $id;
    }
  
    /**
     * Returns translation filePath
     * @param string $country
     *
     * @return string
     */
    public function getLanguage($country = 'czech') {
      return '../../admin_theme/vendor/datatables/languages/' . $country . '.json';
    }
  
    /**
     * Perform prompted delete action with given settings
     * @param       $promptQuestion
     * @param       $successMessage
     * @param       $failMessage
     * @param array $promptData
     */
    protected function actionDelete($promptQuestion , $successMessage, $failMessage , $promptData = []) {
      $id = $this->getParameter('rowId');
      /** CONFIRMED */
      if (!$this->isAjax() || $this->getParameter('confirmed')) {
        $item = $this->model->getItemById($id);
        try {
          $this->model->delete($item);
        } catch (Exception $e) {
          $this->flashMessage(...Strings::placeholders($item->toArray(),$failMessage));
          $this->finishWithPayload(['success' => FALSE]);
        }
        $this->flashMessage(...Strings::placeholders($item->toArray(),$successMessage));
        $this->finishWithPayload(['success' => TRUE]);
    
        /** PROMPT*/
      } else {
        if ($this->isAjax()) {
          $item = $this->model->getItemById($id)->toArray();
          $prompt = [
            'title'   => Strings::placeholders($item,$promptQuestion),
          ] + $promptData;
          
          $this->finishWithPayload(
            [
              'id' => $id,
              'prompt' => $prompt
            ]
          );
        }
      }
    
    }
    
    
  }