<?php

declare(strict_types=1);

namespace Framework\Http\Adapter;

use GuzzleHttp\Psr7\Request;
use Framework\Http\Contract\RequestInterface;

class RequestGuzzleHttpAdapter extends Request implements RequestInterface {}
