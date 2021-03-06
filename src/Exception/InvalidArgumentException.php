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

namespace Ness\Component\Cache\Exception;

use Psr\Cache\InvalidArgumentException as PSR6InvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as PSR16InvalidArgumentException;

/**
 * InvalidArgumentException
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class InvalidArgumentException extends \InvalidArgumentException implements PSR6InvalidArgumentException, PSR16InvalidArgumentException
{
    //
}