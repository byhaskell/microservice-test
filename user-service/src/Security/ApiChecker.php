<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;

readonly class ApiChecker
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) { }

    public function check($apiKey): void
    {
        if ($this->parameterBag->get('internal_api_key') !== $apiKey) {
            throw new AccessDeniedException('Не коректний API ключ');
        }
    }
}
