# ImguBox - Store Imgur favorites in Dropbox

[![Build Status](https://travis-ci.org/stefanzweifel/imgubox.svg?branch=master)](https://travis-ci.org/stefanzweifel/imgubox)
[![Code Climate](https://codeclimate.com/github/stefanzweifel/imgubox/badges/gpa.svg)](https://codeclimate.com/github/stefanzweifel/imgubox)
[![Test Coverage](https://codeclimate.com/github/stefanzweifel/imgubox/badges/coverage.svg)](https://codeclimate.com/github/stefanzweifel/imgubox/coverage)

—
# This project is no longer maintained! Please read [this]() post for more information.
—

> Found a funny cat GIF or an awesome wallpaper album? ImguBox stores those files within your Dropbox automatically.

Read more about this project on [my blog](https://stefanzweifel.io/projects/imgubox/).

## Selfhosting

This application was built with the PHP Framework Laravel 5.2. To run imgubox on your own server you need at least PHP 5.5!

To run the application you need the following:

- PHP
- A database Server (mysql, sqlite, …)
- A registered [Imgur Application](https://api.imgur.com/oauth2/addclient)
- A registered [Dropbox Application](https://www.dropbox.com/developers)

### Installation

```
git clone https://github.com/stefanzweifel/imgubox.git && cd imgubox
cp .env.example .env
composer install
php artisan key:generate
```


### Configuration

After you’ve installed the PHP dependencies you have to update the `.env` file to your needs: 

- Add Database Credentials, 
- Add Client ID, Secret Key, Redirect URL for Imgur and Dropbox

You then need to migrate and seed your database. Run the following on your server:

```shell
php artisan migrate --seed
```

The Application should be ready to go. Open the site in your browser of choice and create an account. You should then be able to connect with your Imgur and Dropbox Account.

### Running the Application

The core of imgubox is a scheduled [command](https://github.com/stefanzweifel/imgubox/blob/master/app/Console/Commands/FetchUserFavs.php) which runs every 30 minutes. The command will then dispatches a [job](https://github.com/stefanzweifel/imgubox/blob/master/app/Jobs/FetchImages.php) to the queue for every user with an active Imgur and Dropbox token. The Job gets the latest favorited images of the given user and dispatches another [job](https://github.com/stefanzweifel/imgubox/blob/master/app/Jobs/StoreImgurImages.php) which will then store the passed Imgur Image in Dropbox.

Add the following line to your `crontab`. The `schedule:run`  runs Laravel’s internal Scheduling class. Read more about it [here](https://laravel.com/docs/5.2/scheduling#defining-schedules).

```shell
* * * * * php /home/imgubox/artisan schedule:run 1>> /dev/null 2>&1
```

As described above, Laravel pushed Jobs onto the queue you have configured in the beginning. The Queue can be consumed by the `queue:work` command. You can add the following line to your `supervisord` configuration. Replace `$YOUR_QUEUE_CONNECTION` with whatever you have configured in [this file](https://github.com/stefanzweifel/imgubox/blob/master/config/queue.php#L19).

```shell
php /home/imgubox/artisan queue:work $YOUR_QUEUE_CONNECTION --queue=high,low --daemon  2>&1
```