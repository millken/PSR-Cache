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

use Ness\Component\Cache\AbstractCache;
use Psr\SimpleCache\CacheInterface;
use Psr\Cache\CacheItemPoolInterface;
use Ness\Component\Cache\NullCache;

/**
 * Common to all caches
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class AbstractCacheTest extends CacheTestCase
{

    /**
     * Instance of cache
     * 
     * @var AbstractCache[]
     */
    protected $cache;
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::get()
     */
    public function testGet(): void
    {
        $this->execute(function(CacheInterface $cache): void {
            $cache->set("foo", "bar");
            
            if($cache instanceof NullCache) {
                $this->assertNull($cache->get("foo"));
            } else {
                $this->assertSame("bar", $cache->get("foo"));
                $this->assertSame("default", $cache->get("bar", "default"));
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::set()
     */
    public function testSet(): void
    {
        $this->execute(function(CacheInterface $cache): void {
            if($cache instanceof NullCache) {
                $this->assertFalse($cache->set("foo", "bar"));
            } else {
                $this->assertTrue($cache->set("foo", "bar"));                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::delete()
     */
    public function testDelete(): void
    {
        $this->execute(function(CacheInterface $cache): void {
            $cache->set("foo", "bar");
            
            if($cache instanceof NullCache) {
                $this->assertFalse($cache->delete("foo"));
            } else {    
                $this->assertTrue($cache->delete("foo"));
                $this->assertFalse($cache->delete("bar"));                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::clear()
     */
    public function testClear(): void
    {
        $this->execute(function(CacheInterface $cache): void {
            $this->assertTrue($cache->clear());
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::getMultiple()
     */
    public function testGetMultiple(): void
    {
        $this->execute(function(CacheInterface $cache): void {
            $cache->set("moz", "poz");
            $cache->set("poz", "moz");
            
            if($cache instanceof NullCache) {
                $this->assertSame(["foo" => "default", "bar" => "default"], $cache->getMultiple(["foo", "bar"], "default"));   
            } else {
                $found = $cache->getMultiple(["foo", "poz", "moz"], "default");
                
                $this->assertSame("poz", $found["moz"]);
                $this->assertSame("moz", $found["poz"]);
                $this->assertSame("default", $found["foo"]);
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::setMultiple()
     */
    public function testSetMultiple(): void
    {
        $this->execute(function(CacheInterface $cache): void {
            if($cache instanceof NullCache) {
                $this->assertFalse($cache->setMultiple(["foo" => "bar", "bar" => "foo"]));
            } else {
                $this->assertTrue($cache->setMultiple(["foo" => "bar", "bar" => "foo"]));
                $this->assertSame("bar", $cache->get("foo"));                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::deleteMultiple()
     */
    public function testDeleteMultiple(): void
    {
        $this->execute(function(CacheInterface $cache): void {
            $cache->set("foo", "bar");
            $cache->set("bar", "foo");
            
            if($cache instanceof NullCache) {
                $this->assertFalse($cache->deleteMultiple(["foo", "bar"]));
            } else {
                $this->assertTrue($cache->deleteMultiple(["foo", "bar"]));
                $this->assertFalse($cache->deleteMultiple(["foo", "bar"]));                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::has()
     */
    public function testHas(): void
    {
        $this->execute(function(CacheInterface $cache): void {
            $cache->set("foo", "bar");
            
            if($cache instanceof NullCache) {
                $this->assertFalse($cache->has("foo"));
            } else {
                $this->assertTrue($cache->has("foo"));
                $this->assertFalse($cache->has("bar"));                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::getItem()
     */
    public function testGetItem(): void
    {
        $this->execute(function(CacheItemPoolInterface $pool): void {
            $item = $pool->getItem("foo");
            
            $this->assertSame("foo", $item->getKey());
            $this->assertFalse($item->isHit());
            
            $pool->save($item);
            
            $item = $pool->getItem("foo");
            
            if($pool instanceof NullCache) {
                $this->assertSame("foo", $item->getKey());
                $this->assertFalse($item->isHit());
            } else {
                $this->assertSame("foo", $item->getKey());
                $this->assertTrue($item->isHit());                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::getItems()
     */
    public function testGetItems(): void
    {
        $this->execute(function(CacheItemPoolInterface $pool): void {
            $items = $pool->getItems(["foo", "bar"]);
            foreach ($items as $item)
                $this->assertFalse($item->isHit());
            $pool->save($items["foo"]);
            if($pool instanceof NullCache) {
                $this->assertFalse($pool->getItem("foo")->isHit());
            } else {
                $this->assertTrue($pool->getItem("foo")->isHit());                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::hasItem()
     */
    public function testHasItem(): void
    {
        $this->execute(function(CacheItemPoolInterface $pool): void {
            $this->assertFalse($pool->hasItem("foo"));
            $pool->save($pool->getItem("foo"));
            
            if($pool instanceof NullCache) {
                $this->assertFalse($pool->hasItem("foo"));
            } else {                
                $this->assertTrue($pool->hasItem("foo"));
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::deleteItem()
     */
    public function testDeleteItem(): void
    {
        $this->execute(function(CacheItemPoolInterface $pool): void {
            $this->assertFalse($pool->deleteItem("foo"));
            $pool->save($pool->getItem("foo"));
            
            if($pool instanceof NullCache) {
                $this->assertFalse($pool->deleteItem("foo"));
            } else {
                $this->assertTrue($pool->deleteItem("foo"));                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::deleteItems()
     */
    public function testDeleteItems(): void
    {
        $this->execute(function(CacheItemPoolInterface $pool): void {
            $pool->save($pool->getItem("foo"));
            $pool->save($pool->getItem("bar"));
            
            if($pool instanceof NullCache) {
                $this->assertFalse($pool->deleteItems(["foo", "bar"]));
            } else {
                $this->assertTrue($pool->deleteItems(["foo", "bar"]));
                $this->assertFalse($pool->deleteItems(["foo", "bar"]));                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::save()
     */
    public function testSave(): void
    {
        $this->execute(function(CacheItemPoolInterface $pool): void {
            if($pool instanceof NullCache) {
                $this->assertFalse($pool->save($pool->getItem("foo")));
                $this->assertFalse($pool->hasItem("foo"));
            } else {
                $this->assertTrue($pool->save($pool->getItem("foo")));
                $this->assertTrue($pool->hasItem("foo"));                
            }
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::saveDeferred()
     */
    public function testSaveDeferred(): void
    {
        $this->execute(function(CacheItemPoolInterface $pool): void {
            $this->assertTrue($pool->saveDeferred($pool->getItem("foo")));
        });
    }
    
    /**
     * @see \Ness\Component\Cache\AbstractCache::commit()
     */
    public function testCommit(): void
    {
        $this->execute(function(CacheItemPoolInterface $pool): void {
            $pool->saveDeferred($pool->getItem("foo"));
            
            if($pool instanceof NullCache) {
                $this->assertFalse($pool->commit());
                
                $this->assertFalse($pool->hasItem("foo"));
            } else {
                $this->assertTrue($pool->commit());
                
                $this->assertTrue($pool->hasItem("foo"));                
            }
        });
    }
    
    /**
     * Execute an action over all setted caches.
     * 
     * @param \Closure $action
     *   Action to execute. Takes as parameter the cache
     */
    private function execute(\Closure $action): void
    {
        foreach ($this->cache as $cache) {
            $action->call($this, $cache);
            $cache->clear();
        }
    }
    
}
