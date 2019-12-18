<?php


namespace Drupal\api_rest\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\RequestException;

class ApiController extends ControllerBase
{

    /**
     * {@inheritdoc}
     */
    public function titleItems()
    {
        return $this->t('List Item');
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $client = \Drupal::httpClient();
        $url = 'http://api.example.com/test/';
        //$items = [];
        try {
            $response = $client->get($url);
            $data = $response->getBody();

            $response = (object)json_decode($data, true);
            foreach ($response->data as $dat) {
                if(!$dat['logo']){
                    $dat['logo'] =  file_create_url(drupal_get_path('module', 'api_rest') . '/assets/img/logo.png');
                }
                $items[] = [
                    'id' => $dat['id'],
                    'title' => $dat['raison_soc'],
                    'logo' => $dat['logo']
                ];
            }
        } catch (RequestException $e) {
            watchdog_exception('api_rest', $e->getMessage());
        }
        //kint($items);
        return [
            '#theme' => 'list_items',
            '#items' => $items,
        ];

    }


}

