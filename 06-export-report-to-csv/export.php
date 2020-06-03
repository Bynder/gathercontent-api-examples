<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use League\Csv\Writer;

$username = getenv('GC_USER_NAME');
$apikey = getenv('GC_API_KEY');

$projectId = getenv('GC_PROJECT_ID');

// Setup API client
$gc = new Client([
    'base_uri' => 'https://api.gathercontent.com',
    'headers' => [
        'Accept' => 'application/vnd.gathercontent.v2+json'
    ],
    'auth' => [
        $username,
        $apikey
    ]
]);

// Create the CSV File
touch('items.csv');
$csv = Writer::createFromPath('items.csv');

// Insert header row
$csv->insertOne(['Title', 'Assignee(s)', 'Template', 'Folder', 'Workflow Status', 'Due Date']);

function insertItem($item): void {
    global $csv;
    $csv->insertOne([$item->name, join(",\n", $item->assignee_full_names), $item->template_name, $item->folder_name, $item->status_name, $item->next_due_at]);
}

// @link https://docs.gathercontent.com/reference#listitems
$listItemsPage1 = $gc->get("projects/{$projectId}/items?include=template_name,assignee_full_names,folder_name,status_name,item_url");

$response = json_decode($listItemsPage1->getBody());
$totalPages = $response->pagination->total_pages;

array_walk($response->data, 'insertItem');

if ($totalPages > 1) {
    foreach (range(2, $totalPages) as $page) {
        $response = $gc->get("projects/{$projectId}/items?include=template_name,assignee_full_names,folder_name,status_name,item_url&page={$page}");
        $response = json_decode($response->getBody());
        array_walk($response->data, 'insertItem');
    }
}
