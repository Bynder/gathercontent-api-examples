# Exporting a report of project items

To run this you will need a Gather Content account, and to [generate an API key](https://help.gathercontent.com/en/articles/369871-generating-an-api-key-the-api-documentation).

This example also assumes you have PHP and [Composer](https://getcomposer.org/) dependency manager installed.

I've set my username (email), api key and project id in my environment, but you may edit the export.php file and put your
details in there if you prefer.

First install any composer dependencies by running `composer install`.
Then run the export script:
```bash
GC_API_KEY=<Your API key goes here> \
GC_USER_NAME=<your email address> \
GC_PROJECT_ID=<project id> \
php export.php
```

You should see a file, `items.csv` once finished.