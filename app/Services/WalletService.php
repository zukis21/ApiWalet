<?php

namespace App\Services;

use App\Interfaces\MemberRepositoryInterface;
use App\Models\Member;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository
    ) {}

    public function inquiryBalance(int $memberId): array
    {
        $member = $this->memberRepository->findOrFail($memberId);

        return [
            'member_id' => $member->id,
            'name' => $member->name,
            'balance' => (float) $member->balance,
        ];
    }

    public function deposit(int $memberId, float $amount): array
    {
        return DB::transaction(function () use ($memberId, $amount) {
            $member = $this->memberRepository->findByIdWithLock($memberId);

            if (!$member) {
                throw new ModelNotFoundException();
            }

            $balanceBefore = (float) $member->balance;
            $balanceAfter = $balanceBefore + $amount;

            $this->memberRepository->updateBalance($member, $balanceAfter);
            $this->memberRepository->recordTransaction(
                $member,
                'deposit',
                $amount,
                $balanceBefore,
                $balanceAfter
            );

            return $this->buildTransactionResult($member, $amount, $balanceBefore, $balanceAfter);
        });
    }

    public function withdraw(int $memberId, float $amount): array
    {
        return DB::transaction(function () use ($memberId, $amount) {
            $member = $this->memberRepository->findByIdWithLock($memberId);

            if (!$member) {
                throw new ModelNotFoundException();
            }

            $balanceBefore = (float) $member->balance;

            if ($balanceBefore < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo tidak mencukupi untuk melakukan penarikan.',
                ]);
            }

            $balanceAfter = $balanceBefore - $amount;

            $this->memberRepository->updateBalance($member, $balanceAfter);
            $this->memberRepository->recordTransaction(
                $member,
                'withdraw',
                $amount,
                $balanceBefore,
                $balanceAfter
            );

            return $this->buildTransactionResult($member, $amount, $balanceBefore, $balanceAfter);
        });
    }

    private function buildTransactionResult(
        Member $member,
        float  $amount,
        float  $balanceBefore,
        float  $balanceAfter
    ): array {
        return [
            'member_id' => $member->id,
            'name' => $member->name,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
        ];
    }
}
