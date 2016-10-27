<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use League\Csv\Writer;
use function GuzzleHttp\Promise\settle;

$username = 'your email address here';
$apikey = 'your api key here';
$projectId = 12345;

// Setup API client
$client = new Client([
    'base_uri' => 'https://api.gathercontent.com',
    'headers' => [
        'Accept' => 'application/vnd.gathercontent.v0.5+json'
    ],
    'auth' => [
        $username,
        $apikey
    ]
]);

// @link https://gathercontent.com/developers/items/get-items/
$itemListResponse = $client->get('/items', [
    'query' => [
        'project_id' => $projectId
    ]
]);

$items = json_decode($itemListResponse->getBody(), true)['data'];


/*
 * Asynchronously fetch each Item
 */
$promises = array_map(function ($item) use ($client) {
    return $client->getAsync('/items/' . $item['id']);
}, $items);

$promiseResponses = settle($promises)->wait();

$encodedItemResponses = array_column($promiseResponses, 'value');

$itemConfigs = array_map(function ($response) {
    return json_decode($response->getBody(), true)['data']['config'];
}, $encodedItemResponses);

$headings = array_column($itemConfigs[0][0]['elements'], 'label');

/*
 * Extract the 'value' from each of the Item elements
 */
$content = array_map(function ($itemConfig) {
    return array_column($itemConfig[0]['elements'], 'value');
}, $itemConfigs);

/*
 * Create the CSV File
 */

touch('content.csv');

$csv = Writer::createFromPath('content.csv');

$csv->insertOne($headings);

array_walk($content, function ($item) use ($csv) {
    $csv->insertOne($item);
});
