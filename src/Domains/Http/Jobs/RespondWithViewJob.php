<?php

namespace Lego\Domains\Http\Jobs;

use Illuminate\Routing\ResponseFactory;
use Lego\Units\Job;

class RespondWithViewJob extends Job
{
    protected int $status;

    protected $data;

    protected array $headers;

    protected $template;

    public function __construct($template, $data = [], $status = 200, array $headers = [])
    {
        $this->template = $template;
        $this->data = $data;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function handle(ResponseFactory $factory)
    {
        return $factory->view($this->template, $this->data, $this->status, $this->headers);
    }
}
