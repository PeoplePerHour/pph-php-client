pph-php-client
==============

A Guzzle client to connect to the PeoplePerHour.com API.

Installation Using Composer
===========================

The client has not been added to packagist but can be included in your project using [Composer](http://getcomposer.org/) if you app the github repo.

1. First you will need to add "peopleperhour/pph-php-client" as a dependency in your composer.json file:

    ```json
    {
        "repositories": [
            {
                "type":"vcs",
                "url":"git@github.com:PeoplePerHour/pph-php-client.git"
            }
        ],
        "require": {
          "peopleperhour/pph-php-client": "dev-master"
        }
    }
    ```

2. If you don't already have composer installed, download and install it:

    ```Batchfile
    curl -sS https://getcomposer.org/installer | php
    ```

3. Install dependancies

    ```Batchfile
    php composer.phar update
    ```

4. Lastly, you need to include the Composer autoloader in your bootstrap:

    ```php
    require '[/path/to/vendor]/autoload.php';
    ```

Getting Started
===============

To begin developing with our PHP client, you will need your own application ID and secret key.


Initializing the Client
-----------------------

To get started using the client, instantiate a new instance of the PPHApi client class.
This is where you will need to pass in the Application ID and Secret Key, as well as the developer environment you are using (Sandbox, or Live).

```php
require '[path/to/vendor]/autoload.php';

$appId = "{APPID}";
$secretKey = "{SecretKey}";

$client = new PPHApi($appId, $secretKey);
// ...
```

Fetching Data
-------------

```php
// Fetch details for a particular user ID
$response = $client->user(['id'=>12345]);

// Fetch details for a particular user ID but only return their name. (a is for attributes wanted)
$response = $client->user(['id'=>12345, 'a'=>'fname,lname']);

// Fetch a page of users
$response = $client->userList();

// Fetch the 4th page of users sorted by latest registration date
$response = $client->userList(['page'=>4,'sort'=>'latest']);
```

User Login
----------

Cookies are enabled by default in the client so the session is maintained after a login. Example:

```php
// should show false
print_r($client->isGuest());

// login
$post_data = array(
  'email'    => 'your-pph-email@example.com',
  'password' => '<your-password>',
);
$response = $client->UserLogin($post_data);

// should now show true
print_r($client->isGuest());
```

Miscellaneous
-------------

```php
// If you want to get the guzzle http client and change a option, this is how:
$httpClient = $client->getHttpClient();
$httpClient->setDefaultOption('cookies', false);
```

```php
// If you want to view the URL being used for the API call
$command = $client->getCommand('hourlieList', array('f[max_price]'=>100,'a'=>'id,price'));
$request = GuzzleHttp\Command\Command::createRequest($client->getHttpClient(), $command);
print_r($request->getUrl());
```


Running the unit tests
======================

 1. Go to the folder where pph-php-client is checked out.
 1. Install phpunit: `composer update --dev`
 1. Trigger the tests: `php vendor/phpunit/phpunit/phpunit.php tests/`
