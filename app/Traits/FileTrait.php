<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

trait FileTrait
{
    protected mixed $file;

    /**
     * @param Request $request
     * @return mixed
     *
     * @throws Throwable
     */
    public function getImages(Request $request): mixed
    {
        list('type'=> $type, 'path' => $path) = $request->validate([
             'type' => 'required|string',
             'path' => 'required|string',
        ]);
        match ($type) {
            'public', 'private' => $this->getFile($type, $path),
            default => abort(Response::HTTP_NOT_FOUND, 'File type is not supported'),
        };
        return $this->file;
    }

    /**
     * @throws Throwable
     */
    private function getFile(string $type, string $path, $response = true): void
    {
        self::authorize($type);
        $storage = Storage::disk($type);
        abort_if(! $storage->exists($path) && $response, Response::HTTP_NOT_FOUND, 'File not found');
        $this->file = $response ? ($storage->response($path)) : (base64_encode($storage->get($path)));
    }

    /**
     * @throws Throwable
     */
    public function getImage(string $type, string $path): mixed
    {
        $this->getFile($type, $path, false);
        return $this->file ?  "data:image/png;base64,{$this->file}" : null;
    }

    /**
     * @param string $type
     * @param $file
     * @param string $path
     * @return string|bool
     */
    public function uploadFile(string $type, $file, string $path): string|bool
    {
        self::authorize($type);
        return str_replace('', '', Storage::disk($type)->put($path, $file));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function httpResponse(Request $request): Response
    {
        $request->validate($this->rules());

        return response()->json([
            'message' => 'File uploaded successfully',
            'file_name' => $this->uploadFile($request->input('type'), $request->file('file'), 'posts/'.$this->authApi()->user()->id.'/images'),
            'type' => $request->input('type'),
        ]);
    }

    private function rules(): array
    {
        return [
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|string|in:public,private',
        ];
    }
}
