<?php

namespace Core;

interface Middleware
{
    /**
     * Maneja la solicitud entrante.
     *
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function handle(Request $request, callable $next);
}
