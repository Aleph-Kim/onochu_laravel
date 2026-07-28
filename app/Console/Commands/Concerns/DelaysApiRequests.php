<?php

namespace App\Console\Commands\Concerns;

trait DelaysApiRequests
{
    // API 요청 전 무작위 딜레이 추가
    private function delay(): void
    {
        usleep(random_int(1_000_000, 3_000_000)); // 1~3초
    }
}
