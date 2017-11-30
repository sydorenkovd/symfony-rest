<?php
/**
 * Created by PhpStorm.
 * User: sydorenkovd
 * Date: 20.10.17
 * Time: 17:17
 */
namespace AppBundle\Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use GuzzleHttp\Client;
use GuzzleHttp\Message\AbstractMessage;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Subscriber\History;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ApiTestCase extends KernelTestCase
{
    private static $staticClient;
    /**
     * @var History
     */
    private static $history;
    /**
     * @var Client
     */
    protected $client;
    protected function setUp()
    {
        $this->client = self::$staticClient;
        $this->purgeDatabase();
    }
    public static function setUpBeforeClass()
    {
        self::$staticClient = new Client([
            'base_url' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        self::$history = new History();

        self::bootKernel();
    }
    protected function onNotSuccessfulTest(\Exception $e)
    {
        if (self::$history && $lastResponse = self::$history->getLastResponse()) {
            $this->printDebug('');
            $this->printDebug('<error>Failure!</error> when making the following request:');
            $this->printLastRequestUrl();
            $this->printDebug('');
            $this->debugResponse($lastResponse);
        }
        throw $e;
    }
    protected function debugResponse(ResponseInterface $response)
    {
        $this->printDebug(AbstractMessage::getStartLineAndHeaders($response));
        $body = (string) $response->getBody();
    }
    protected function printDebug($string)
    {
        echo $string."\n";
    }
    protected function printLastRequestUrl()
    {
        $lastRequest = self::$history->getLastRequest();
        if ($lastRequest) {
            $this->printDebug(sprintf('<comment>%s</comment>: <info>%s</info>', $lastRequest->getMethod(), $lastRequest->getUrl()));
        } else {
            $this->printDebug('No request was made.');
        }
    }
    private function purgeDatabase()
    {
        $purger = new ORMPurger($this->getService('doctrine')->getManager());
        $purger->purge();
    }

    protected function getService($id)
    {
        return self::$kernel->getContainer()
            ->get($id);
    }
    /**
     * Clean up Kernel usage in this test.
     */
    protected function tearDown()
    {
        // purposefully not calling parent class, which shuts down the kernel
    }
}