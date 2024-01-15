<?php

namespace Mike4ip\Cloud\Controller;

use Mike4ip\Cloud\Core\Session;
use Mike4ip\Cloud\Core\Templater;
use Mike4ip\Cloud\Entity\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

/**
 * Контроллер для главных страниц (авторизация и само окно с файловым хранилищем)
 * @package Mike4ip\Cloud
 */
class Home
{
    /**
     * Главный интерфейс файлового хранилища;
     * доступен только если пользователь авторизован
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        if(!is_numeric($uid = Session::i()->get('uid'))) {
            return new Response\RedirectResponse('/login');
        }

        $user = new User();
        $user_info = $user->getInfo($uid);
        $files = new \Mike4ip\Cloud\Entity\File();
        $files_list = $files->getFiles($uid);

        $template = Templater::i()->load('main.html')->render([
            'user' => $user_info,
            'files' => $files_list
        ]);

        $response = new Response;
        $response->getBody()->write($template);
        return $response;
    }

    /**
     * Страница входа
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function login(ServerRequestInterface $request): ResponseInterface
    {
        $template = Templater::i()->load('login.html')->render();

        $response = new Response;
        $response->getBody()->write($template);
        return $response;
    }

    /**
     * Уничтожить текущую сессию
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        Session::i()->clear();
        return new Response\RedirectResponse('/');
    }

    /**
     * Страница регистрации
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function register(ServerRequestInterface $request): ResponseInterface
    {
        $template = Templater::i()->load('register.html')->render();

        $response = new Response;
        $response->getBody()->write($template);
        return $response;
    }

    /**
     * Регистрация аккаунта
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function register_do(ServerRequestInterface $request): ResponseInterface
    {
        $request_params = $request->getParsedBody();
        $user = new User();

        $registered_id = $user->register(
            $request_params['username'],
            $request_params['password']
        );

        if(!$registered_id)
            return new Response\RedirectResponse('/register?error=1');

        Session::i()->set('uid', $registered_id);
        return new Response\RedirectResponse('/');
    }

    /**
     * Авторизация (вход в аккаунт)
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function login_do(ServerRequestInterface $request): ResponseInterface
    {
        $request_params = $request->getParsedBody();
        $user = new User();

        $logged_id = $user->login(
            $request_params['username'],
            $request_params['password']
        );

        if(!$logged_id)
            return new Response\RedirectResponse('/login?error=1');

        Session::i()->set('uid', $logged_id);
        return new Response\RedirectResponse('/');
    }
}