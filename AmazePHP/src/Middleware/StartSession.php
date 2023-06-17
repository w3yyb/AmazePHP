<?php

namespace AmazePHP\Middleware;

class StartSession implements \AmazePHP\MiddlewareInterface
{
    public function process($object, \Closure $next, ...$params)
    {
        // $session =  $object->session();

        if (!config('session.enable')) {
            return $next($object);
        }

        $session =  getSession();


        cookie('XSRF-TOKEN', session()->token(), config('session.lifetime'), config('session.path'), config('session.domain'), config('session.secure'), false, config('session.same_site'));



        if (! $session->has('_token')) {
            $session->regenerateToken();
        }

        $response = $next($object);
        $this->storeCurrentUrl($object, $session);
        return $response;
    }

    /**
    * Store the current URL for the request if necessary.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Illuminate\Contracts\Session\Session  $session
    * @return void
    */
    protected function storeCurrentUrl($request, $session)
    {
        if ($request->method() === 'GET' &&
            // $request->route() instanceof Route &&

            ! $request->ajax() &&
            ! $request->prefetch()) {
            $session->setPreviousUrl($request->fullUrl());
        }
    }
}
