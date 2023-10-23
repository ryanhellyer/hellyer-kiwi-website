<?php
/**** USE BRAIN MONKEY - ALREADY CONFIG'D IN COMPOSER.JSON */
declare(strict_types=1);

namespace Inpsyde\CodingTest;

use PHPUnit\Framework\TestCase;
use WP_Mock;
use Mockery;

class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        WP_Mock::setUp();
    }

    protected function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /**
     * Testing that the correct cached key is returned.
     */
    public function testCachedKey()
    {
        $cache = new Cache();
        $reflection = new \ReflectionClass($cache);
        $method = $reflection->getMethod('cachedKey');
        $method->setAccessible(true);
        $userId = rand();
        $result = $method->invokeArgs($cache, [$userId]);
        if ($result === 'codingtest_user_' . $userId) {
            $assert = true;
        }
        $this->assertTrue($assert);
    }

    /**
     * Testing that the correct cache status is returned.
     */
    public function testRetrieveCachedData()
    {
        WP_Mock::userFunction('absint', [
            'times' => 2, // We run two tests on the same method, so need to run this twice.
            'return' => static function (int $input): int {
                return $input;
            },
        ]);

        $softExpiry = time() + 3000;
        WP_Mock::userFunction('get_transient', [
            'args' => 'codingtest_user_1',
            'times' => 1,
            'return' => [
                'softExpiry' => $softExpiry,
                'softExpiryHumanReadable' => date('Y-m-d H:i:s', $softExpiry),
            ],
        ]);

        // Testing for a valid cache.
        $cache = new Cache();
        $reflection = new \ReflectionClass($cache);
        $method = $reflection->getMethod('retrieveCachedData');
        $method->setAccessible(true);

        $result1 = $method->invokeArgs($cache, [1]);
        if ($result1['cacheStatus'] === 'cached') {
            $assert = true;
        }
        $this->assertTrue($assert);

        // This time testing for a stale cache.
        $softExpiry = time() - 3000;
        WP_Mock::userFunction('get_transient', [
            'args' => 'codingtest_user_1',
            'times' => 1,
            'return' => [
                'softExpiry' => $softExpiry,
                'softExpiryHumanReadable' => date('Y-m-d H:i:s', $softExpiry),
            ],
        ]);
        $result2 = $method->invokeArgs($cache, [1]);
        if ($result2['cacheStatus'] === 'stale') {
            $assert = true;
        }
        $this->assertTrue($assert);
    }
}
