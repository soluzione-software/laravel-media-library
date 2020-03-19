<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Http\Controllers;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;
use SoluzioneSoftware\LaravelMediaLibrary\Http\Requests\StorePendingMediaRequest;
use SoluzioneSoftware\LaravelMediaLibrary\Http\Requests\UpdatePendingMediaRequest;
use SoluzioneSoftware\LaravelMediaLibrary\Models\PendingMedia;
use SoluzioneSoftware\LaravelMediaLibrary\Traits\StoresMedia;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\DiskDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileIsTooBig;

class PendingController extends BaseController
{
    use StoresMedia;

    /**
     * @param StorePendingMediaRequest $request
     * @return JsonResponse
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(StorePendingMediaRequest $request)
    {
        // todo: use DB transaction

        $request->validated();

        /** @var PendingMedia $media */
        $media = PendingMedia::query()->create();

        $this->storePendingMedia($media);

        $data = ['id' => $media->id];

        return Response::json($data);
    }

    /**
     * @param UpdatePendingMediaRequest $request
     * @param PendingMedia $media
     * @return JsonResponse
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(UpdatePendingMediaRequest $request, PendingMedia $media)
    {
        // todo:
        //  use DB transaction
        //  Authorize action

        $request->validated();

        $this->updatePendingMedia($media);

        return Response::json();
    }

    /**
     * @param PendingMedia $media
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(PendingMedia $media)
    {
        // todo:
        //  use DB transaction
        //  Authorize action
        //  check if deleted

        $media->media()->delete();
        $media->delete();

        return Response::json();
    }
}
