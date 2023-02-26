<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusArtist;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

class TattooArtistController extends Controller
{
    public function __construct()
    {
        self::validateUser();
    }

    public static function validateUser()
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


    public function assignImages(Request $request): \Illuminate\Http\Response
    {
        $validate = $request->validate($this->rulesImages());
        if ($request->hasFile('images')) {
            $tattoo_artist = User::query()->find($this->authApi()->user()->getAuthIdentifier())->tattoo_artist();
            dd($tattoo_artist);
            if ($tattoo_artist->images()->exists()) {
                $tattoo_artist->images()->delete();
            }
            foreach ($request->file('images') as $image) {
                $tattoo_artist->images()->create([
                    'url' => $this->uploadFile('public', $image, 'TattooArtist/images'),
                    'type' => 'public',
                ]);
            }
        }
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

    #[ArrayShape(['images' => "string", 'images.*' => "string"])] private function rulesImages(): array
    {
        return [
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'required|image',
        ];
    }
}
