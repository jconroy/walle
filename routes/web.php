<?php

use App\Jobs\ExampleJob;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

/**
 * Display Text
 *
 * Browse to: https://walle.localhost/
 * See: WALL-E
 */
$app->get('/', function () use ($app) {
    return 'WALL-E';
});

/**
 * Run a test - blocking
 *
 * Browse to: https://walle.localhost/run
 * See: The title is 'Selenium - Web Browser Automation'
 * See: Screenshot "screen.png" located at '/tmp/screen.png'
 */
$app->get('run', function () use ($app) {

	// test AWS t2.micro instance running phantomjs 2.1.1
	$host = '52.53.195.73:4444';

	$capabilities = array(
	    WebDriverCapabilityType::BROWSER_NAME => 'phantomjs',
	    'phantomjs.page.settings.userAgent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:25.0) Gecko/20100101 Firefox/25.0',
	);

	// start phantomjs with 5 second timeout
	$driver = RemoteWebDriver::create($host, $capabilities, 5000);
	// navigate to 'http://docs.seleniumhq.org/'
	$driver->get('http://docs.seleniumhq.org/');
	// click the link 'About'
	$link = $driver->findElement(
	    WebDriverBy::id('menu_about')
	);
	$link->click();
	// print the title of the current page
	echo "The title is '" . $driver->getTitle() . "'\n";
	// print the URI of the current page
	//echo "The current URI is '" . $driver->getCurrentURL() . "'\n";
	// Search 'php' in the search box
	$input = $driver->findElement(
	    WebDriverBy::id('q')
	);
	$input->sendKeys('php')->submit();
	// wait at most 10 seconds until at least one result is shown
	$driver->wait(10)->until(
	    WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
	        WebDriverBy::className('gsc-result')
	    )
	);
	// print the URI of the current page
	//echo "The current URI is '" . $driver->getCurrentURL() . "'\n";

	$driver->takeScreenshot('/tmp/screen.png');
	// close the Firefox
	$driver->quit();

});

/**
 * Run a test - queued - non blocking
 *
 * Browse to: https://walle.localhost/queue
 * See: 'ExampleJob Queued...'
 * See: In listener it will displayed the "The title is 'Selenium - Web Browser Automation'" and [2016-11-16 07:51:14] Processed: App\Jobs\ExampleJob
 * See: Screenshot screen_<timestamp>.png located in '/tmp/screen_<timestamp>.png' e.g. screen_1479282672.png
 *
 * Note running multiple times will just queue the tests up and they will run in the background
 */
$app->get('queue', function () use ($app) {
	dispatch(new ExampleJob);
    return 'ExampleJob Queued...';
});


