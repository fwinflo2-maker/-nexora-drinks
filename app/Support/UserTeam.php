<?php

namespace App\Support;

readonly class UserTeam
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public bool $isPersonal,
        public ?string $role,
        public ?string $roleLabel,
        public ?string $sector = null,
        public ?bool $isCurrent = null,
    ) {
        //
    }
}
