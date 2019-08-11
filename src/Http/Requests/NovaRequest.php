<?php

    namespace YuriyMartini\Nova\Tools\Profile\Http\Requests;

    use Laravel\Nova\Http\Requests\NovaRequest as OriginalNovaRequestAlias;

    class NovaRequest extends OriginalNovaRequestAlias
    {
        use InteractsWithResources;
    }
