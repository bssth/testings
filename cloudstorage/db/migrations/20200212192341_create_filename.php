<?php

use Phinx\Migration\AbstractMigration;

class CreateFilename extends AbstractMigration
{
    /**
     * Добавление поля для имени файла
     */
    public function change()
    {
        $this->table('files')
            ->addColumn('filename', 'string', ['limit' => 64])
            ->save();
    }
}
