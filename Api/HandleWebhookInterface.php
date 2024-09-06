<?php
declare(strict_types=1);

namespace Pallapay\PPG\Api;

interface HandleWebhookInterface
{
    /**
     * @return string
     */
    public function handle();
}
