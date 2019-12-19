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
    public function items($page)
    {
        $client = \Drupal::httpClient();
        $url = 'http://api.example.com/posts/';
        $items = [];
        try {
            $response = $client->get($url. '?page=' . $page);
            $data = $response->getBody();
            $response = (object)json_decode($data, true);

            foreach ($response->data as $dat) {
                if (!$dat['logo']) {
                    $dat['logo'] = file_create_url(drupal_get_path('module', 'api_rest') . '/assets/img/logo.png');
                }
                $items[] = [
                    'id' => $dat['id'],
                    'title' => $dat['raison_soc'],
                    'logo' => $dat['logo']
                ];

                $first_page_url = $response->links['first'];
                $last_page_url = $response->links['last'];
                $current_page = $response->meta['current_page'];
                $last_page = $response->meta['last_page'];
                $per_page = $response->meta['per_page'];
                $total = $response->meta['total'];
                $pages_number = intdiv($total, $per_page) + 1;
            }
        } catch (RequestException $e) {
            watchdog_exception('api_rest', $e->getMessage());
        }
             //kint($pages_number);
        return [
            '#theme' => 'list_items',
            '#items' => $items,
            "#first_page_url" => $first_page_url,
            "#last_page_url" => $last_page_url,
            "#current_page" => $current_page,
            "#per_page" => $per_page,
            "#last_page" => $last_page,
            "#total" => $total,
            "#pages_number" => $pages_number,
        ];

    }


}

