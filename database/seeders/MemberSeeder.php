<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            ['name' => 'Budi Indonesia', 'balance' => 500000],
            ['name' => 'Sari Indonesia', 'balance' => 250000],
            ['name' => 'Ahmad Indonesia', 'balance' => 0],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }
    }
}
