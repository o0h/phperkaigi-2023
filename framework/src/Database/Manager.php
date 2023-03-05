<?php

declare(strict_types=1);

namespace O0h\KantanFw\Database;

class Manager
{
    protected $connections = [];

    protected $repositoryConnectionMap = [];

    protected $repositories = [];

    public function connect($name, $params)
    {
        $params = [
            ...[
                'dsn' => null,
                'user' => '',
                'password' => '',
                'options' => [],
            ],
            ...$params
        ];

        $con = new PDO(
            $params['dsn'],
            $params['user'],
            $params['password'],
            $params['options'],
        );

        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connections[$name] = $con;
    }

    public function getConnection($name = null)
    {
        if (is_null($name)) {
            return current($this->connections);
        }

        return $this->connections['name'];
    }

    public function setRepositoryConnectionMap($repositoryName, $name)
    {
        $this->repositoryConnectionMap[$repositoryName] = $name;
    }

    public function getConnectionForRepository($repositoryName)
    {
        if (isset($this->repositoryConnectionMap[$repositoryName])) {
            $name = $this->repositoryConnectionMap[$repositoryName];
            $con = $this->getConnection($name);
        } else {
            $con = $this->getConnection();
        }

        return $con;
    }

    public function get($repostoryName)
    {
        if (!isset($this->repositories[$repostoryName])) {
            $repositoryClass = $repostoryName . 'Repository';
            $con = $this->getConnectionForRepository($repostoryName);

            $repository = new $repositoryClass($con);
            $this->repositories[$repostoryName] = $repository;
        }

        return $this->repositories[$repostoryName];
    }

    public function __destruct()
    {
        foreach ($this->repositories as $repository) {
            unset($repository);
        }

        foreach ($this->connections as $con) {
            unset($con);
        }
    }
}