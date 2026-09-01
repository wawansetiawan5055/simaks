<?php
namespace App\Models;

class User extends BaseModel
{
    protected string $table = 'cbt_user';
    protected string $primaryKey = 'id';

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `username` = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
