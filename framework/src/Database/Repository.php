<?php

declare(strict_types=1);

namespace O0h\KantanFw\Database;

use PDO;
use PDOStatement;
use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

#[CodeCoverageIgnore]
// FIXME: ちゃんとテストを書こうね
abstract class Repository
{
    public function __construct(readonly protected PDO $con)
    {
    }

    /**
     * @phpstan-param array<string, scalar> $params
     */
    public function execute(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * @phpstan-param array<string, scalar> $params
     */
    public function fetch(string $sql, array $params = []): mixed
    {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @phpstan-param array<string, scalar> $params
     * @phpstan-return array<string, mixed>|false
     */
    public function fetchAll(string $sql, array $params = []): array|false
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
