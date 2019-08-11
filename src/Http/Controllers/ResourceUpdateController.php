<?php

    namespace YuriyMartini\Nova\Tools\Profile\Http\Controllers;

    use Illuminate\Auth\Access\AuthorizationException;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\DB;
    use Laravel\Nova\Actions\ActionEvent;
    use YuriyMartini\Nova\Tools\Profile\Http\Requests\NovaRequest;

    class ResourceUpdateController extends Controller
    {
        /**
         * Create a new resource.
         *
         * @param NovaRequest $request
         * @return JsonResponse
         * @throws AuthorizationException
         * @see \Laravel\Nova\Http\Controllers\ResourceUpdateController::handle
         */
        public function handle(NovaRequest $request)
        {
            $resource = $this->getResource($request);

            $resource->authorizeToUpdate($request);

            $request->route()->setParameter('resourceId', $resource->model()->getKey());

            $resource::validateForUpdate($request);

            $model = DB::transaction(function () use ($request, $resource) {
                $model = $this->getModel($request);

                if ($this->modelHasBeenUpdatedSinceRetrieval($request, $model)) {
                    return response('', 409)->throwResponse();
                }

                [$model, $callbacks] = $resource::fillForUpdate($request, $model);

                ActionEvent::forResourceUpdate($request->user(), $model)->save();

                /** @var Model $model */
                $model->save();

                collect($callbacks)->each->__invoke();

                return $model;
            });

            return response()->json([
                'id' => $model->getKey(),
                'resource' => $model->attributesToArray(),
                'redirect' => '/profile', // todo: make it dynamic
            ]);
        }

        /**
         * Determine if the model has been updated since it was retrieved.
         *
         * @param NovaRequest $request
         * @param Model $model
         * @return bool
         * @see \Laravel\Nova\Http\Controllers\ResourceUpdateController::modelHasBeenUpdatedSinceRetrieval
         */
        protected function modelHasBeenUpdatedSinceRetrieval(NovaRequest $request, $model)
        {
            $column = $model->getUpdatedAtColumn();

            if (! $model->{$column}) {
                return false;
            }

            return $request->input('_retrieved_at') && $model->usesTimestamps() && $model->{$column}->gt(
                    Carbon::createFromTimestamp($request->input('_retrieved_at'))
                );
        }
    }
