<?php

require __DIR__ . '/FakeBrowser.php';

use Tuenti\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $browser;

    protected function setUp()
    {
        $this->client = new Client('email', 'password');
        $this->browser = new FakeBrowser;
        $this->client->setBrowser($this->browser);
    }

    /**
     * @test
     */
    public function shouldAuthenticate()
    {
        $this->browserAuthenticates();

        $me = $this->client->me();

        $this->assertEquals('id', $me);
        $this->assertBrowserHeadersAreValid();
        $this->assertBrowserReceivedValidAuthenticationDetails();
    }

    /**
     * @test
     */
    public function shouldCacheSession()
    {
        $this->browserAuthenticates();

        $this->client->me();
        $this->client->me();

        $this->assertEquals(2, $this->browser->numberOfRequests);
    }

    /**
     * @test
     * @expectedException Tuenti\ApiError
     */
    public function responseWithErrorShouldThrowException()
    {
        $this->browser->returns('getChallenge', '[{"error":32,"message":"Test error"}]');

        $this->client->me();
    }

    /**
     * @test
     */
    public function getFriendsCallCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getFriends();

        $this->assertSentParametersEquals('getFriendsData', array('fields' => array(
            'name', 'surname',  'avatar', 'sex', 'status', 'phone_number', 'chat_server'
        )));
    }

    /**
     * @test
     */
    public function getProfileCallCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getProfile(1);

        $this->assertSentParametersEquals('getUsersData', array(
            'ids' => array(1),
            'fields' => array(
                'favorite_books', 'favorite_movies', 'favorite_music', 'favorite_quotes',
                'hobbies', 'website', 'about_me_title', 'about_me', 'birthday', 'city', 'province',
                'name', 'surname', 'avatar', 'sex', 'status', 'phone_number', 'chat_server'
            )));
    }

    /**
     * @test
     */
    public function getProfileWallWithStatusCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getProfileWallWithStatus(1, 2, 3);

        $this->assertSentParametersEquals('getProfileWallWithStatus', array(
            'user_id' => 1, 'page' => 2, 'page_size' => 3
        ));
    }

    /**
     * @test
     */
    public function setStatusCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->setStatus('status');

        $this->assertSentParametersEquals('setUserData', array('status' => 'status'));
    }

    /**
     * @test
     */
    public function getPersonalNotificationsCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getPersonalNotifications();

        $this->assertSentParametersEquals('getUserNotifications', array(
            'types' => array(
                'unread_friend_messages', 'unread_spam_messages', 'new_profile_wall_posts',
                'new_friend_requests', 'accepted_friend_requests', 'new_photo_wall_posts',
                'new_tagged_photos', 'new_event_invitations', 'new_profile_wall_comments'
            )
        ));
    }

    /**
     * @test
     */
    public function getFriendsNotificationsCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getFriendsNotifications(1, 2);

        $this->assertSentParametersEquals('getFriendsNotifications', array('page' => 1, 'page_size' => 2));
    }

    /**
     * @test
     */
    public function getInboxCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getInbox(1, 2);

        $this->assertSentParametersEquals('getInbox', array('page' => 1, 'page_size' => 2));
    }

    /**
     * @test
     */
    public function getSentBoxCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getSentBox(1, 2);

        $this->assertSentParametersEquals('getSentBox', array('page' => 1, 'page_size' => 2));
    }

    /**
     * @test
     */
    public function getSpamBoxCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getSpamBox(1, 2);

        $this->assertSentParametersEquals('getSpamBox', array('page' => 1, 'page_size' => 2));
    }

    /**
     * @test
     */
    public function getThreadCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getThread(1, 2, 3);

        $this->assertSentParametersEquals('getThread', array('thread_key' => 1, 'page' => 2, 'page_size' => 3));
    }

    /**
     * @test
     */
    public function sendMessageCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->sendMessage(1, 2, 'message');

        $this->assertSentParametersEquals('sendMessage', array(
            'recipient' => 1, 'thread_key' => 2, 'body' => 'message'
        ));
    }

    /**
     * @test
     */
    public function getAlbumsCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getAlbums(1, 2, 3);

        $this->assertSentParametersEquals('getUserAlbums', array('user_id' => 1, 'page' => 2, 'albums_per_page' => 3));
    }

    /**
     * @test
     */
    public function getAlbumPhotosCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getAlbumPhotos(1, 2, 3);

        $this->assertSentParametersEquals('getAlbumPhotos', array('user_id' => 1, 'album_id' => 2, 'page' => 3));
    }

    /**
     * @test
     */
    public function getPhotoTagsCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getPhotoTags(1);

        $this->assertSentParametersEquals('getPhotoTags', array('photo_id' => 1));
    }

    /**
     * @test
     */
    public function addPostToPhotoWallCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->addPostToPhotoWall(1, 'message');

        $this->assertSentParametersEquals('addPostToPhotoWall', array('photo_id' => 1, 'body' => 'message'));
    }

    /**
     * @test
     */
    public function getPhotoWallCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getPhotoWall(1, 2, 3);

        $this->assertSentParametersEquals('getPhotoWall', array('photo_id' => 1, 'page' => 2, 'post_per_page' => 3));
    }

    /**
     * @test
     */
    public function getUpcomingEventsCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getUpcomingEvents(1, true);

        $this->assertSentParametersEquals('getUpcomingEvents', array(
            'desired_number' => 1,
            'include_friend_birthdays' => true
        ));
    }

    /**
     * @test
     */
    public function getEventCallShouldFitContract()
    {
        $this->browserAuthenticatesAndReturnsData();

        $this->client->getEvent(1);

        $this->assertSentParametersEquals('getEvent', array('event_id' => 1));
    }

    private function browserAuthenticates()
    {
        $this->browser
            ->returns('getChallenge', '[{"challenge":"c","seed":"f","timestamp":1}]')
            ->returns('getSession', '[{"user_id":"id","session_id":"sid"}]');
    }

    private function assertBrowserHeadersAreValid()
    {
        $expectedHeaders = array(
            'Accept' => '*/*',
            'Accept-Language' => 'es-es',
            'Connection' => 'keep-alive',
            'User-Agent' => 'Tuenti/1.2 CFNetwork/485.10.2 Darwin/10.3.1',
            'Content-Type' => 'application/x-www-form-urlencoded'
        );

        $this->assertEquals($expectedHeaders, $this->browser->requests['getChallenge']['headers']);
    }

    private function assertBrowserReceivedValidAuthenticationDetails()
    {
        $expectedSessionRequest =
            '{"version":"0.5","requests":[["getSession",{' .
            '"passcode":"420986589fa018302702d5c11b0460b9","seed":"f","email":"email","timestamp":1,' .
            '"application_key":"MDI3MDFmZjU4MGExNWM0YmEyYjA5MzRkODlmMjg0MTU6MC43NzQ4ODAwMCAxMjc1NDcyNjgz"' .
            '}]]}';

        $this->assertEquals($expectedSessionRequest, $this->browser->requests['getSession']['parameters']);
    }

    private function assertSentParametersEquals($method, $data) {
        $this->assertEquals(array($method, $data), $this->browser->getSentParameters($method));
    }

    private function browserAuthenticatesAndReturnsData()
    {
        $this->browserAuthenticates();
        $this->browser->alwaysReturn('[[]]');
    }
}