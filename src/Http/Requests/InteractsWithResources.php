<?php

    namespace YuriyMartini\Nova\Tools\Profile\Http\Requests;

    use Laravel\Nova\Http\Requests\InteractsWithResources as NovaInteractsWithResources;
    use Laravel\Nova\Nova;

    trait InteractsWithResources
    {
        use NovaInteractsWithResources;

        /**
         * Get the class name of the resource being requested.
         *
         * @return mixed
         * @see \Laravel\Nova\Http\Requests\InteractsWithResources::resource
         */
        public function resource()
        {
            return tap( Nova::resourceForKey('users'), // todo: make it dynamic
                function ($resource) {
                    abort_if(is_null($resource), 404);
                });
        }
    }

