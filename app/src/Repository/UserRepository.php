<?php

namespace App\Repository;
use DbRepository;
use O0h\KantanFw\Database\Repository;

class UserRepository extends Repository
{
    public function insert($userName, $password)
    {
        $password = $this->hashPassword($password);
        $now = new \DateTimeImmutable();

        $sql = "
            INSERT INTO user(user_name, password, created_at)
            VALUES (:user_name, :password, :created_at)
        ";

        $stmt = $this->execute($sql, [
            ':user_name' => $userName,
            ':password' => $password,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ]);
    }

    public function fetchByUserName($userName)
    {
        $sql = "SELECT * FROM user WHERE user_name = :user_name";

        return $this->fetch($sql, [':user_name' => $userName]);
    }

    public function isUniqueUserName($userName)
    {
        $sql = "SELECT COUNT(id) as count FROM user WHERE user_name = :user_name";

        $row = $this->fetch($sql, [':user_name' => $userName]);
        if ($row['count'] === 0) {
            return true;
        }

        return false;
    }

    public function hashPassword($password)
    {
        return sha1($password . 'SecretKey');
    }

    public function fetchAllFollowingsByUserId($userId)
    {
        $sql = "
            SELECT u.*
            FROM user u 
                LEFT JOIN following f ON f.following_id = u.id
            WHERE f.user_id = :user_id
        ";

        return $this->fetchAll($sql, [':user_id' => $userId]);
    }
}