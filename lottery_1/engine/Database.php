<?php  declare(strict_types=1);


class Database
{
    /**
     * The PDO object.
     * @var
     */
    private $pdo;

    /**
     * Connected to the database.
     * @var bool
     */
    private $is_connected;

    /**
     * PDO statement object.
     * @var PDOStatement
     */
    private $statement;

    /**
     * The database settings.
     * @var array
     */
    private $settings = [];

    /**
     * The parameters of the SQL query.
     * @var array
     */
    private $parameters = [];

    /**
     * Database constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->connect();
    }

    /**
     * Подключение к базе
     */
    private function connect()
    {
        $dsn = 'mysql:host=' . $this->settings['host'] . ';port=' . $this->settings['port'] .
            ';dbname=' . $this->settings['db_name'] . ';charset=' . $this->settings['charset'];

        try {
            $this->pdo = new PDO($dsn, $this->settings['username'], $this->settings['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

            $this->is_connected = true;
        } catch (PDOException $e) {
            exit(($e->getMessage()));
        }
    }

    /**
     * function закрывает подключение
     */
    public function closeConnection()
    {
        $this->pdo = null;
    }

    /**
     * @param string $query
     * @param array $parameters
     */
    private function init(string $query, array $parameters = [])
    {
        # подключена ли база
        if (!$this->is_connected){
            $this->connect();
        }

        try {
            # создаем объект для подготовки запроса
            # передаем в statement Prepare query
            $this->statement = $this->pdo->prepare($query);

            # обрабатываем parameters в Bind
            $this->bind($parameters);


            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param => $value) {
                    if (is_int($value[1])) {
                        $type = PDO::PARAM_INT;
                    } elseif (is_bool($value[1])) {
                        $type = PDO::PARAM_BOOL;
                    } elseif (is_null($value[1])) {
                        $type = PDO::PARAM_NULL;
                    } else {
                        $type = PDO::PARAM_STR;
                    }

                    $this->statement->bindValue($value[0], $value[1], $type);
                }
            }

            $this->statement->execute();

        } catch (PDOException $e) {
            exit(($e->getMessage()));
        }

        # обнуляем параметры
        $this->parameters = [];
    }

    /**
     * function обработка parameters
     * @return void
     * @param array $parameters
     */
    private function bind(array $parameters): void    # функция ничего не возвращает
    {
        if (!empty($parameters) and is_array($parameters)) {
            $columns = array_keys($parameters);

            foreach ($columns as $i=>&$column) {
                $this->parameters[sizeof($this->parameters)] = [':' . $column, $parameters[$column]];
            }
        }
    }

    /**
     * function запроса
     * @param string $query
     * @param array $parameters
     * @param int $mode
     * @return array|int|null
     */
    public function query(string $query, array $parameters = [], $mode = PDO::FETCH_ASSOC)
    {
        # удаяляем переносы и обрезаем пробелы с начала и в конце
        $query = trim(str_replace('\r', '', $query));

        $this->init($query, $parameters);

        $raw_statement = explode(' ', preg_replace("/\s+|\t+|\n+/", " ", $query));

        $statement = strtolower($raw_statement[0]);

        if ($statement === 'select' || $statement === 'show') {
            return $this->statement->fetchAll($mode);
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->statement->rowCount();
        } else {
            return null;
        }
    }
}