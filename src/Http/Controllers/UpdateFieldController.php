<?php

    namespace YuriyMartini\Nova\Tools\Profile\Http\Controllers;

    use Illuminate\Auth\Access\AuthorizationException;
    use Illuminate\Http\Response;
    use YuriyMartini\Nova\Tools\Profile\Http\Requests\NovaRequest;

    class UpdateFieldController extends Controller
    {
        /**
         * List the update fields for the given resource.
         *
         * @param NovaRequest $request
         * @return Response
         * @throws AuthorizationException
         * @see \Laravel\Nova\Http\Controllers\UpdateFieldController::index
         */
        public function index(NovaRequest $request)
        {
            $resource = $this->getResource($request);

            $resource->authorizeToUpdate($request);

            return response()->json([
                'resourceId' => $this->getModel($request)->getKey(),
                'fields' => $resource->updateFieldsWithinPanels($request),
                'panels' => $resource->availablePanelsForUpdate($request),
            ]);
        }
    }
