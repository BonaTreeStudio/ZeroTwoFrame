<?php
/**
 * PDO mysql engine
 * modern type
 * more protected
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */
namespace Core\Parts\Database\Engines;

class CDbPDO
{
    protected static $_INSTANCE = [];
    private $configId = NULL;
    private $connectionLink = NULL;
    private $dataSet = NULL;
    private $lastQuery = NULL;
    protected $host = 'localhost';
    protected $user = 'user';
    protected $password = 'password';
    protected $database = 'user';
    protected $port = '3306';

    public static function getInstance($connection): self
    {
        if (empty(self::$_INSTANCE[$connection])) {
            self::$_INSTANCE[$connection] = new self($connection);
        }
        return self::$_INSTANCE[$connection];
    }

    public function __construct($connection)
    {
        $this->configId = $connection;
        $this->bind(CApp::getInstance()->getConfig(DATABASE_CONFIG, $connection));
        $this->connect();
    }

    protected function connect()
    {
        $this->connectionLink = @mysqli_connect($this->host, $this->user, $this->password, $this->database, $this->port);
        if (empty($this->connectionLink)) {
            throw new Exception("Failed to connect to mysql via {$this->configId} configuration!");
        }
    }

    /**
     * Делает запроc в бд отдавая лишь признак успешности
     * @return boolean
     */
    public function query($query)
    {
        mysqli_escape_string($this->connectionLink, $query);
        $this->lastQuery = $query;
        return (boolean) (mysqli_query($this->connectionLink, $query));
    }

    /**
     * Делает запроc в бд получет много строк
     * @return []
     */
    public function queryRows($query)
    {
        mysqli_escape_string($this->connectionLink, $query);
        $this->lastQuery = $query;
        $resultLink = mysqli_query($this->connectionLink, $query);
        if (empty($resultLink)) {
            return [];
        }
        $rows = mysqli_fetch_all($resultLink, MYSQLI_ASSOC);
        return empty($rows) ? [] : $rows;
    }

    /**
     * Делает запроc в бд получет oдну строку
     * @return []
     */
    public function queryRow($query)
    {
        $rows = $this->queryRows($query);
        return empty($rows) ? [] : array_shift($rows);
    }

    public function getLastError()
    {
        return $this->connectionLink->error;
    }

    public function getLastQuery()
    {
        return $this->lastQuery;
    }

    public function getLastInsertId()
    {
        return mysqli_insert_id($this->connectionLink);
    }

    public function clearString($string): string
    {
        return htmlspecialchars($string);
    }

    /**
     * Возвращает настройки подключения бд или false если такой опции нет
     * @param string $name
     * @return mixed
     */
    public function getConnectionOption($name)
    {
        $config = CApp::getInstance()->getConfig(DATABASE_CONFIG, $this->configId);
        if (!empty($config) && isset($config[$name])) {
            return $config[$name];
        }
        return false;
    }

    public static function factory($connection)
    {
        return self::getInstance($connection);
    }
}