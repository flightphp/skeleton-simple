<?php

use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
use Tracy\Debugger;

/*
 * FlightPHP Framework Skeleton - Simple Implementation
 * 
 * This implementation will be everything in a simple file. Most of what you'll do is probably
 * calling functions instead of controllers.
 * 
 * @copyright   Copyright (c) 2024, Mike Cao <mike@mikecao.com>, n0nag0n <n0nag0n@sky-9.com>
 * @license     MIT, http://flightphp.com/license
                                                                  .____   __ _
     __o__   _______ _ _  _                                     /     /
     \    ~\                                                  /      /
       \     '\                                         ..../      .'
        . ' ' . ~\                                      ' /       /
       .  _    .  ~ \  .+~\~ ~ ' ' " " ' ' ~ - - - - - -''_      /
      .  <#  .  - - -/' . ' \  __                          '~ - \
       .. -           ~-.._ / |__|  ( )  ( )  ( )  0  o    _ _    ~ .
     .-'                                               .- ~    '-.    -.
    <                      . ~ ' ' .             . - ~             ~ -.__~_. _ _
      ~- .       N121PP  .          . . . . ,- ~
            ' ~ - - - - =.   <#>    .         \.._
                        .     ~      ____ _ .. ..  .- .
                         .         '        ~ -.        ~ -.
                           ' . . '               ~ - .       ~-.
                                                       ~ - .      ~ .
                                                              ~ -...0..~. ____
   Cessna 402  (Wings)
   by Dick Williams, rjw1@tyrell.net
*/
$ds = DIRECTORY_SEPARATOR;

/*
 * This file is the equivalent of a typical bootstrap file. A bootstrap files 
 * job is to make sure that all the required services, plugins, connections, etc. 
 * are loaded and ready to go for every request made to the application.
 */

 // First autoload composer
require(__DIR__ . $ds . '..' . $ds . 'vendor' . $ds . 'autoload.php');
// additionally if you download this and the Flight lib as a zip file and not a composer project, you could
// comment the require above, and uncomment the line below and correct the path to your lib.
// require(__DIR__ . $ds . '..' . $ds . 'path/to/flight/autoload.php');

/*
 * Config
 */
$config_file_path = __DIR__. $ds . '..' . $ds . 'app/config/config.php';
if(file_exists($config_file_path) === false) {
	Flight::halt(500, 'Config file not found. Please create a config.php file in the app/config directory to get started.');
}
$config = require($config_file_path);

/*
 * Services
 * 
 * A "service" is basically something special that you want to use in your app.
 * For instance, need a database connection? You can set up a database service.
 * Need caching? You can setup a Redis service
 * Need to send email? You can setup a mailgun/sendgrid/whatever service to send emails.
 * Need to send SMS? You can setup a Twilio service.
 * 
 * All the services and how they are configured are setup in the services file.
 * In many cases, services are all attached to something called a "services container"
 * or more simply, a "container". The container manages if you should share the same
 * service, or if you should create a new instance of the service every time you need it.
 * That's a discussion for another day. Suffice to say, that Flight has a basic concept
 * of a services container by registering classes to the Engine/Flight class.
 */

// uncomment the following line for MySQL
// $dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';

// uncomment the following line for SQLite
// $dsn = sqlite:' . $config['database']['file_path'];

// Uncomment the following lines for a Flight::db() service
// In development, you'll want the class that captures the queries for you. In production, not so much.
// $pdoClass = Debugger::$showBar === true ? PdoQueryCapture::class : PdoWrapper::class;
// $app->register('db', $pdoClass, [ $dsn, $config['database']['user'] ?? null, $config['database']['password'] ?? null ]);

/*
 * A route is really just a URL, but saying route makes you sound cooler.
 * When someone hits that URL, you point them to a function or method 
 * that will handle the request.
 * 
 * The below are just examples you can use to understand how it works. Feel free to change it up.
 */
Flight::route('GET /', function() {
	echo '<h1>Welcome to the Flight Simple Example!</h1><h2>You are gonna do great things!</h2>';
});

Flight::route('GET /hello-world/@name', function($name) {
	echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
});

Flight::group('/api', function() {
	Flight::route('GET /users', function() {
		// You could actually pull data from the database if you had one set up
		// $users = Flight::db()->fetchAll("SELECT * FROM users");
		$users = [
			[ 'id' => 1, 'name' => 'Bob Jones', 'email' => 'bob@example.com' ],
			[ 'id' => 2, 'name' => 'Bob Smith', 'email' => 'bsmith@example.com' ],
			[ 'id' => 3, 'name' => 'Suzy Johnson', 'email' => 'suzy@example.com' ],
		];

		// You actually could overwrite the json() method if you just wanted to
		// to Flight::json($users); and it would auto set pretty print for you.
		// https://flightphp.com/learn#overriding
		Flight::json($users, 200, true, 'utf-8', JSON_PRETTY_PRINT);
	});
	Flight::route('GET /users/@id:[0-9]', function($id) {
		// You could actually pull data from the database if you had one set up
		// $user = Flight::db()->fetchRow("SELECT * FROM users WHERE id = ?", [ $id ]);
		$users = [
			[ 'id' => 1, 'name' => 'Bob Jones', 'email' => 'bob@example.com' ],
			[ 'id' => 2, 'name' => 'Bob Smith', 'email' => 'bsmith@example.com' ],
			[ 'id' => 3, 'name' => 'Suzy Johnson', 'email' => 'suzy@example.com' ],
		];

		$users_filtered = array_filter($users, function($data) use ($id) {
			return $data['id'] === (int) $id;
		});
		if($users_filtered) {
			$user = array_pop($users_filtered);
		}

		Flight::json($user, 200, true, 'utf-8', JSON_PRETTY_PRINT);
	});
	Flight::route('POST /users/@id:[0-9]', function($id) {
		// You could actually update data from the database if you had one set up
		// $statement = Flight::db()->runQuery("UPDATE users SET email = ? WHERE id = ?", [ Flight::data['email'], $id ]);
		Flight::json([ 'success' => true, 'id' => $id ], 200, true, 'utf-8', JSON_PRETTY_PRINT);
	});
});

// At this point, your app should have all the instructions it needs and it'll
// "start" processing everything. This is where the magic happens.
Flight::start();
/*
 .----..---.  .--.  .----.  .---.     .---. .-. .-.  .--.  .---.    .----. .-. .-..----. .----..-.  .-.
{ {__ {_   _}/ {} \ | {}  }{_   _}   {_   _}| {_} | / {} \{_   _}   | {}  }| { } || {}  }| {}  }\ \/ / 
.-._} } | | /  /\  \| .-. \  | |       | |  | { } |/  /\  \ | |     | .--' | {_} || .--' | .--'  }  {  
`----'  `-' `-'  `-'`-' `-'  `-'       `-'  `-' `-'`-'  `-' `-'     `-'    `-----'`-'    `-'     `--'  
*/
