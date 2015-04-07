# Flash [![](https://img.shields.io/travis/znck/flash.svg)](https://travis-ci.org/znck/flash) [![](https://img.shields.io/github/release/znck/flash.svg)](https://github.com/znck/flash/releases) [![](https://img.shields.io/packagist/v/znck/flash.svg)](https://packagist.org/packages/znck/flash) [![](https://img.shields.io/packagist/dt/znck/flash.svg)](https://packagist.org/packages/znck/flash)  [![](https://img.shields.io/packagist/l/znck/flash.svg)](http://znck.mit-license.org) [![Codacy Badge](https://www.codacy.com/project/badge/005c3669e57442a198f3a4ffe5e5c9e2)](https://www.codacy.com/app/hi_3/flash)

Easy flash notifications for Laravel 5

## Installation

First, pull in the package through Composer.

```js
"require": {
    "znck/flash": "~1.2"
}
```

And then, if using Laravel 5, include the service provider within `app/config/app.php`.

```php
'providers' => [
    'Znck\Flash\FlashServiceProvider'
];
```

And, for convenience, add a facade alias to this same file at the bottom:

```php
'aliases' => [
    'Flash' => 'Znck\Flash\Flash'
];
```

## Usage

Within your controllers, before you perform a redirect...

```php
public function store()
{
    Flash::message('Welcome Aboard!');

    return Redirect::home();
}
```

You may also do:

- `Flash::info('Message')`
- `Flash::success('Message')`
- `Flash::error('Message')`
- `Flash::warning('Message')`
- `Flash::overlay('Modal Message', 'Modal Title')`

This will set a few keys in the session:

- 'znck.flash.notifications' - Session key for flash notification's message bag

Each message will have these keys:

- 'message' - The message you're flashing
- 'level' - A string that represents the HTML class for displaying the message
- 'sort' - Level weight used to sort the messages.

Alternatively, again, you may reference the `flash()` helper function, instead of the facade. Here's an example:

```
/**
 * Destroy the user's session (logout).
 *
 * @return Response
 */
public function destroy()
{
    Auth::logout();

    flash()->success('You have been logged out.');

    return home();
}
```

Or, for a general information flash, just do: `flash('Some message');`.

With this message flashed to the session, you may now display it in your view(s). 
Maybe something like:

```html
@foreach(flash()->get() as $notification)
    <div class="alert alert-{{ $notification['level'] }}">
        <button type="button" class="close" data-dismiss="alert" aria hidden="true">&times;</button>

        {!! $notification['message'] !!}
    </div>
@endforeach
```

You may also do:

- Flash::get()
- flash()->get()

`Flash::get()` or `flash()->get()` function can take an optional variable to filter the result.
Eg:

```
php
Flash::get('*'); // To get all messages.
Flash::get('info'); // To get only messages with info level.
Flash::get('info|warning'); // To get messages with info or warning level
    
```

You can publish the config file to add custom message levels and reorder messages.

```php
// Default messagle levels and their sort order
[
    'classes' => [
        'error'   => 'danger',
        'warning' => 'warning',
        'success' => 'success',
        'info'    => 'info',
    ],

    'levels'  => [
        'error'   => 400,
        'warning' => 300,
        'success' => 200,
        'info'    => 100,
    ]
];
```

> Note that this package is optimized for use with Twitter Bootstrap.

Because flash messages and overlays are so common, if you want, you may use (or modify) the views that are included with this package. Simply append to your layout view:

```html
@include('znck::flash.notifications')
```

## Example

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    @include('flash::message')

    <p>Welcome to my website...</p>
</div>

<script src="//code.jquery.com/jquery.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<!-- This is only necessary if you do Flash::overlay('...') -->
<script>
    $('#flash-overlay-modal').modal();
</script>

</body>
</html>
```

If you need to modify the flash message partials, you can run:

```bash
php artisan vendor:publish
```

The two package views will now be located in the `app/views/packages/laracasts/flash/' directory.

```php
Flash::message('Welcome aboard!');

return Redirect::home();
```

![https://dl.dropboxusercontent.com/u/774859/GitHub-Repos/flash/message.png](https://dl.dropboxusercontent.com/u/774859/GitHub-Repos/flash/message.png)

```php
Flash::error('Sorry! Please try again.');

return Redirect::home();
```

![https://dl.dropboxusercontent.com/u/774859/GitHub-Repos/flash/error.png](https://dl.dropboxusercontent.com/u/774859/GitHub-Repos/flash/error.png)

```php
Flash::overlay('You are now a Laracasts member!');

return Redirect::home();
```

![https://dl.dropboxusercontent.com/u/774859/GitHub-Repos/flash/overlay.png](https://dl.dropboxusercontent.com/u/774859/GitHub-Repos/flash/overlay.png)
