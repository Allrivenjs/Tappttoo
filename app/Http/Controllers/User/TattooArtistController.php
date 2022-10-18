<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

class TattooArtistController extends Controller
{

    public function __construct()
    {
        throw_if(!auth()->user()->tattoo_artist()->exists(), \Exception::class, 'You are not a company');
    }

    /**
     * @throws \Throwable
     */
    public function updatePrice(Request $request): \Illuminate\Http\Response
    {
        $validate = $request->validate($this->rulesPrice());
        $this->authApi()->user()->update($validate);
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @return \string[][]
     */
    #[ArrayShape(['price_per_hour' => "string[]", 'base_price' => "string[]"])] protected function rulesPrice(): array
    {
        return  [
            'price_per_hour'=> [
                'numeric',
                'required',
            ],
            'base_price'=>[
                'numeric',
                'required',
            ],
        ];
    }

    public function updateStatus(Request $request): \Illuminate\Http\Response
    {
        $validate = $request->validate($this->rulesStatus());
        $this->authApi()->user()->tattoo_artist()->update($validate);
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function updateInstagram(Request $request): \Illuminate\Http\Response
    {
        $validate = $request->validate($this->rulesInstagram());
        $this->authApi()->user()->tattoo_artist()->update($validate);
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function updateNameCompany(Request $request): \Illuminate\Http\Response
    {
        $validate = $request->validate($this->rulesNameCompany());
        $this->authApi()->user()->tattoo_artist()->update($validate);
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    #[ArrayShape(['name_company' => "string[]"])] private function rulesNameCompany(): array
    {
        return  [
            'name_company'=> [
                'string',
                'required',
            ],
        ];
    }

    #[ArrayShape(['instagram' => "string[]"])] private function rulesInstagram(): array
    {
        return  [
            'instagram'=> [
                'required',
                'url',
            ],
        ];
    }


}
