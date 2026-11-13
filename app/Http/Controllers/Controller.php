<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Contracts\Docs\Swagger\ApiInfo as SwaggerDoc;

class Controller extends BaseController implements SwaggerDoc
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
