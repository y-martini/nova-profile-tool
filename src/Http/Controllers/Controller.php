<?php

    namespace YuriyMartini\Nova\Tools\Profile\Http\Controllers;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Routing\Controller as BaseController;
    use Laravel\Nova\Http\Requests\NovaRequest;
    use Laravel\Nova\Resource;

    class Controller extends BaseController
    {
        /**
         * @param NovaRequest $request
         * @return Resource
         */
        protected function getResource(NovaRequest $request)
        {
            return $request->newResourceWith($this->getModel($request));
        }

        /**
         * @param NovaRequest $request
         * @return Model
         */
        protected function getModel(NovaRequest $request)
        {
            return $request->user();
        }
    }
