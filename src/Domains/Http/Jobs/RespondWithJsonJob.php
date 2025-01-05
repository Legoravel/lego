<?php

namespace Lego\Domains\Http\Jobs;

use Illuminate\Routing\ResponseFactory;
use Lego\Units\Job;

class RespondWithJsonJob extends Job
{
    public function __construct(
        protected string|array $content,
        protected int $status = 200,
        protected array $headers = [],
        protected int $options = 0)
    {
        if (is_string($this->content)) {
            $this->content = ['message' => $content];
        }

        $this->content = array_merge($this->content, ['request_tracking_id' => REQUEST_TRACKING_ID]);
    }

    public function handle(ResponseFactory $factory)
    {
        return $factory->json($this->content, $this->status, $this->headers, $this->options);
    }
}
