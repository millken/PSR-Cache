<?php
//StrictType
declare(strict_types = 1);

/*
 * Zoe
 * Cache component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */

namespace ZoeTest\Component\Cache;

/**
 * Declare global behaviours shared by all tests
 * Only declare const in this file
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface GlobalConfiguration
{
    
    /**
     * Date format used by all testcases for testing time comparaison implying DateTimeInterface
     *
     * @var string
     */
    public const DATE_FORMAT_TEST = "Y-m-d H:i:s";
    
    /**
     * If tests considered long (call to sleep function) MUST be executed
     *
     * @var bool
     */
    public const EXECUTE_LONG_TESTS = true;
    
    /**
     * Prefix used in test that implied usage of a prefix
     *
     * @var string
     */
    public const PREFIX = "PREFIX_";

    /*********************************/
    /**           REDIS             **/
    /*********************************/
    
    /**
     * Redis host used by this test
     *
     * @var string
     */
    public const REDIS_HOST = "127.0.0.1";
    
    /**
     * Port used for the redis connection
     *
     * @var int
     */
    public const REDIS_PORT = 6379;
    
    /**
     * Options setted into the redis instance
     * 
     * @var array
     */
    public const REDIS_OPTIONS = [
        "prefix"  =>  self::PREFIX   
    ];

}
