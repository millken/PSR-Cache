<?php
//StrictType
declare(strict_types = 1);

/*
 * Ness
 * Cache component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */

namespace NessTest\Component\Cache;

use Psr\Log\LoggerInterface;
use Ness\Component\Cache\NullCache;

/**
 * NullCache testcase
 *
 * @see \Ness\Component\Cache\ApcuCache
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NullCacheTest extends AbstractCacheTest
{
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->cache = [
            new NullCache()
        ];
        if(\interface_exists(LoggerInterface::class))
            $this->cache[] = new NullCache(null, null, $this->getMockBuilder(LoggerInterface::class)->getMock());
    }
    
}
