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

namespace Ness\Component\Cache\Adapter;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;

/**
 * Simple wrapper to log error from the adapter
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class LoggingWrapperCacheAdapter implements CacheAdapterInterface, LoggerAwareInterface
{
    
    use LoggerAwareTrait;
    
    /**
     * Adapter wrapped
     * 
     * @var CacheAdapterInterface
     */
    private $adapter;
    
    /**
     * Log level used to log error from the adapter
     * 
     * @var string
     */
    private $logLevel;
    
    /**
     * Bit mask log
     * 
     * @var int
     */
    private $maskLog;
    
    /**
     * Wrapped class name adapter
     * 
     * @var string
     */
    private $adapterName;
    
    /**
     * Log error when a cache value cannot be getted
     * 
     * @var int
     */
    public const LOG_GET = 1;
    
    /**
     * Log error when a cache value cannot be setted
     *
     * @var int
     */
    public const LOG_SET = 2;
    
    /**
     * Log error when a cache value cannot be deleted
     *
     * @var int
     */
    public const LOG_DELETE = 4;
    
    /**
     * Initialize adapter
     * 
     * @param CacheAdapterInterface $adapter
     *   Cache adapter to wrap
     * @param string|null $adapterIdentifier
     *   Identify the adapter wrapped or will use the class name of it 
     * @param string $logLevel
     *   Log level used to log error (default setted to error)
     * @param int $maskLog
     *   What to log (by default will log errors when a value cannot be setted or deleted) bit mask
     */
    public function __construct(
        CacheAdapterInterface $adapter,
        ?string $adapterIdentifier = null,
        string $logLevel = LogLevel::ERROR, 
        int $maskLog = self::LOG_SET|self::LOG_DELETE)
    {
        $this->adapter = $adapter;
        $this->logLevel = $logLevel;
        $this->maskLog = $maskLog;
        $this->adapterName = $adapterIdentifier ?? \get_class($adapter);
    }

    /**
     * {@inheritDoc}
     * @see \Ness\Component\Cache\Adapter\CacheAdapterInterface::get()
     */
    public function get(string $key): ?string
    {
        $result = $this->adapter->get($key);
        
        return $this->log(
            self::LOG_GET, 
            $result, 
            "Cache key '{$key}' cannot be reached over '{$this->adapterName}' adapter", 
            null === $result
        );
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Cache\Adapter\CacheAdapterInterface::getMultiple()
     */
    public function getMultiple(array $keys): array
    {
        $results = $this->adapter->getMultiple($keys);

        return $this->log(
            self::LOG_GET, 
            $results, 
            \sprintf("This keys '%s' via '%s' adapter cannot be reached", 
                \implode(", ", \array_filter(
                                    \array_map(function(?string $value, string $key): ?string {
                                        if(null === $value)
                                            return $key;
                                        return null;
                                    }, $results, $keys))),
                $this->adapterName), 
            \in_array(null, $results)
        );
    }

    /**
     * {@inheritDoc}
     * @see \Ness\Component\Cache\Adapter\CacheAdapterInterface::set()
     */
    public function set(string $key, string $value, ?int $ttl): bool
    {
        $result = $this->adapter->set($key, $value, $ttl);
        return $this->log(
            self::LOG_SET, 
            $result, 
            "This cache key '{$key}' cannot be setted into cache via '{$this->adapterName}' adapter", 
            !$result
        );
    }

    /**
     * {@inheritDoc}
     * @see \Ness\Component\Cache\Adapter\CacheAdapterInterface::setMultiple()
     */
    public function setMultiple(array $values): ?array
    {
        $results = $this->adapter->setMultiple($values);

        return $this->log(
            self::LOG_SET, 
            $results, 
            \sprintf("This cache keys '%s' cannot be setted into cache via '%s' adapter",
                \implode(", ", $results ?? []),
                $this->adapterName), 
            null !== $results
        );
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Cache\Adapter\CacheAdapterInterface::delete()
     */
    public function delete(string $key): bool
    {
        $result = $this->adapter->delete($key);
        
        return $this->log(
            self::LOG_DELETE, 
            $result, 
            "This cache key '{$key}' cannot be deleted from cache via '{$this->adapterName}' adapter", 
            !$result
        );
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Cache\Adapter\CacheAdapterInterface::deleteMultiple()
     */
    public function deleteMultiple(array $keys): ?array
    {
        $result = $this->adapter->deleteMultiple($keys);
        
        return $this->log(
            self::LOG_DELETE,
            $result,
            \sprintf("This cache keys '%s' cannot be delete from cache via '%s' adapter",
                \implode(", ", $result ?? []),
                $this->adapterName),
            null !== $result
        );
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Cache\Adapter\CacheAdapterInterface::has()
     */
    public function has(string $key): bool
    {
        return $this->adapter->has($key);
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Cache\Adapter\CacheAdapterInterface::purge()
     */
    public function purge(?string $pattern): void
    {
        $this->adapter->purge($pattern);
    }

    /**
     * Log value depending of a verification parameter.
     * 
     * @param int $logLevel
     *   Log level needed to log. If not enough, will early return provided value
     * @param mixed $value
     *   Value to check, log and return
     * @param string $logMessage
     *   Message to log
     * @param bool $verification
     *   Verification over given value. If return true, log will be done
     *  
     * @return mixed
     *   Given value
     */
    private function log(int $logLevel, $value, string $logMessage, bool $verification)
    {
        if( !(bool) ($this->maskLog & $logLevel) || !$verification)       
            return $value;

        $this->logger->log($this->logLevel, $logMessage);
        
        return $value;
    }
    
}
