<?php

use GuzzleHttp\Adapter\MockAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream;

class PPHApiTest extends \PHPUnit_Framework_TestCase
{
    // Test we can use the PPHApi to fetch data for a user
    public function testUserView()
    {
        // Define a mock user view API response
        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            Stream\create('{"data":{"job_title":"Web Developer"}}')
        );

        // cURL is the usual adapter but lets create a mock adapter that uses our mock response
        $adapter = new MockAdapter($mockResponse);

        // A Client is used to send HTTP requests - lets create one with our mock adaptor (instead of cURL)
        $client = new Client(['adapter' => $adapter]);

        // Check our mock client is returning our mock response (whatever endpoint we give)
        $this->assertSame($mockResponse, $client->get('http://test.com'));
        $this->assertEquals('http://test.com', $mockResponse->getEffectiveUrl());

        // Create our command client using our dummy http client
        $pphApi = new PPHApi('dummyID', 'dummyKey', $client);

        $this->assertEquals('dummyID', $pphApi->apiId);
        $this->assertEquals('dummyKey', $pphApi->apiKey);

        // Test the base_url is set as expected
        $this->assertEquals('https://api.peopleperhour.com/', $pphApi->getConfig('base_url'));

        // Test default config is empty
        $this->assertEquals([], $pphApi->getConfig('defaults'));

        // Finally we can do the test we want
        $response = $pphApi->user(['id'=>12345]);
        $this->assertInstanceOf('GuzzleHttp\\Command\\Model', $response);
        $this->assertEquals('Web Developer', $response['data']['job_title']);

        // Test alternative usage
        $command = $pphApi->getCommand('user',['id'=>12345]);
        $this->assertInstanceOf('GuzzleHttp\\Command\\Guzzle\\Command', $command);
        $this->assertEquals('User', $command->getName());
        $result = $pphApi->execute($command);
        $this->assertInstanceOf('GuzzleHttp\\Command\\Model', $result);
        $this->assertEquals(200, $result['code']);

        // Test that a exception is thrown for a random command
        $this->setExpectedException('InvalidArgumentException', 'No operation found named thisShouldNotExist');
        $pphApi->getCommand('thisShouldNotExist');

        // The following is pointless because our response is fixed but test other parameters
        $pphApi->user(['id'=>21561, 'a'=>'cert']);
    }

    // Test we can use the PPHApi to fetch data for a list of users
    public function testUserList()
    {
        // Define a mock user list API response
        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            Stream\create('{"data":[{"fname":"Tom"},{"fname":"awardwinner"},{"fname":"Social"},{"fname":"Benny"},{"fname":"White Hat SEO"},{"fname":"Rebecca (Dakota Digital Ltd)"},{"fname":"Chris"},{"fname":"Guru"},{"fname":"Michael"},{"fname":"Aled"},{"fname":"Matthew"},{"fname":"Peter"},{"fname":"Steven"},{"fname":"Agile Cyber Solutions"},{"fname":"T"},{"fname":"Green"},{"fname":"Fiona"},{"fname":"Wojtek"},{"fname":"Darren"},{"fname":"Natasha"}],"count":630330,"page":1,"pageCount":20,"nextUrl":"https:\/\/api.peopleperhour.com\/v1\/user?a=fname&sort=rating&page=2","previousUrl":null}')
        );

        // Create our command client but use a dummy http client that avoids cURLing to the real API
        $pphApi = new PPHApi('dummyID', 'dummyKey', new Client(['adapter' => new MockAdapter($mockResponse)]));

        $response = $pphApi->userList();
        $this->assertEquals(20, count($response['data']));
        $this->assertEquals('Tom', $response['data'][0]['fname']);

        // The following is pointless because our response is fixed but test other parameters
        $response = $pphApi->userList(['page'=>2]);
        $response = $pphApi->userList(['page'=>2,'sort'=>'fname.desc']);
    }

}
