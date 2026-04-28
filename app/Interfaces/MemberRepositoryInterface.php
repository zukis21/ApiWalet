<?php

namespace App\Interfaces;

use App\Models\Member;

interface MemberRepositoryInterface
{
    public function findOrFail(int $memberId): Member;
    public function findByIdWithLock(int $id): ?Member;
    public function updateBalance(Member $member, float $newBalance): void;
    public function recordTransaction(
        Member $member,
        string $type,
        float  $amount,
        float  $balanceBefore,
        float  $balanceAfter
    ): void;
}
