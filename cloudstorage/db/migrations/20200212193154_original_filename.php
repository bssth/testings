<?php

use Phinx\Migration\AbstractMigration;

class OriginalFilename extends AbstractMigration
{
    /**
     * Поле с оригинальным именем файла
     */
    public function change()
    {
        $this->table('files')
            ->addColumn('original', 'string', ['limit' => 64])
            ->save();
    }
}
