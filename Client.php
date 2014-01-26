<?php

namespace Tuenti;

class Client
{
    const API_KEY = 'MDI3MDFmZjU4MGExNWM0YmEyYjA5MzRkODlmMjg0MTU6MC43NzQ4ODAwMCAxMjc1NDcyNjgz';
    const API_URL = 'http://api.tuenti.com/api/';
    const DEFAULT_PAGE_SIZE = 10;
    const VERSION = '0.5';

    private static $headers = array(
        'Accept' => '*/*',
        'Accept-Language' => 'es-es',
        'Connection' => 'keep-alive',
        'User-Agent' => 'Tuenti/1.2 CFNetwork/485.10.2 Darwin/10.3.1',
        'Content-Type' => 'application/x-www-form-urlencoded'
    );

    private $browser;
    private $email;
    private $password;
    private $session;

    public function __construct($email, $password)
    {
        $this->browser = new Browser;
        $this->email = $email;
        $this->password = $password;
    }

    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    public function getFriends()
    {
        return $this->executeAuthenticatedRequest('getFriendsData', array(
            'fields' => array(
                'name', 'surname',  'avatar', 'sex', 'status', 'phone_number', 'chat_server'
            )
        ));
    }

    public function getProfile($userId)
    {
        return current($this->executeAuthenticatedRequest('getUsersData', array(
            'ids' => array($userId),
            'fields' => array(
                'favorite_books', 'favorite_movies', 'favorite_music', 'favorite_quotes',
                'hobbies', 'website', 'about_me_title', 'about_me', 'birthday', 'city', 'province',
                'name', 'surname', 'avatar', 'sex', 'status', 'phone_number', 'chat_server'
            )
        )));
    }

    public function getProfileWallWithStatus($userId, $page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        return $this->executeAuthenticatedRequest('getProfileWallWithStatus', array(
            'user_id' => $userId,
            'page' => $page,
            'page_size' => $size
        ));
    }

    public function setStatus($status)
    {
        return $this->executeAuthenticatedRequest('setUserData', array(
            'status' => $status
        ));
    }

    public function getPersonalNotifications()
    {
        return $this->executeAuthenticatedRequest('getUserNotifications', array(
            'types' => array(
                'unread_friend_messages', 'unread_spam_messages', 'new_profile_wall_posts',
                'new_friend_requests', 'accepted_friend_requests', 'new_photo_wall_posts',
                'new_tagged_photos', 'new_event_invitations', 'new_profile_wall_comments'
            )
        ));
    }

    public function getFriendsNotifications($page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        return $this->executeAuthenticatedRequest('getFriendsNotifications', array(
            'page' => $page,
            'page_size' => $size
        ));
    }

    public function getInbox($page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        return $this->executeAuthenticatedRequest('getInbox', array(
            'page' => $page,
            'page_size' => $size
        ));
    }

    public function getSentBox($page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        return $this->executeAuthenticatedRequest('getSentBox', array(
            'page' => $page,
            'page_size' => $size
        ));
    }

    public function getSpamBox($page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        return $this->executeAuthenticatedRequest('getSpamBox', array(
            'page' => $page,
            'page_size' => $size
        ));
    }

    public function getThread($threadKey, $page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        return $this->executeAuthenticatedRequest('getThread', array(
            'thread_key' => $threadKey,
            'page' => $page,
            'page_size' => $size
        ));
    }

    public function sendMessage($userId, $threadKey, $message)
    {
        return $this->executeAuthenticatedRequest('sendMessage', array(
            'recipient' => $userId,
            'thread_key' => $threadKey,
            'body' => $message
        ));
    }

    public function getAlbums($userId, $page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        return $this->executeAuthenticatedRequest('getUserAlbums', array(
            'user_id' => $userId,
            'page' => $page,
            'albums_per_page' => $size
        ));
    }

    public function getAlbumPhotos($userId, $albumId, $page = 0)
    {
        return $this->executeAuthenticatedRequest('getAlbumPhotos', array(
            'user_id' => $userId,
            'album_id' => $albumId,
            'page' => $page
        ));
    }

    public function getPhotoTags($photoId)
    {
        return $this->executeAuthenticatedRequest('getPhotoTags', array(
            'photo_id' => $photoId
        ));
    }

    public function addPostToPhotoWall($photoId, $message)
    {
        return $this->executeAuthenticatedRequest('addPostToPhotoWall', array(
            'photo_id' => $photoId,
            'body' => $message
        ));
    }

    public function getPhotoWall($photoId, $page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        return $this->executeAuthenticatedRequest('getProfileWallWithStatus', array(
            'photo_id' => $photoId,
            'page' => $page,
            'post_per_page' => $size
        ));
    }

    public function getUpcomingEvents($size = self::DEFAULT_PAGE_SIZE, $includeBirthdays = false)
    {
        return $this->executeAuthenticatedRequest('getUpcomingEvents', array(
            'desired_number' => $size,
            'include_friend_birthdays' => $includeBirthdays
        ));
    }

    public function getEvent($eventId)
    {
        return $this->executeAuthenticatedRequest('getEvent', array(
            'event_id' => $eventId
        ));
    }

    public function getEventWall($eventId, $page = 0, $size = self::DEFAULT_PAGE_SIZE)
    {
        return $this->executeAuthenticatedRequest('getEventWall', array(
            'event_id' => $eventId,
            'page' => $page,
            'posts_per_page' => $size
        ));
    }

    private function executeAuthenticatedRequest($method, $parameters)
    {
        $session = $this->getSession();
        $request = array_merge($this->buildRequest($method, $parameters), array(
            'session_id' => $session['session_id']
        ));

        return $this->sendRequest($request);
    }

    private function getSession()
    {
        if ($this->session) {
            return $this->session;
        }

        $c = $this->getChallenge();

        $this->session = $this->executeRequest('getSession', array(
            'passcode' => $this->generatePasscode($c['challenge']),
            'seed' => $c['seed'],
            'email' => $this->email,
            'timestamp' => $c['timestamp'],
            'application_key' => self::API_KEY
        ));

        return $this->session;
    }

    private function getChallenge()
    {
        return $this->executeRequest('getChallenge', array('type' => 'login'));
    }

    private function generatePasscode($challenge)
    {
        return md5($challenge . md5($this->password));
    }

    private function buildRequest($method, $parameters)
    {
        return array(
            'version' => self::VERSION,
            'requests' => array(array($method, $parameters))
        );
    }

    private function sendRequest($request)
    {
        $rawResponse = $this->browser->post(self::API_URL, json_encode($request), self::$headers);
        $response = current(json_decode($rawResponse, true));
        $this->assertNoErrors($response);

        return $response;
    }

    private function assertNoErrors($response)
    {
        if (isset($response['error'])) {
            throw new ApiError($response['message'], $response['error']);
        }
    }

    private function executeRequest($method, $parameters)
    {
        $request = $this->buildRequest($method, $parameters);

        return $this->sendRequest($request);
    }

    public function me()
    {
        $session = $this->getSession();

        return $session['user_id'];
    }
}