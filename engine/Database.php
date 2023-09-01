<?php
class DatabaseConnection
{
    public $db;

    function __construct($host, $username, $password, $dbname)
    {
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error connecting to database: " . $e->getMessage());
        }
    }
}
class GenerateMySqlSQL extends DatabaseConnection
{
    protected $where = [];
    protected $whereParams = [];

    public function where($column, $operator, $value)
    {
        $paramName = ":$column";
        $this->where[] = "$column $operator $paramName";
        $this->whereParams[$paramName] = $value;
        return $this;
    }

    public function getWhereClause()
    {
        return $this->where ? 'WHERE ' . implode(' AND ', $this->where) : '';
    }

    public function clearWhere()
    {
        $this->where = [];
        $this->whereParams = [];
    }

    public function all($table)
    {
        $whereClause = $this->getWhereClause();
        $sql = "SELECT * FROM $table $whereClause";
        $stmt = $this->db->prepare($sql);

        foreach ($this->whereParams as $paramName => $value) {
            $stmt->bindValue($paramName, $value);
        }

        $stmt->execute();
        $this->clearWhere(); // Clear the WHERE conditions for subsequent queries
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function create($table, $data)
    {
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $stmt = $this->db->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    function read($table, $id)
    {
        $sql = "SELECT * FROM $table WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function update($table, $id, $data)
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $set = implode(", ", $set);
        $sql = "UPDATE $table SET $set WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        return $stmt->execute();
    }

    function updateConfig($table, $id, $data)
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $set = implode(", ", $set);
        $sql = "UPDATE $table SET $set WHERE id_config = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        return $stmt->execute();
    }

    function delete($table, $id)
    {
        $sql = "DELETE FROM $table WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        return $stmt->execute();
    }
}

class TableCreator extends DatabaseConnection
{

    public function createTable($tableName, $columns)
    {
        $sql = "CREATE TABLE IF NOT EXISTS $tableName (";
        $columnSql = [];
        foreach ($columns as $columnName => $columnType) {
            $columnSql[] = "$columnName $columnType";
        }
        $sql .= implode(", ", $columnSql);
        $sql .= ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $sql;
    }
}

function query_builder()
{
    function parseEnvFile($filePath)
    {
        $envVariables = [];

        if (file_exists($filePath)) {
            $envFileContents = file_get_contents($filePath);
            $lines = explode("\n", $envFileContents);

            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $envVariables[$key] = $value;
                }
            }
        }

        return $envVariables;
    }
    $envFilePath = './.env';
    $envVariables = parseEnvFile($envFilePath);
    $db_info = array(
        $envVariables['DB_HOST'],     // host
        $envVariables['DB_USER'],     // user
        $envVariables['DB_PASSWORD'], // password
        $envVariables['DB_DATABASE']      // database name
    );
    return new GenerateMySqlSQL(...$db_info);
}
// $crud = new GenerateMySqlSQL(...$db_info);
// $tableCreator = new TableCreator(...$db_info);
