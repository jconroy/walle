# Playground

Personal repo for testing out [Laravel Lumen](https://lumen.laravel.com/). If you've stumbled upon this randomly there really won't be anything of interest here.

## Prerequisites

This is basically just how I have my local environment set up.

* Homebrew
* PHP7 (Installed using Homebrew)
* Redis (Installed using Homebrew - see below)
* Composer (Installed using Homebrew)

### Valet

I'm using [Laravel Valet](https://laravel.com/docs/5.3/valet) for a quick local dev environment that supports Lumen.

Installed following instructions in the Valet documentation.

I have `~/Sites` parked for my local sites and am using `.localhost` as my TLD.

### Redis

It's possible we'll be using [Redis](http://redis.io/) and something like [AWS Elasticache](https://aws.amazon.com/elasticache/)in a production environment as a data store for things like queues, sessions or caches - mainly because of the scalability. So I've had a play with it here. I'm using it for storing Queued jobs.

Redis can be installed using Homebrew

> brew install redis

Once installed I simply following the onscreen commands output by homebrew to run as a service/on startup.

To check if redis is running you can use the following command:

> redis-cli ping

You should receive a “PONG” back,

To see what redis is doing you can using the monitor argument:

> redis-cli monitor

Although this is not very human friendly.

## Setup

*. Make sure Valet and Redis are working and Redis is running.
*. Clone the repo into a directory within `~/Sites` I used "walle". The directory name correlates with the URL used to access the site/api e.g. `http://walle.localhost`
*. CD to the directory e.g. `cd ~/Sites/walle`
*. [Secure](https://laravel.com/docs/5.3/valet#securing-sites) the site e.g. `valet secure walle` - this will serve the site from `https://walle.localhost`
*. Install the project dependencies using composer e.g. `composer install` - this will take a few minutes
*. Create a`.env` file within the project root with the following contents:

```
QUEUE_DRIVER=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

* Open a new terminal window, from the project root the following command to start a daemon which processes jobs in the queue

> php artisan queue:work --daemon

## Usage

If everything is installed correctly opening [https://walle.localhost](https://walle.localhost) within you browser should simply display [https://cl.ly/0B3n310q370J](https://cl.ly/0B3n310q370J)

### Running a "Test"

Navigating to [https://walle.localhost/run](https://walle.localhost/run) will use php-webdriver to [scrape](https://cl.ly/412D3v2E0M1T) the title from the Selenium website and create a screenshot at `/tmp/screen.png`.

You'll notice that there is a delay/nothing else happens whilst the scraping taking place - this is because the command is blocking other processing from taking place.

### Queuing "Tests"

Navigating to [https://walle.localhost/queue](https://walle.localhost/queue) will do the same as `/run` except it adds a job to a queue to be processed in the background.

The page will finish loading nearly instantly and you'll see the test "[ExampleJob Queued...](https://cl.ly/0S0U0L17222w)" on the screen.

At this point the scraping will be occurring behind the scenes. If you view your terminal window where you ran the queue daemon you'll see the job has been run in the [background](https://cl.ly/1W281s000D3F).

Refreshing/reloading [https://walle.localhost/queue](https://walle.localhost/queue) multiple times will add multiple jobs to the queue and they will process safely in the background - eventually being [displayed](https://cl.ly/0a2w1E2g2S2Q).

### Scheduling

To make use of laravel/lumen scheduling system, add a single local cron job to do the following:

> * * * * * php /Users/jconroy/Sites/walle/artisan schedule:run >> /dev/null 2>&1

You can use:

> crontab -e

Be sure to adjust the path to suit your needs. The above basically ensures the laravel/lumen "scheduling" is triggered every minute.

Instead of setting up individual cron jobs we can now schedule tasks to occur at [different schedules](https://laravel.com/docs/5.3/scheduling) within the `App\Console\Kernel` class and Lumen will take care of the rest.

The above is great for basic stuff but to schedule individual jobs to happen at all different times (like the subscriptions action scheduler) we'll need to super charge things a little with something _like_:

* Create a scheduled task/command within the project to run every X minutes which checks tests stored in a database (i.e. the "scheduled" events)
* If the tests are due to based on a stored time in a database "queue" them.
* When the test is complete, mark the test as complete in the db etc..

