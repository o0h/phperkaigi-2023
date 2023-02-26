<?php

class FollowingRepository extends DbRepository
{
    public function insert($userId, $followingId)
    {
        $sql = 'INSERT INTO following VALUES(:user_id, :following_id)';

        $stmt = $this->execute($sql, [
            ':user_id' => $userId,
            ':following_id' => $followingId,
        ]);
    }

    public function isFollowing($userId, $followingId)
    {
        $sql = "
            SELECT COUNT(user_id) as count
                FROM following
                WHERE user_id = :user_id
                    AND following_id = :following_id
        ";

        $row = $this->fetch($sql, [
            ':user_id' => $userId,
            ':following_id' => $followingId,
        ]);

        if ($row['count'] !== 0) {
            return true;
        }

        return false;
    }

}