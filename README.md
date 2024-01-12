<h1 align="center">
    <br>
    <a href="https://easyappointments.org">
        <img src="https://raw.githubusercontent.com/alextselegidis/phingle/main/logo.png" alt="Phingle" width="150">
    </a>
    <br>
    Phingle
    <br>
</h1>

<br>

<h4 align="center">
    Single File App Template
</h4>

<p align="center">
  <a href="#about">About</a> •
  <a href="#features">Structure</a> •
  <a href="#setup">Develop</a> •
  <a href="#license">License</a>
</p>

## About

**Phingle** is a simple yet practical single file app template file, that will allow to quickly implement utility 
scripts for your server. It was created as a started point for very basic server side operations both locally or on 
remote servers, as it aims to provide lightweight routing functionality and CDN based styling with Bootstrap. 
**Attention: this is just a file template and no framework by any means.** You can copy it and create your own single
(or even multiple) file scripts with PHP. 

## Features

The file is designed to be as lightweight and easy to work with as possible. 

* Application Class (Common Logic)
* Simple Routing
* Bootstrap Styling (CDN)
* Single File App

## Develop

By default, Phingle has no 3rd party dependencies (although you could add some if needed) and you can directly start by 
cloning this repository and copying the `phingle.php` file template, before you start to add your own custom 
functionality.

```php
$app->route('default', function () {
	$this->render('<h1>Hello World</h1>');
});
```

### Routing

Towards the bottom of the file, you will find a section where you can add your own custom callbacks and render HTML or 
process form submissions. For a request to route to your custom callback, you just need to provide the `action` 
parameter, either with a `GET` or `POST` request. 

```php
$app->route('default', function () {
	$content = <<<HTML
        <div>
            <h1>Default Page</h1>
            
            <a href="?action=second-page" class="btn btn-primary">
                Go To Second Page
            </a>
        </div>
HTML;

	$this->render($content);
});

$app->route('second-page', function () {
	$content = <<<HTML
        <div>
            <h1>Second Page</h1>
            
            <a href="?action=default" class="btn btn-primary">
                Go To Default Page
            </a>
        </div>
HTML;

	$this->render($content);
});
```

### Form Submission

In the same way, you can include forms in your HTML mark up and add some server side request handling for them, by 
utilizing the `action` parameter.

```php
$app->route('default', function () {
	$content = <<<HTML
        <div>
            <h1>Default Page</h1>
            
            <form action="?action=submission-callback" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Submit    
                </button>
            </form>
        </div>
HTML;

	$this->render($content);
});

$app->route('submission-callback', function () {
    $name = $_POST['name'] ?? null;
    
    if ( ! $name) {
        die('No name POST parameter was provided.'); 
    }

	$content = <<<HTML
        <div>
            <h1>Hello {$name}!</h1>
            
            <a href="?action=default" class="btn btn-primary">
                Go To Default Page
            </a>
        </div>
HTML;

	$this->render($content);
});
```

### Enable Authentication

You can easily protect the script with the built-in HTTP Basic Authentication. 

At the top of your Phingle file, you will find the following section: 

```php
const AUTH_USERNAME = 'administrator';

const AUTH_PASSWORD = ''; // Set a password to enable HTTP Basic Auth.
```

Change the username and set a password accordingly, so that the file requires the credentials before being executed on 
the server.


## License

Code Licensed Under [GPL v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html) | Content Under [CC BY 3.0](https://creativecommons.org/licenses/by/3.0/)

---

Website [alextselegidis.com](https://alextselegidis.com) &nbsp;&middot;&nbsp;
GitHub [alextselegidis](https://github.com/alextselegidis) &nbsp;&middot;&nbsp;
Twitter [@alextselegidis](https://twitter.com/AlexTselegidis)

###### More Projects On Github
###### ⇾ [Plainpad &middot; Self Hosted Note Taking App](https://github.com/alextselegidis/plainpad)
###### ⇾ [Questionful &middot; Web Questionnaires Made Easy](https://github.com/alextselegidis/questionful)
###### ⇾ [Integravy &middot; Service Orchestration At Your Fingertips](https://github.com/alextselegidis/integravy)
