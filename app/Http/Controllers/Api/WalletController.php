<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    //  check saldo / inquiry
    public function inquiry(int $memberId): JsonResponse
    {
        $data = $this->walletService->inquiryBalance($memberId);

        return $this->successResponse('Inquiry balance berhasil.', $data);
    }


    public function deposit(DepositRequest $request): JsonResponse
    {
        $memberId = $request->integer('member_id');
        $amount = (float) $request->input('amount');

        $data = $this->walletService->deposit($memberId, $amount);

        return $this->successResponse('Deposit berhasil.', $data);
    }

    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        $memberId = $request->integer('member_id');
        $amount = (float) $request->input('amount');

        $data = $this->walletService->withdraw($memberId, $amount);

        return $this->successResponse('Withdraw berhasil.', $data);
    }

    private function successResponse(string $message, array $data): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ]);
    }
}
