<?php
/**
 * Providing query cunstruction.
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 *
 * @property string $CREATE_TABLE Запрос создания таблицы
 * @property string $DROP_TABLE Запрос удаления таблицы
 * @property string $TRUNCATE_TABLE Запрос очистки таблицы
 * @property string $INSERT Запрос вставки значений в таблицу
 * @property string $UPDATE Запрос обновления значений в таблице
 * @property string $DELETE Запрос удаления значений в таблице
 * @property string $SELECT Запрос получения данных
 */
class CDbSimpleQueryConstructor extends CAppComponent
{
    protected $database;
    protected $engine;
    protected $table;
    protected $rules;
    protected $indexes;
    protected $arRecords = [];

    /**
     *
     * @var CDbSelectionCriteria
     */
    protected $criteria = NULL;

    public function __construct($tableData)
    {
        $this->bind($tableData);
    }

    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    public function isCriteriaSetted()
    {
        return !empty($this->criteria);
    }

    public function flushCriteria()
    {
        $this->criteria = NULL;
    }

    public function setArRecords($records)
    {
        $this->arRecords = $records;
    }

    public function areArRecrdsSetted()
    {
        return !empty($this->arRecords);
    }

    public function flushArRecrds()
    {
        $this->arRecords = [];
    }

    public function __get($name)
    {
        if (!in_array($name, get_class_vars(self::class))) {
            return $this->makeQuery($name);
        }
        return $this->$name;
    }

    /**
     *
     * @param type $sqlCommand
     * @return mixed запрос или false если подобный запрос невозможно создать
     */
    public function makeQuery($sqlCommand): string
    {
        $method = 'make_'.$sqlCommand;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return false;
    }

    public function make_CREATE_TABLE()
    {
        $query = "CREATE TABLE IF NOT EXISTS `{$this->database}`.`$this->table` (\n";
        $insideStatements = [];
        foreach ($this->rules as $field => $rules) {
            $row = '';
            if (empty($rules['type'])) {
                throw new Exception("CREATE TABLE FAILER: {$field} of table {$this->database}.$this->table has no type in rule config!");
            }
            $row .= "`{$field}` {$rules['type']}";
            $row .= (isset($rules['length']) ? "({$rules['length']})" : "");
            if (isset($rules['default'])) {
                $row .= " DEFAULT ";
                $dVal = $rules['default'];
                if (!is_numeric($dVal) || $dVal !== 'NULL') {
                    $dVal = "'{$dVal}'";
                }
                $row .= $dVal;
            }
            $row .= (!empty($rules['required']) ? " NOT NULL" : "");
            $row .= (!empty($rules['ai']) ? " {$rules['ai']}" : "");
            $insideStatements[] = $row;
        }
        foreach ($this->indexes as $index) {
            $indexType = array_shift($index);
            switch ($indexType) {
                case CDbRecord::INDEX_PRIVATE_KEY:
                    $indexCols = str_replace(' ', '', array_shift($index));
                    if (empty($indexCols)) {
                        throw new Exception("CREATE TABLE FAILER: private key requier columns!");
                    }
                    $insideStatements[] = "{$indexType} ({$indexCols})";
                    break;
                case CDbRecord::INDEX_FOREIGN_KEY:
                    $indexCols = str_replace(' ', '', array_shift($index));
                    if (empty($indexCols)) {
                        throw new Exception("CREATE TABLE FAILER: foreign key requier columns!");
                    }
                    $linkTableCol = str_replace(' ', '', array_shift($index));
                    if (empty($linkTableCol)) {
                        throw new Exception("CREATE TABLE FAILER: foreign key requier link table and column in table(column) format!");
                    }
                    $onDelete = (string) array_shift($index);
                    $onUpdate = (string) array_shift($index);
                    if (empty($onDelete) && empty($onUpdate)) {
                        throw new Exception("CREATE TABLE FAILER: foreign key requier actions for delete or update!");
                    }
                    $insideStatements[] = "{$indexType} ({$indexCols}) REFERENCES {$linkTableCol} {$onDelete} {$onUpdate}";
                    break;
                case CDbRecord::INDEX_SEARCH:
                    $indexCols = str_replace(' ', '', array_shift($index));
                    if (empty($indexCols)) {
                        throw new Exception("CREATE TABLE FAILER: search index requier columns!");
                    }
                    $insideStatements[] = "{$indexType} ({$indexCols})";
                    break;
                case CDbRecord::INDEX_UNICUE:
                    $indexCols = str_replace(' ', '', array_shift($index));
                    if (empty($indexCols)) {
                        throw new Exception("CREATE TABLE FAILER: unique index requier columns!");
                    }
                    $insideStatements[] = "{$indexType} ({$indexCols})";
                    break;
            }
        }
        $query .= implode(",\n", $insideStatements);
        $query .= "\n) ENGINE = \"{$this->engine}\";\n";
        return $query;
    }

    public function make_DROP_TABLE()
    {
        return "DROP TABLE IF EXISTS `{$this->database}`.`$this->table`;";
    }

    public function make_TRUNCATE_TABLE()
    {
        return "TRUNCATE TABLE IF EXISTS `{$this->database}`.`$this->table`;";
    }

    public function make_INSERT()
    {
        $query = "INSERT INTO `{$this->database}`.`$this->table` ";
        $innerFields = [];
        foreach ($this->rules as $field => $rules) {
            $innerFields[] = "`{$field}`";
        }
        $query .= "(".implode(', ', $innerFields).") VALUES \n ";
        $innerValues = [];
        foreach ($this->arRecords as $record) {
            $recordRow = [];
            foreach ($this->rules as $field => $rules) {
                $recordRow[] = "'{$record->$field}'";
            }
            $innerValues[] = "(".implode(', ', $recordRow).")";
        }
        $query .= implode(",\n", $innerValues).";\n";
        return $query;
    }

    public function make_UPDATE()
    {
        $query = '';
        foreach ($this->arRecords as $record) {
            $query .= "UPDATE `{$this->database}`.`$this->table` SET ";
            $innerFields = [];
            foreach ($this->rules as $field => $rules) {
                $innerFields[] = "`{$field}` = '{$record->$field}'";
            }
            $query .= implode(', ', $innerFields)." WHERE ";
            $innerWhere = [];
            //Если нет прайвет ключа, то сравниваем по всем
            if (!isset($this->indexes[CDbRecord::INDEX_PRIVATE_KEY])) {
                foreach ($this->rules as $field => $rules) {
                    $innerWhere[] = "`$field` = '{$record->getInitialValue($field)}'";
                }
            } else {
                $indexCols = explode(str_replace([', ', ','], '|', array_shift($this->indexes[CDbRecord::INDEX_PRIVATE_KEY])));
                foreach ($indexCols as $field) {
                    $innerWhere[] = "`$field` = '{$record->getInitialValue($field)}'";
                }
            }
            $query .= implode(' AND ', $innerWhere).";\n";
        }
        return $query;
    }

    public function make_DELETE()
    {
        $query = '';
        foreach ($this->arRecords as $record) {
            $query .= "DELETE FROM `{$this->database}`.`$this->table` WHERE ";
            //Если нет прайвет ключа, то сравниваем по всем
            if (!isset($this->indexes[CDbRecord::INDEX_PRIVATE_KEY])) {
                foreach ($this->rules as $field => $rules) {
                    $innerWhere[] = "`$field` = '{$record->$field}'";
                }
            } else {
                $indexCols = explode(str_replace([', ', ','], '|', array_shift($this->indexes[CDbRecord::INDEX_PRIVATE_KEY])));
                foreach ($indexCols as $field) {
                    $innerWhere[] = "`$field` = '{$record->$field}'";
                }
            }
            $query .= implode(' AND ', $innerWhere).";\n";
        }
        return $query;
    }

    public function make_SELECT()
    {
        $query = "SELECT * FROM `{$this->database}`.`$this->table`";
        if ($this->isCriteriaSetted()) {
            $query .= "WHERE ".$this->criteria->getCriteria();
        }
        return $query.";\n";
    }

    public static function factory($tableData): self
    {
        return new self($tableData);
    }
}