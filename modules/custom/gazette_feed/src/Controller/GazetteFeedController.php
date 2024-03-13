<?php

namespace Drupal\gazette_feed\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Http\ClientFactory;
use GuzzleHttp\Exception\RequestException;

class GazetteFeedController extends ControllerBase {

  // Constructor if needed
  public function __construct() {
    // Constructor logic here if needed
  }

  public function content() {
    // Your controller logic here
    return [
      '#markup' => $this->t('Hello from GazetteFeedController!'),
    ];
  }

  public function contentJson() {
    // Your controller logic here

    $endpoint = 'https://www.thegazette.co.uk/all-notices/notice/data.json';
    $page = 1; // Initial page number
    $pageSize = 10; // Number of items per page


    $responseData = self::fetch_data_from_gazette_api($endpoint, $page, $pageSize);
    
    if ($responseData) {
        echo '<pre>'; print_r($responseData);

        return [
          '#markup' => $this->t('Hello from GazetteFeedController!', ['response' => $responseData]),
        ];
    }
    else {
        // Handle the case where fetching data failed.
        echo "Failed to fetch data from The Gazette API.";
    }
  }

  function fetch_data_from_gazette_api($endpoint, $page, $pageSize) {
    // Initialize variables.
    $data = FALSE;
  
    // Get the HTTP client service.
    $client = \Drupal::service('http_client');
  
    try {
      // Make a GET request to the API endpoint.
      $response = $client->get($endpoint, [ 'query' => [ 'page' => $page, 'per_page' => $pageSize ] ]);
  
      // Check if the request was successful (status code 200).
      if ($response->getStatusCode() === 200) {
        // Decode the JSON response.
        $data = json_decode($response->getBody(), TRUE);
      }
      else {
        // Log an error if the request was not successful.
        \Drupal::logger('custom_module')->error('Failed to fetch data from The Gazette API. Status code: @code', ['@code' => $response->getStatusCode()]);
      }
    }
    catch (RequestException $e) {
      // Log any request exceptions.
      \Drupal::logger('custom_module')->error('An error occurred while fetching data from The Gazette API: @message', ['@message' => $e->getMessage()]);
    }
  
    return $data;
  }
}
