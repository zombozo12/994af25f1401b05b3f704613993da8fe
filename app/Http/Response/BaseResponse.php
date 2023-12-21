<?php

namespace App\Http\Response;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;

abstract class BaseResponse implements Responsable
{
    public array $data;
    public string $request_id;
    public Carbon $start_time;
    public string $elapse_time;
    public bool $is_error;

    public function toResponse($request)
    {
        return $request->json();
    }
}
