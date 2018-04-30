<?php
  
  namespace App\Traits;
  use Nette\Database\Context;
  use Nette\Database\Table\ActiveRow;
  use Nette\Utils\Arrays;

  /**
   * Class DatatableModelTrait
   * Extending model giving them datatable related functions
   *
   * @author  Zdeněk Houdek
   * @created 26.04.2018
   */
  trait DatatableModelTrait
  {
    /** @var string database table name */
    protected $tableName = '';
    
    /** @var Context database connection*/
    protected $db;
  
  
    /**
     * DatatableModelTrait constructor.
     *
     * @param Context $db
     */
    public function __construct(Context $db) {
      $this->db = $db;
    }
  
    /**
     * Returns table name from parsed classname ofr from property if is set
     * @return string
     */
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
  
    /**
     * Get Base class name
     * @return mixed
     */
    protected function getName(){
    $exploded = explode('\\', __CLASS__);
     return str_replace('Model', '', array_pop($exploded));
    }
  
    /**
     * Create all items selection
     * @return \Nette\Database\Table\Selection
     */
    public function getAllItems($where = []) {
      $table = $this->db->table($this->getTableName());
      if ($where) {
        foreach ($where as $condition => $values) {
          $table->where($condition, ...$values);
        }
      }
      return $table;
    }
  
    /**
     * Get one row by primary key
     * @param $id
     *
     * @return false|ActiveRow
     */
    public function getItemById($id) {
      return $this->db->table($this->getTableName())->get($id);
    }
  
    /**
     * Returns array of data for <select> dom element
     * @param       $valueGetter
     * @param       $textGetter
     * @param null  $excludeId
     * @param array $defaultValue
     *
     * @return array
     */
    public function getPairsForSelect($valueGetter, $textGetter, $excludeId = NULL, $defaultValue = [NULL => ''], $conditions = [] ) {
      $items = $this->getAllItems($conditions)->select($valueGetter . ',' . $textGetter);
      if ($excludeId) {
        $items->where($items->getPrimary() .' != ?', $excludeId);
      }
      return ($defaultValue ? $defaultValue : []) + $items->fetchPairs($valueGetter, $textGetter);
    }
  
    /**
     * Insert / Update database row with given values
     * @param $values
     */
    public function store($values) {
      $primaryKey = $this->getPrimaryKey();
      if ($id = Arrays::get($values, $primaryKey )) {
        $this->getAllItems()->where($primaryKey .' = ?', $id)->update($values);
      } else {
        $this->getAllItems()->insert($values);
      }
    }
  
    /**
     * Delete row (by mark or for real) from database table
     * @param ActiveRow $item
     * @param bool      $byMark
     */
    public function delete(ActiveRow $item, $byMark = FALSE) {
      if ($byMark) {
        $item->update(['deleted' => 1]);
      } else {
        $item->delete();
      }
    }
  
    /**
     * Returns table primary key column name
     * @return array|null|string
     */
    protected function getPrimaryKey() {
      return $this->db->table($this->getTableName())->getPrimary();
    }
  
    /**
     *
     * Check for unique value in table
     * @param $values
     * @param $key
     *
     * @return bool
     */
    public function checkUniqueValue($values, $key) {
      $value = Arrays::get($values , $key, FALSE);
      if (!$value || !is_null(Arrays::get($values,$this->getPrimaryKey()))) {
        return true;
      } else {
        return !$this->db->table($this->getTableName())->where($key . ' = ?', $value)->count();
      }
    }
  }