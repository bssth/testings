<?php

namespace Mike4ip\Cloud\Controller;

use Mike4ip\Cloud\Core\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Upload\Storage\FileSystem;
use Upload\Validation\Size;
use Zend\Diactoros\Response;

/**
 * Контроллер для управления файлами
 * @package Mike4ip\Cloud\Controller
 */
class File
{
    /**
     * Загрузить файл в хранилище
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function upload(ServerRequestInterface $request): ResponseInterface
    {
        if(!is_numeric($uid = Session::i()->get('uid')))
            throw new \Exception('Unauthorized');

        $request = $request->getParsedBody();
        $storage = new FileSystem(__DIR__ . '/../../upload');
        $file = new \Upload\File('file', $storage);
        $file->addValidations([
            new Size('10M')
        ]);
        $new_filename = uniqid();
        $old_name = $file->getNameWithExtension();
        $file->setName($new_filename);

        try {
            $file->upload();
        } catch (\Exception $e) {
            return new Response\RedirectResponse('/?upload_error=1');
        }

        $name = $file->getNameWithExtension();
        $folder = isset($request['folder']) ? $request['folder'] : 0;
        $size = $file->getSize();

        $files = new \Mike4ip\Cloud\Entity\File();
        $files->saveFile($name, $uid, $folder, $size, $old_name);

        return new Response\RedirectResponse('/');
    }
}