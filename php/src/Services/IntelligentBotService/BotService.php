<?php

declare(strict_types=1);

namespace App\Services\IntelligentBotService;

use App\Services\IntelligentBotService\Dto\RequestDto;
use App\Services\IntelligentBotService\Dto\ResponseDto;
use App\Services\IntelligentBotService\Handlers\Interfaces\MessageHandlerInterface;
use App\Services\IntelligentBotService\Pipeline\HandlerPipeline;
use InvalidArgumentException;

/**
 * Class BotService
 * @package Services\IntelligentBotService
 */
readonly class BotService
{
    /**
     * @param MessageHandlerInterface[] $handlers
     * @param HandlerPipeline $handlerPipeline
     */
    public function __construct(
        array $handlers = [],
        private HandlerPipeline $handlerPipeline = new HandlerPipeline(),
    ) {
        foreach ($handlers as $handler) {
            if (!$handler instanceof MessageHandlerInterface) {
                throw new InvalidArgumentException('Handler must implement MessageHandlerInterface');
            }
            $this->handlerPipeline->addHandler($handler);
        }
    }

    public function processRequest(RequestDto $request): ResponseDto
    {
        return $this->handlerPipeline->process($request);
    }
}
