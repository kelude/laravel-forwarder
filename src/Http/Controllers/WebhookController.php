<?php

namespace Kelude\Forwarder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kelude\Forwarder\Contracts\HandlesWebhooks;
use Kelude\Forwarder\Events\WebhookHandled;
use Kelude\Forwarder\Events\WebhookReceived;
use Kelude\Forwarder\Http\Middleware\VerifyWebhookSignature;

class WebhookController extends Controller
{
    public function __construct()
    {
        if (config('forwarder.webhook.secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    /**
     * Handle a Forwarder webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Kelude\Forwarder\Contracts\HandlesWebhooks  $handler
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, HandlesWebhooks $handler)
    {
        WebhookReceived::dispatch($request);

        $response = $handler->handle($request);

        WebhookHandled::dispatch($request);

        return $response;
    }
}
