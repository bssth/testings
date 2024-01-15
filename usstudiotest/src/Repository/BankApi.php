<?php
namespace App\Repository;

use DateTime;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class BankApi
 * @package App\Repository
 */
class BankApi
{
    /**
     * URL API Банка РФ
     */
    const ENDPOINT = 'http://www.cbr.ru/scripts/XML_dynamic.asp';

    /**
     * Код доллара
     */
    const CODE_USD = 'R01235';

    /**
     * Код евро
     */
    const CODE_EUR = 'R01239';

    /**
     * Заголовки валют
     */
    const TITLES = [
        self::CODE_USD => 'Доллар',
        self::CODE_EUR => 'Евро'
    ];

    /**
     * Дата, которая будет использоваться
     * @var DateTime
     */
    protected $date;

    /**
     * Валюта, о которой будем спрашивать у API
     * @var string
     */
    protected $currency;

    /**
     * Установить дату для последующих запросов
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * Установить валюту для последующих запросов
     * @param string $currency
     * @return bool
     */
    public function setCurrency(string $currency): bool
    {
        switch(strtolower($currency))
        {
            case 'usd':
                $this->currency = self::CODE_USD;
                return true;

            case 'eur':
                $this->currency = self::CODE_EUR;
                return true;

            default:
                return false;
        }
    }

    /**
     * Получить информацию о валюте
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function getInfo(): array
    {
        if(!$this->currency || !$this->date)
            throw new Exception('Currency or date not passed');

        $url = self::ENDPOINT;
        $d2 = clone $this->date;

        $params = [
            'date_req1' => $d2->modify('-1 day')->format('d/m/Y'),
            'date_req2' => $this->date->format('d/m/Y'),
            'VAL_NM_RQ' => $this->currency
        ];

        $client = HttpClient::create();
        $response = $client->request('GET', $url . '?' . http_build_query($params));
        $statusCode = $response->getStatusCode();

        if($statusCode !== 200)
            throw new Exception('HTTP error: ' . $statusCode);

        $content = $response->getContent();
        $content = simplexml_load_string($content);
        $result = [
            'title' => self::TITLES[$this->currency],
            'numbers' => [],
            'dates' => []
        ];

        $date_format = $this->date->format('d.m.Y');
        $previous = 0;

        if(!$content || !$content->Record)
            throw new Exception('No info');

        foreach($content->Record as $r) {
            $date = (string)$r->attributes()->Date;

            $result['dates'][$date] = [
                'date' => $date,
                'value' => ($val = floatval(str_replace(',', '.', $r->Value))),
                'nominal' => (int)$r->Nominal
            ];

            if($date_format === $date) {
                $result['numbers'] = [
                    'value' => $val,
                    'previous' => $previous,
                    'changed' => ($changed = round($val - $previous, 5)),
                    'is_up' => ($changed >= 0)
                ];
            } else
                $previous = $val;
        }

        return $result;
    }
}