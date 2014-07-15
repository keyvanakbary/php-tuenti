# PHP Tuenti API

[![Build Status](https://secure.travis-ci.org/keyvanakbary/php-tuenti.svg?branch=master)](http://travis-ci.org/keyvanakbary/php-tuenti)

Unofficial Tuenti API.

## Setup and Configuration
Add the following to your `composer.json` file
```json
{
    "require": {
        "keyvanakbary/tuenti": "~1.0"
    }
}
```

Update the vendor libraries

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

## Usage

```php
<?php

require 'vendor/autoload.php';

use Tuenti\Client;
use Tuenti\ApiError;

$t = new Client('foo@example.com', 'password');

try {
    $profile = $t->getProfile($t->me());
} catch (ApiError $e) {
    error_log($e->getMessage());
}
```

Quick Reference
---------------

### Current User
The method `me()` returns your user id. You can use it in methods that require a `$userId` to retrieve your own data.
```php
$t->getProfile($t->me());
```

### Profile

Get profile for a given user
```php
getProfile($userId)
```

Get profile wall with statuses for a given user
```php
getProfileWallWithStatus($userId [, $page = 0 [, $size = 10]])
```

Get all your friends
```php
getFriends()
```

Set your status
```php
setStatus($status)
```

### Notifications

Get personal notifications
```php
getPersonalNotifications()
```

Get friends notifications
```php
getFriendsNotifications([$page = 0 [, $size = 10]])
```

### Messages

Get inbox message threads
```php
getInbox([$page = 0 [, $size = 10]])
```

Get sent message threads
```php
getSentBox([$page = 0 [, $size = 10]])
```

Get spam message threads
```php
getSpamBox([$page = 0 [, $size = 10]])
```

Retrieve messages from a given thread
```php
getThread($threadKey [, $page = 0 [, $size = 10]])
```

Send a message
```php
sendMessage($userId, $threadKey, $message)
```

### Photos

Get user albums
```php
getAlbums($userId [, $page = 0 [, $size = 10]])
```

Get album photos for a given user
```php
getAlbumPhotos($userId, $albumId [, $page = 0])
```

Get all the tags for a photo
```php
getPhotoTags($photoId)
```

Add a post to a photo wall
```php
addPostToPhotoWall($photoId, $message)
```

Get the wall posts for a given photo
```php
getPhotoWall($photoId [, $page = 0 [, $size = 10]])
```

Iterate over all your albums and photos
```php
foreach ($t->getAlbums($t->me()) as $albumId => $album) {
    // do something with $album
    for ($i = 0; $i < $album['size']; $i = $i + Client::DEFAULT_PAGE_SIZE) {
        $page = floor($i / Client::DEFAULT_PAGE_SIZE);
        $photos = current($t->getAlbumPhotos($t->me(), $albumId, $page));
        foreach ($photos as $photo) {
            // do something with $photo
        }
    }
}
```

### Events

Get upcoming events. You can include birthays
```php
getUpcomingEvents([$size = 10 [, $includeBirthdays = false]])
```

Retrieve event
```php
getEvent($eventId)
```

Get the wall for a given event
```php
getEventWall($eventId [, $page = 0 [, $size = 10]])
```
