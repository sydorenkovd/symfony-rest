<?php
/**
 * Created by PhpStorm.
 * User: sydorenkovd
 * Date: 20.10.17
 * Time: 17:17
 */
namespace AppBundle\Tests;

use GuzzleHttp\Client;

class ApiTestCase extends \PHPUnit_Framework_TestCase
{
    private static $staticClient;
    /**
     * @var Client
     */
    protected $client;
    protected function setUp()
    {
        $this->client = self::$staticClient;
    }
    public static function setUpBeforeClass()
    {
        self::$staticClient = new Client([
            'base_url' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
    }

}