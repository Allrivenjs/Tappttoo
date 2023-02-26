<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $quotation = Quotation::create($request->validate(Quotation::validationRules));
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $quotation->images()->create([
                    'url' => $this->uploadFile('private', $image, 'quotation/images'),
                    'type' => 'private',
                ]);
            }
        }
        return response($quotation);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return Response
     */
    public function show(Quotation $quotation)
    {
        return response($quotation->load(['room', 'user', 'payment']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Models\Quotation  $quotation
     * @return Response
     */
    public function update(Request $request, Quotation $quotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return Response
     */
    public function destroy(Quotation $quotation)
    {
        //
    }
}
