<?php

namespace YuriyMartini\Nova\Tools\Profile\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use YuriyMartini\Nova\Tools\Profile\Http\Requests\NovaRequest;

class ResourceShowController extends Controller
{
    /**
     * @param NovaRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @see \Laravel\Nova\Http\Controllers\ResourceShowController::handle
     */
    public function handle(NovaRequest $request)
    {
        $resource = $this->getResource($request);

        $resource->authorizeTo($request, 'view');

        return response()->json([
            'panels' => $resource->availablePanelsForDetail($request),
            'resource' => $resource->serializeForDetail($request),
            'resourceId' => $this->getModel($request)->getKey(),
        ]);
    }
}
