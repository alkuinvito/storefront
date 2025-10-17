<?php

namespace App\Services\PageBlocks;

use App\Enums\BlockType;
use App\Services\PageBlocks\BasePageBlock;
use Exception;

class BlockServiceFactory
{
    protected static array $map = [
        BlockType::TextBlock->value => TextBlockService::class,
    ];

    public static function getService(string $blockTypeString): BasePageBlock
    {
        if (!isset(self::$map[$blockTypeString])) {
            throw new Exception("Unsupported block type: {$blockTypeString}");
        }

        $class = self::$map[$blockTypeString];

        return app($class);
    }

    public static function getValidTypes(): array
    {
        return array_keys(self::$map);
    }
}
