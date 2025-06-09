<?php

declare(strict_types=1);

namespace Framework\Http\Adapter;

use GuzzleHttp\Psr7\Response;
use Framework\Http\Contract\ResponseInterface;

class ResponseGuzzleHttpAdapter extends Response implements ResponseInterface {}
