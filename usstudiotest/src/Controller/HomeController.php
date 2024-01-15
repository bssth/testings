<?php
namespace App\Controller;
use App\Repository\BankApi;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Основной контроллер, отображающий нужные нам котировки
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * Основная страница с курсами валют
     * @return Response
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function index()
    {
        // получаем информацию о запросе
        $request = Request::createFromGlobals();

        // проверить, указал ли пользователь дату, иначе смотреть 10 дней назад
        if(!($date = $request->get('date')))
            $date = '2002-02-02';

        $date_obj = new DateTime($date);
        $error = null;
        $info = [];

        // создаём экземпляр класса для работы с API банка
        // и получаем нужную информацию
        $api = new BankApi();
        $api->setDate($date_obj);

        // получаем нужную информацию, записываем ошибку, если есть
        try {
            foreach(['usd', 'eur'] as $curr) {
                $api->setCurrency($curr);
                $info[] = $api->getInfo();
            }
        } catch (Exception $e) {
            $error = 'Ошибка при получении информации по этой дате';
        }

        return $this->render('base.html.twig', [
            'date' => $date,
            'info' => $info,
            'error' => $error
        ]);
    }
}