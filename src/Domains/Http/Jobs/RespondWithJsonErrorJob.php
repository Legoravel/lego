<?php

namespace Lego\Domains\Http\Jobs;

use Illuminate\Routing\ResponseFactory;
use Lego\Units\Job;

class RespondWithJsonErrorJob extends Job
{
    protected array $content;

    public function __construct(
        protected string $message = 'An error occurred',
        protected int $status = 400,
        protected array $headers = [],
        protected int $options = JSON_FORCE_OBJECT,
        protected array $errors = [],
    )
    {
        $this->content = [
            'message' => $message,
            'status' => $status,
            'errors' => $errors,
            'request_tracking_id' => REQUEST_TRACKING_ID
        ];
    }

    public function handle(ResponseFactory $response)
    {
        return $response->json($this->content, $this->status, $this->headers, $this->options);
    }
}
