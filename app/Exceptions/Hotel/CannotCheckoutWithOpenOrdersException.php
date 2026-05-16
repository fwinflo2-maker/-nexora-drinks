<?php

declare(strict_types=1);

namespace App\Exceptions\Hotel;

use RuntimeException;

class CannotCheckoutWithOpenOrdersException extends RuntimeException
{
    public function __construct(int $openOrderCount)
    {
        parent::__construct(
            "Impossible d'effectuer le check-out : {$openOrderCount} commande(s) F&B encore ouverte(s)."
        );
    }
}
