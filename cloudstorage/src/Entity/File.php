<?php

namespace Mike4ip\Cloud\Entity;

use Mike4ip\Cloud\Core\Database;

/**
 * Class File
 * @package Mike4ip\Cloud\Entity
 */
class File
{
    /**
     * @var \SafeMySQL
     */
    protected $db;

    /**
     * File constructor.
     */
    public function __construct()
    {
        $this->db = Database::i();
    }

    /**
     * Сохранить ранее загруженный файл в базу
     * @param string $filename
     * @param int $owner_id
     * @param int $folder
     * @param int $size
     * @param string $original
     * @return int
     */
    public function saveFile(string $filename, int $owner_id, int $folder = 0, int $size = -1, string $original = ''): int
    {
        $this->db->query('INSERT INTO `files` (`is_folder`, `filename`, `parent_file_id`, `size`, `owner_id`, `original`) VALUES (0, ?s, ?i, ?i, ?i, ?s)', $filename, $folder, $size, $owner_id, $original);
        return $this->db->insertId();
    }

    /**
     * Получить список файлов
     * @param int $owner_id
     * @param int $folder
     * @return array
     */
    public function getFiles(int $owner_id, int $folder = 0): array
    {
        return $this->db->getAll('SELECT * FROM `files` WHERE `owner_id` = ?i AND `parent_file_id` = ?i', $owner_id, $folder);
    }
}