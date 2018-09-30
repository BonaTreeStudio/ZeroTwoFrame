<?php
/**
 * Description of CDbRecord
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */
namespace Core\Parts\Database;

class CDbRecord {
    const RULE_AI = 'AUTO_INCREMENT';
    const RULE_REQUIERED = true;
    const RULE_NOT_REQUIERED = false;
    const RULE_NO_DEFAULT = NULL;
    const RULE_ANY_LENGTH = NULL;
    const INDEX_PRIVATE_KEY = 'PRIMARY KEY';
    const INDEX_FOREIGN_KEY = 'FOREIGN KEY';
    const INDEX_SEARCH = 'INDEX';
    const INDEX_UNICUE = 'UNIQUE';
    const FOREIGN_KEY_ON_DELETE_CASCADE = 'ON DELETE CASCADE';
    const FOREIGN_KEY_ON_DELETE_RESTRICT = 'ON DELETE RESTRICT';
    const FOREIGN_KEY_ON_DELETE_NOTHING = '';
    const FOREIGN_KEY_ON_UPDATE_CASCADE = 'ON UPDATE CASCADE';
    const FOREIGN_KEY_ON_UPDATE_RESTRICT = 'ON UPDATE RESTRICT';
    const FOREIGN_KEY_ON_UPDATE_NOTHING = '';

    /**
     *
     * @var CDb
     */
    protected $db;
    protected $initialValues = [];

    /**
     *
     * @var CDbQueryConstructor
     */
    public $constructor;
    public $isNewRecord = true;

    //put your code here
    public function __construct($attribures = [])
    {
        $cdb = CDb;
        $cdbQQ = CDbQueryConstructor;
        $this->db = $cdb::factory($this->dbConnection());
        $this->constructor = $cdbQQ::factory([
                'database' => $this->database(),
                'engine' => $this->engine(),
                'table' => $this->table(),
                'rules' => $this->getParsedRules(),
                'indexes' => $this->indexes(),
        ]);
        $this->setAttributes($attribures);
    }

    public function setAttributes($attribures)
    {
        foreach ($this->getParsedRules() as $field => $rules) {
            if (isset($attribures[$field])) {
                if (!isset($this->initialValues[$field])) {
                    $this->initialValues[$field] = $attribures[$field];
                }
                $this->$field = $attribures[$field];
            }
        }
    }

    public function getAttributes()
    {
        $return = [];
        foreach ($this->getParsedRules() as $field => $rules) {
            if (isset($this->$field)) {
                $return[$field] = $this->$field;
            }
        }
        return $return;
    }

    protected function dbConnection()
    {
        return DEFAULT_DATABASE_CONFIG;
    }

    protected function database()
    {
        return $this->db->getConnectionOption('database');
    }

    protected function engine()
    {
        $engine = $this->db->getConnectionOption('table-engine');
        return ($engine) ? $engine : DEFAULT_DATABASE_ENGINE;
    }

    protected function table()
    {
        return '';
    }

    protected function rules()
    {
        return [];
    }

    protected function indexes()
    {
        return [];
    }

    protected function relations()
    {
        return [];
    }

    /**
     * Возвращает текущую модель
     * @return self
     */
    public static function model()
    {
        $arClass = get_called_class();
        return new $arClass();
    }

    /**
     * Возвращает текущую модель
     * @return self
     */
    public static function populateRecord($data)
    {
        $arClass = get_called_class();
        $record = new $arClass($data);
        $record->isNewRecord = false;
        return $record;
    }

    public function beforeFind()
    {

    }

    public function afterFind()
    {

    }

    /**
     * Изет записи
     * @param CDbSelectionCriteria $criteria
     * @return self[] Description
     */
    public function findAll($criteria = NULL)
    {
        $this->beforeFind();
        $this->constructor->setCriteria($criteria);
        $rows = $this->db->queryRows($this->constructor->SELECT);
        $records = [];

        foreach ($rows as $row) {
            $rec = self::populateRecord($row);
            $rec->afterFind();
            $records[] = $rec;
        }
        $this->constructor->flushCriteria();
        return $records;
    }

    /**
     * Изет запись
     * @param CDbSelectionCriteria $criteria
     * @return self Description
     */
    public function find($criteria = NULL)
    {
        if (empty($criteria)) {
            $criteria = new CDbSelectionCriteria();
        }
        if (!$criteria->hasLimit()) {
            $criteria->limit(1);
        }
        $rows = $this->findAll($criteria);
        return (empty($rows)) ? false : array_shift($rows);
    }

    /**
     * Изет запись
     * @param [] $attributes
     * @return self[] Description
     */
    public function findAllByAttributes($attributes = [])
    {
        $criteria = new CDbSelectionCriteria();
        foreach ($this->getParsedRules() as $field => $rule) {
            if (isset($attributes[$field])) {
                $criteria->compare($field, $attributes[$field]);
            }
        }
        return $this->findAll($criteria);
        return (empty($rows)) ? false : array_shift($rows);
    }

    /**
     * Изет запись
     * @param [] $attributes
     * @return [] Description
     */
    public function findByAttributes($attributes = [])
    {
        $rows = $this->findAllByAttributes($attributes);
        return (empty($rows)) ? false : array_shift($rows);
    }

    /**
     * Изет запись
     * @param [] $pk
     * @return self
     */
    public function findByPk($pk = [])
    {
        $criteria = new CDbSelectionCriteria();
        $indexes = $this->indexes();
        if (empty($indexes[CDbRecord::INDEX_PRIVATE_KEY])) {
            throw new Exception("AR ".get_called_class()." has no Primary key index, so it does not support pk search...");
        }
        $indexCols = explode(str_replace([', ', ','], '|', array_shift($indexes[CDbRecord::INDEX_PRIVATE_KEY])));
        foreach ($indexCols as $field) {
            if (!empty($pk[$field])) {
                $criteria->compare($field, $pk);
            }
        }
        return $this->find($criteria);
    }

    public function beforeSave()
    {
        return $this->validate();
    }

    public function afterSave()
    {

    }

    public function beforeDelete()
    {
        return (!$this->isNewRecord);
    }

    public function afterDelete()
    {
        return $this->validate();
    }

    public function save($scenario = 'normal')
    {
        if ($scenario == 'normal') {
            if ($this->beforeSave()) {
                if (!$this->constructor->areArRecrdsSetted()) {
                    $this->constructor->setArRecords([$this]);
                }
                if ($this->isNewRecord) {
                    $result = $this->db->query($this->constructor->INSERT);
                    if (property_exists($this, 'id')) {
                        $this->id = $this->db->getLastInsertId();
                        $this->isNewRecord = false;
                    }
                } else {
                    $result = $this->db->query($this->constructor->UPDATE);
                }
                $this->constructor->flushArRecrds();
                $this->afterSave();
                return $result;
            }
        } else {
            if ($this->constructor->areArRecrdsSetted()) {
                $result = $this->db->query($this->constructor->$scenario);
                $this->constructor->flushArRecrds();
                return $result;
            }
        }
        return false;
    }

    /**
     *
     * @param self $records
     */
    public static function saveMassive($records)
    {
        $massiveSend = [];
        foreach ($records as $record) {
            if ($record->validate()) {
                if ($record->beforeSave()) {
                    $massiveSend[get_class($record)][($record->isNewRecord ? "INSERT" : "UPDATE")][] = $record;
                }
            }
        }
        foreach ($massiveSend as $modelClass => $scenarioActions) {
            $model = $modelClass::model();
            foreach ($scenarioActions as $scenario => $actions) {
                foreach ($actions as $method => $recordsTo) {
                    if ($model->setRecords($recordsTo)->save($scenario)) {
                        foreach ($recordsTo as $record) {
                            $record->afterSave();
                        }
                    }
                }
            }
        }
    }

    public function delete()
    {
        if ($this->beforeDelete()) {
            if (!$this->constructor->areArRecrdsSetted()) {
                $this->constructor->setArRecords([$this]);
            }
            $result = $this->db->query($this->constructor->DELETE);
            $this->constructor->flushArRecrds();
            $this->afterDelete();
            return $result;
        }
        return false;
    }

    public static function deleteMassive($records)
    {
        $massiveSend = [];
        foreach ($records as $record) {
            if ($record->beforeDelete()) {
                $massiveSend[get_class($record)][] = $record;
            }
        }
        foreach ($massiveSend as $modelClass => $recordsTo) {
            $model = $modelClass::model();
            if ($model->setRecords($records)->save('DELETE')) {
                foreach ($records as $record) {
                    $record->afterDelete();
                }
            }
        }
    }

    public function setRecords($records)
    {
        $this->constructor->setArRecords($records);
        return $this;
    }

    public function getError()
    {
        return $this->db->getLastError();
    }

    public function getLastQuery()
    {
        return $this->db->getLastQuery();
    }

    public function validate()
    {
        return true;
    }

    public function getInitialValue($field)
    {
        return $this->initialValues[$field] ?? NULL;
    }

    public function setup()
    {
        if (!$this->db->query($this->constructor->CREATE_TABLE)) {
            throw new Exception("SETUP FAILED: failed to create table ".$this->table()." with query:\n ".$this->constructor->CREATE_TABLE."With error: ".$this->db->getLastError()."!!!\n");
        }
        echo "SETUP: table ".$this->table()." created; \n";
    }

    protected function getParsedRules()
    {
        $parsedRules = [];
        foreach ($this->rules() as $rule) {
            $rFields = explode('|', str_replace([', ', ','], '|', array_shift($rule)));
            $type = array_shift($rule);
            $length = array_shift($rule);
            $default = array_shift($rule);
            $required = array_shift($rule);
            $ai = array_shift($rule);
            foreach ($rFields as $field) {
                $parsedRules[$field] = [
                    'type' => $type,
                    'length' => $length,
                    'default' => $default,
                    'required' => $required,
                    'ai' => $ai,
                ];
            }
        }
        return $parsedRules;
    }
}