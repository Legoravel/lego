<?php

namespace Lego\Domains\Http\Jobs;

use Illuminate\Routing\ResponseFactory;
use Lego\Units\Job;

class RespondWithJsonErrorJob extends Job
{
    public function __construct(
        string $message = 'An error occurred',
        int $code = 400,
        int $status = 400,
        array $headers = [],
        int $options = 0,
        array $errors = []
    )
    {
        $this->content = [
            'message' => $message,
            'status' => $status,
            'code' => $code,
            'errors' => $errors,
        ];

        $this->status = $status;
        $this->headers = $headers;
        $this->options = $options;
    }

    public function handle(ResponseFactory $response)
    {
        return $response->json($this->content, $this->status, $this->headers, $this->options);
    }
}
