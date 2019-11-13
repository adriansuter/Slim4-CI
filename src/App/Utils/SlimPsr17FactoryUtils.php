<?php
/**
 * Slim4 CI (https://github.com/adriansuter/Slim4-CI)
 *
 * @license https://github.com/adriansuter/Slim4-CI/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace App\Utils;

use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;
use Slim\Factory\Psr17\Psr17Factory;
use Slim\Factory\Psr17\Psr17FactoryProvider;

class SlimPsr17FactoryUtils
{
    /**
     * @return StreamFactoryInterface
     */
    public static function getStreamFactory(): StreamFactoryInterface
    {
        $psr17FactoryProvider = new Psr17FactoryProvider();

        /** @var Psr17Factory $psr17factory */
        foreach ($psr17FactoryProvider->getFactories() as $psr17factory) {
            if ($psr17factory::isStreamFactoryAvailable()) {
                return $psr17factory::getStreamFactory();
            }
        }

        throw new RuntimeException('No stream factory found.');
    }
}
