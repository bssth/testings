<?php

namespace Mike4ip\Cloud\Entity;

use Mike4ip\Cloud\Core\Database;

/**
 * Class User
 * @package Mike4ip\Cloud\Entity
 */
class User
{
    /**
     * @var \SafeMySQL
     */
    protected $db;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->db = Database::i();
    }

    /**
     * Получить хэш пароля из строки
     * @param string $password
     * @return string
     */
    protected function hashPassword(string $password): string
    {
        return hash('sha256', $password);
    }

    /**
     * Вход в существующий аккаунт (проверка логина и пароля)
     * @param string $username
     * @param string $password
     * @return int
     */
    public function login(string $username, string $password): int
    {
        $user = $this->db->getRow("SELECT * FROM `users` WHERE `username` = ?s", $username);

        if(!$user)
            return 0;
        elseif(!isset($user['password']) || $user['password'] !== $this->hashPassword($password))
            return -1;

        return $user['user_id'];
    }

    /**
     * Получить информацию по ID
     * @param int $uid
     * @return array
     */
    public function getInfo(int $uid): array
    {
        return $this->db->getRow("SELECT * FROM `users` WHERE `user_id` = ?i", $uid);
    }

    /**
     * Регистрация нового аккаунта
     * @param string $username
     * @param string $password
     * @return int
     */
    public function register(string $username, string $password): int
    {
        if(!strlen($username) || !strlen($password))
            return 0;

        $user = $this->db->getOne('SELECT COUNT(*) FROM `users` WHERE `username` = ?s', $username);

        if($user > 0)
            return 0;

        $this->db->query('INSERT INTO `users` (`username`, `password`) VALUES (?s, ?s)', $username, $this->hashPassword($password));
        return $this->db->insertId();
    }
}