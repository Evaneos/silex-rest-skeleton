<?php

namespace Evaneos\REST\API\Listeners;

use Silex\EventListener\LogListener as BaseLogListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogListener extends BaseLogListener
{
    /**
     * {@inheritdoc}
     */
    protected function logRequest(Request $request)
    {
        $context = [];

        if (null !== $route = $request->attributes->get('_route')) {
            $context['route'] = $route;
        } else {
            $context['route'] = 'n/a';
        }

        $this->logger->info('> ' . $request->getMethod() . ' ' . $request->getRequestUri(), $context);
    }

    /**
     * {@inheritdoc}
     */
    protected function logResponse(Response $response)
    {
        $context = [
            'response_code' => $response->getStatusCode(),
        ];

        if ($response instanceof RedirectResponse) {
            $this->logger->info('< ' . $response->getStatusCode() . ' ' . $response->getTargetUrl(), $context);
        } else {
            $this->logger->info('< ' . $response->getStatusCode(), $context);
        }
    }
}
