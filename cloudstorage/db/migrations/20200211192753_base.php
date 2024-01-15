<?php

use Phinx\Migration\AbstractMigration;

class Base extends AbstractMigration
{
    /**
     * Базовая версия структуры базы данных
     */
    public function change()
    {
        // Таблица с пользователями
        $table = $this->table('users', ['id' => 'user_id']);

        $table->addColumn('username', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 64]) // для хэша пароля нужно ровно 64 символа
            ->addColumn('registered', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex('username') // индекс юзернейма позволит быстрее проводить авторизацию
            ->changeComment('Таблица с зарегистрированными пользователями')
            ->save();

        // Таблица с файлами
        $table = $this->table('files', ['id' => 'file_id']);

        $table->addColumn('is_folder', 'boolean')
            ->addColumn('parent_file_id', 'integer')
            ->addColumn('size', 'integer')
            ->addColumn('owner_id', 'integer')
            ->addColumn('uploaded', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex('owner_id') // индекс владельца файла, чтоб быстрее производить поиск
            ->addIndex('parent_file_id')
            ->changeComment('Таблица со всеми загруженными файлами')
            ->save();
    }
}
