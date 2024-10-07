<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Framework\Symfony;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Class Kernel.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
