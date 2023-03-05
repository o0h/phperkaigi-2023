<?php
declare(strict_types=1);

namespace O0h\KantanFw\Database;

abstract class Repository
{
    public function __construct(readonly protected \PDO $con)
    {
    }

    public function execute($sql, $params = [])
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function fetch($sql, $params = [])
    {
        return $this->execute($sql, $params)->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->execute($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
