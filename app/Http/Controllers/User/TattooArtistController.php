<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusArtist;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

class TattooArtistController extends Controller
{

    public function validateUser()
    {
        try {
            throw_if(!auth()->user()->tattoo_artist()->exists(), \Exception::class, 'You are not a company');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @throws \Throwable
     */
    public function updatePrice(Request $request): \Illuminate\Http\Response
    {
        $this->validateUser();
        $validate = $request->validate($this->rulesPrice());
        $this->authApi()->user()->tattoo_artist()->update($validate);
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
        $this->validateUser();
        $validate = $request->validate($this->rulesStatus());
        $this->authApi()->user()->tattoo_artist()->update($validate);
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function updateInstagram(Request $request): \Illuminate\Http\Response
    {
        $this->validateUser();
        $validate = $request->validate($this->rulesInstagram());
        $this->authApi()->user()->tattoo_artist()->update($validate);
        return response(null)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function updateNameCompany(Request $request): \Illuminate\Http\Response
    {
        $this->validateUser();
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

    #[ArrayShape(['status' => "array"])] private function rulesStatus(): array
    {
        return [
            'status'=> [
                'required',
                'string',
                Rule::in(StatusArtist::toArray()),
            ],
        ];
    }


}
