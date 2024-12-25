<?php

namespace Lego\Domains\Http\Jobs;

use Illuminate\Routing\ResponseFactory;
use Lego\Units\Job;

class RespondWithJsonJob extends Job
{
    protected int $status;

    protected $content;

    protected array $headers;

    protected $options;

    public function __construct($content, int $status = 200, array $headers = [], int $options = 0)
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
        $this->options = $options;
    }

    public function handle(ResponseFactory $factory)
    {
        $response = [
            'data' => $this->content,
            'status' => $this->status,
        ];

        return $factory->json($response, $this->status, $this->headers, $this->options);
    }
}
