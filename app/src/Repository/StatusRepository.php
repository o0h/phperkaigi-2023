<?php

declare(strict_types=1);

namespace App\Repository;

use O0h\KantanFw\Database\Repository;

class StatusRepository extends Repository
{
    public function insert($userId, $body)
    {
        $now = new \DateTimeImmutable();

        $sql = "
            INSERT INTO status(user_id, body, created_at)
                VALUES(:user_id, :body, :created_at)
        ";

        $this->execute($sql, [
            ':user_id' => $userId,
            ':body' => $body,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ]);
    }

    public function fetchAllPersonalArchivesByUserId($userId)
    {
        $sql = "
            SELECT a.*, u.user_name
            FROM status a
                LEFT JOIN user u ON a.user_id = u.id
                LEFT JOIN following f ON f.following_id = a.user_id
                    AND f.user_id = :user_id
            WHERE f.user_id = :user_id OR u.id = :user_id
            ORDER BY a.created_at DESC
        ";

        return $this->fetchAll($sql, [':user_id' => $userId]);
    }

    public function fetchAllByUserId($userId)
    {
        $sql = "
            SELECT a.*, u.user_name
                FROM status a
                    LEFT JOIN user u ON a.user_id = u.id
                WHERE u.id = :user_id
                ORDER BY a.created_at DESC
        ";

        return $this->fetchAll($sql, [':user_id' => $userId]);
    }

    public function fetchByIdAndUserName($id, $userName)
    {
        $sql = "
            SELECT a.*, u.user_name
                FROM status a
                    LEFT JOIN user u ON u.id = a.user_id
                WHERE a.id = :id
                    AND u.user_name = :user_name
        ";

        return $this->fetch($sql, [
            ':id' => $id,
            ':user_name' => $userName,
        ]);
    }
}
