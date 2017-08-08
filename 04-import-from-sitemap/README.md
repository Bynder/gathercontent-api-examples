# Import existing text content from the web into GatherContent

## Description

This is an example interactive command line application, which takes a url to a `sitemap.xml` file, creates a list of web pages on that website, then parses the text from a chosen CSS selector on each web page and creates a corresponding item in GatherContent.

## Requirements

* PHP 5.5+
* Composer (PHP package management tool)

## Installation

Clone this package from GitHub

```bash
$ git clone https://github.com/gathercontent/api-examples.git
```

Move into this project directory

```bash
$ cd 04-import-from-sitemap
```

Install the package dependencies 

```bash
$ composer install
```

## How to use the application

Execute the application with

```bash
$ bin/scrape
```

You will be prompted for a username and API key (found in your personal settings).

If you are a member of multiple accounts on GatherContent you will be asked to choose which account you are importing to.

You can then choose to create a new project, or import content into an existing project.

Give the application a link to your sitemap.xml file, e.g. http://yourwebsite.com/sitemap.xml

You should see a number of pages that will be scraped.

Next choose a CSS selector - e.g. 'article' or css class '.content', '#main-content' to denote which block of content you want imported.

Then confirm and watch the content be pulled and posted back to GatherContent through the public API.

## FAQ

### Why are HTML tags not imported?

If you look at [line 14 of ContentScraper](https://github.com/gathercontent/api-examples/blob/82dda9600e74ab549c5923838d2fca0f39bc74c1/04-import-from-sitemap/src/ContentScraper.php#L14) you will see we are only scraping the _text_ of the element, not the _HTML_. You can change this to `return $filtered->html();` instead if you wish to import HTML. 
Note that some uncommon HTML tags may still not be available, and you will have to contact support@gathercontent.com to add these to your project.

### How can I avoid all my content being imported as one blob?

The `ImportCommand` is configured to only import two fields - the page `<title>` and a dynamically scraped element from the [$cssSelector submitted by the user](https://github.com/gathercontent/api-examples/blob/82dda9600e74ab549c5923838d2fca0f39bc74c1/04-import-from-sitemap/src/ImportCommand.php#L104) as you can [see here](https://github.com/gathercontent/api-examples/blob/82dda9600e74ab549c5923838d2fca0f39bc74c1/04-import-from-sitemap/src/ImportCommand.php#L118).

If you have a consistent template that you are importing from, you could edit this file and change the array. For example, say you had a `<title>`, `<h1>`, `<div id="main">`, and `<p class="call-to-action">` on every field that you wanted to import - you can just override the second argument given to `ContentToFieldMaper->mapContentToFields()` like so:

```
$map = [
  'Page Title'     => 'title',
  'Heading'        => 'h1',
  'Main Content'   => 'div#main',
  'Call to Action' => 'p.call-to-action'
];

$contentToFieldMapper->mapToFields($html, $map);
```

## Running tests

To run the phpspec test suite run

```bash
$ vendor/bin/phpspec run
```

