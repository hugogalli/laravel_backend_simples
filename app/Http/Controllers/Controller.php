<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(
 *   title="SH3 Backend API",
 *   version="1.0",
 *   description="API de Atendimentos feita por Hugo Galli para SH3",
 *   @OA\Contact(
 *     email="hugo.galli.r@gmail.com",
 *     name="Hugo Galli"
 *   )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
