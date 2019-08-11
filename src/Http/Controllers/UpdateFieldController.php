<?php

    namespace YuriyMartini\Nova\Tools\Profile\Http\Controllers;

    use Illuminate\Auth\Access\AuthorizationException;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
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
            /** @var Model $model */
            $model = auth()->user();

            $resource = $request->newResourceWith($model);

            $resource->authorizeToUpdate($request);

            return response()->json([
                'fields' => $resource->updateFieldsWithinPanels($request),
                'panels' => $resource->availablePanelsForUpdate($request),
            ]);
        }
    }
