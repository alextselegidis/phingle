<?php

/* ----------------------------------------------------------------------------
 * Phingle - Single File App Template
 *
 * @package     Phingle
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/license/gpl-3-0 - GPLv3
 * @link        https://alextselegidis.com
 * @version     1.0.0
 * ---------------------------------------------------------------------------- */

error_reporting(E_ALL);

ini_set('display_errors', TRUE);

ini_set('display_startup_errors', TRUE);

/*
|--------------------------------------------------------------------------
| Configuration
|--------------------------------------------------------------------------
|
| Describe the functionality and purpose of single file tool. This will be
| the first thing developers will see.
|
*/

const SCRIPT_NAME = 'Scriptname';

const AUTH_USERNAME = 'administrator';

const AUTH_PASSWORD = ''; // Set a password to enable HTTP Basic Auth.

/*
|--------------------------------------------------------------------------
| Disable CLI
|--------------------------------------------------------------------------
|
| This PHP template will not work with the CLI, so return a friendly message
| to the user.
|
*/

if (PHP_SAPI === 'cli')
{
	die('This file does not support CLI requests.');
}

/*
|--------------------------------------------------------------------------
| Phingle
|--------------------------------------------------------------------------
|
| The following class contains the required features the script needs
| in order to perform core operations, such as render a template, process
| a request etc.
|
*/

class Phingle {
	/**
	 * @var array
	 */
	private $routes = [];

	/**
	 * @var array
	 */
	private $messages = [];

	/**
	 * Route an action callback.
	 *
	 * @param string $action
	 * @param callable $callback
	 */
	public function route(string $action, callable $callback)
	{
		$this->routes[$action] = $callback;
	}

	/**
	 * Get the current action (returns 'default' if non specified).
	 *
	 * @return string
	 */
	public function action(): string
	{
		return $_REQUEST['action'] ?? 'default';
	}

	/**
	 * Make an HTTP request.
	 *
	 * @param string $method
	 * @param string $url
	 * @param array $headers
	 * @param string $body
	 *
	 * @return string
	 */
	public function request(string $method, string $url, array $headers = [], string $body = ''): string
	{
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_SSL_VERIFYHOST => FALSE,
			CURLOPT_CUSTOMREQUEST => strtoupper($method),
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => $body
		]);

		$response = curl_exec($curl);

		if ( ! $response)
		{
			die('Curl error: ' . curl_error($curl));
		}

		curl_close($curl);

		return $response;
	}

	/**
	 * Queue a message for display in the page render (optional).
	 *
	 * @param string $content
	 * @param string $type Provide one of 'info', 'success', 'warn', 'danger'.
	 */
	public function message(string $content, string $type = 'info')
	{
		if (empty($this->messages[$type]))
		{
			$this->messages[$type] = [];
		}

		$this->messages[$type][] = $content;
	}

	/**
	 * Get the base URL.
	 *
	 * @param string $segment
	 *
	 * @return string
	 */
	public function baseUrl(string $segment = ''): string
	{
		$protocol =
			(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
			|| (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
			|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
				? 'https://' : 'http://';

		$domain = $_SERVER['HTTP_HOST'] ?? 'localhost';

		$requestUri = dirname($_SERVER['SCRIPT_NAME']);

		return rtrim($protocol . $domain . $requestUri, '/') . '/' . $segment;
	}

	/**
	 * Get the script URL.
	 *
	 * @param string $segment
	 *
	 * @return string
	 */
	public function scriptUrl(string $segment = ''): string
	{
		$file = basename(__FILE__);

		return $this->baseUrl($file . $segment);
	}

	/**
	 * Render the content on the screen (may optionally use a layout too).
	 *
	 * @param string $content
	 */
	public function render(string $content)
	{
		$year = date('Y');

		$scriptName = SCRIPT_NAME;

		echo <<<HTML
            <!doctype html>
            <html lang="en">
            <head>
                <meta charset=utf-8>
                <title>{$scriptName}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body>
                <div class="container py-4">
                    <header class="pb-3 mb-4 border-bottom">
                        <a href="{$this->scriptUrl()}" class="d-flex align-items-center text-dark text-decoration-none">
                            <spanre class="fs-4">{$scriptName}</spanre>
                        </a>
                    </header>
                    
                    {$content}
                    
                    <footer class="pt-3 mt-4 text-muted border-top d-flex justify-content-between align-items-center">
                    	<div>
							<a href="https://alextselegidis.com" target="_blank">Alex Tselegidis</a>
							© {$year}
                        </div>
                        
                        <div>
                        	Based On
                        	<a href="https://github.com/alextselegidis/phingle" target="_blank">
                        		Phingle
                        	</a>
						</div>
                    </footer>
                </div>
            </body>
            </html>
HTML;
	}

	/**
	 * Authorize the current request.
	 */
	public function auth()
	{
		if (empty(AUTH_USERNAME) || empty(AUTH_PASSWORD))
		{
			return;
		}

		if (
			! isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])
			|| (
				$_SERVER['PHP_AUTH_USER'] !== AUTH_USERNAME
				|| $_SERVER['PHP_AUTH_PW'] !== AUTH_PASSWORD
			)
		)
		{
			header('WWW-Authenticate: Basic realm="My Realm"');
			header('HTTP/1.0 401 Unauthorized');
			die('Permission denied');
		}
	}

	/**
	 * Run the application.
	 */
	public function run()
	{
		$this->auth();

		if (empty($this->routes))
		{
			die('No routes defined.');
		}

		$action = $this->action();

		if ( ! array_key_exists($action, $this->routes))
		{
			die('The requested action has no callback routed: ' . $action);
		}

		$callback = Closure::fromCallable($this->routes[$action]);

		$callback->call($this);
	}
}

/*
|--------------------------------------------------------------------------
| Be Creative
|--------------------------------------------------------------------------
|
| Define your own functions, callbacks and logic for this single file app.
|
*/

$app = new Phingle;

$app->route('default', function () {
	$content = <<<HTML
        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Welcome!</h1>
                
                <p class="col-md-8 fs-4">
                    Using this single file template you can create your own scripts for any use case.     
                </p>
                
                <a href="https://github.com/alextselegidis/phingle" class="btn btn-primary btn-lg">
                    Find out more
                </a>
            </div>
        </div>
HTML;

	$this->render($content);
});

// Add your custom routes here ...

$app->run();
