<?php
  
  namespace App\Traits;
  use Nette\Database\Context;
  use Nette\Database\Table\ActiveRow;
  use Nette\Utils\Arrays;
  use Tracy\Dumper;

  /**
   * Class DatatableModelTrait  ...
   *
   * @author  Zdeněk Houdek
   * @created 26.04.2018
   */
  trait DatatableModelTrait
  {
    protected $tableName = '';
    
  
    /** @var Context */
    protected $db;
  
  
    public function __construct(Context $db) {
      $this->db = $db;
    }
    
    public function getTableName(){
      if ($this->tableName) {
        return $this->tableName;
      } else {
        $path          = explode(':', $this->getName());
        $presenter     = array_pop($path);
        $tableSingular = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1_', $presenter));
        $table         = substr($tableSingular, -1) == 's' ? $tableSingular : $tableSingular . 's';
      
        return $table;
      }
    }
    
    protected function getName(){
    $exploded = explode('\\', __CLASS__);
     return str_replace('Model', '', array_pop($exploded));
    }
  
    public function getAllItems() {
      return $this->db->table($this->getTableName());
    }
  
    public function getItemById($id) {
      return $this->db->table($this->getTableName())->get($id);
    }
  
    public function getPairsForSelect($valueGetter, $textGetter, $excludeId = NULL, $defaultValue = [NULL => ''] ) {
      $items = $this->getAllItems()->select($valueGetter . ',' . $textGetter);
      if ($excludeId) {
        $items->where($items->getPrimary() .' != ?', $excludeId);
      }
      return ($defaultValue ? $defaultValue : []) + $items->fetchPairs($valueGetter, $textGetter);
    }
  
    public function store($values) {
      $primaryKey = $this->getPrimaryKey();
      if ($id = Arrays::get($values, $primaryKey )) {
        $this->getAllItems()->where($primaryKey .' = ?', $id)->update($values);
      } else {
        $this->getAllItems()->insert($values);
      }
    }
    
    public function delete(ActiveRow $item, $byMark = FALSE) {
      if ($byMark) {
        $item->update(['deleted' => 1]);
      } else {
        $table = $this->db->table($this->getTableName());
        $table->delete($item->$table->getPrimary());
      }
    }
    protected function getPrimaryKey() {
      return $this->db->table($this->getTableName())->getPrimary();
    }
  
    public function checkUniqueValue($values, $key) {
      $value = Arrays::get($values , $key, FALSE);
      if (!$value || !is_null(Arrays::get($values,$this->getPrimaryKey()))) {
        return true;
      } else {
        return !$this->db->table($this->getTableName())->where($key . ' = ?', $value)->count();
      }
    }
  }