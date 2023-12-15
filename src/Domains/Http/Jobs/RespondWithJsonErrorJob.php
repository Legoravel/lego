<?php

namespace Lego\Domains\Http\Jobs;

use Illuminate\Routing\ResponseFactory;
use Lego\Units\Job;

class RespondWithJsonErrorJob extends Job
{
    public function __construct($message = 'An error occurred', $code = 400, $status = 400, $headers = [], $options = 0)
    {
        $this->content = [
            'status' => $status,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
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
