<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

readonly class ApiChecker
{
    public const KEY = 'X-API-Key';

    public function __construct(
        private ParameterBagInterface $parameterBag
    ) { }

    public function check($apiKey)
    {
        if ($this->parameterBag->get('internal_api_key') !== $apiKey) {
            throw new AccessDeniedHttpException('Не коректний API ключ');
        }
    }
}
