<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Spatie\MissingPageRedirector\RedirectsMissingPages as SpatieRedirectsMissingPages;

/**
 * Extended to keep query strings in redirects working.
 */
class RedirectsMissingPages extends SpatieRedirectsMissingPages
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = parent::handle($request, $next);

        if ($request->query->count() > 0 && $this->shouldRedirect($response) && method_exists($response, 'getTargetUrl')) {
            // make sure we do not lose the query string:
            /** @var RedirectResponse $response */
            if (! Str::contains($response->getTargetUrl(), '?')) {
                $response->setTargetUrl($response->getTargetUrl().'?'.$request->getQueryString());
            }
        }

        return $response;
    }
}
