<?php

namespace App\Repositories;

use App\Interfaces\MemberRepositoryInterface;
use App\Models\Member;
use App\Models\Transaction;

class MemberRepository implements MemberRepositoryInterface
{

    public function findOrFail(int $memberId): Member
    {
        return Member::findOrFail($memberId);
    }

    public function findByIdWithLock(int $id): ?Member
    {
        return Member::lockForUpdate()->find($id);
    }

    public function updateBalance(Member $member, float $newBalance): void
    {
        $member->update(['balance' => $newBalance]);
    }

    public function recordTransaction(
        Member $member,
        string $type,
        float  $amount,
        float  $balanceBefore,
        float  $balanceAfter
    ): void {
        Transaction::create([
            'member_id' => $member->id,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
        ]);
    }
}
