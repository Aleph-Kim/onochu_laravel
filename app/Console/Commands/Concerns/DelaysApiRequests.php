<?php

namespace App\Console\Commands\Concerns;

trait DelaysApiRequests
{
    // 연속 API 호출 시 레이트리밋 회피용 무작위 딜레이
    private function delay(): void
    {
        usleep(random_int(1_000_000, 3_000_000)); // 1~3초
    }
}
