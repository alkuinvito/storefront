<?php

namespace App\Services\PageBlocks;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Models\PageBlock;
use App\Models\Storefront;

class PageBlockService
{
    protected BlockServiceFactory $blockServiceFactory;

    public function __construct(BlockServiceFactory $blockServiceFactory)
    {
        $this->blockServiceFactory = $blockServiceFactory;
    }

    public function createBlock(Storefront $storefront, array $data)
    {
        $blocksCount = $storefront->pageBlocks()->get(['id'])->count();
        if ($blocksCount >= 5) {
            throw new ApiException(ApiErrorCode::ErrBadRequest, 'Maximum number of blocks reached', 400);
        }

        $blockService = $this->blockServiceFactory->getService($data['type']);
        $blockService->validate($data['content']);
        $storefront->pageBlocks()->create($data);
    }

    public function deleteBlock(PageBlock $pageBlock)
    {
        $pageBlock->delete();
    }

    public function getBlockById(Storefront $storefront, string $id)
    {
        $pageBlock = $storefront->pageBlocks()->find($id);
        if (!$pageBlock) {
            throw new ApiException(ApiErrorCode::ErrNotFound, 'Page block not found', 404);
        }

        return $pageBlock;
    }

    public function getBlocks(Storefront $storefront, bool $isActive)
    {
        return $storefront->pageBlocks()->get()->where('is_active', $isActive)->sortBy('index');
    }

    public function updateBlock(PageBlock $pageBlock, array $data)
    {
        $blockService = $this->blockServiceFactory->getService($data['type']);
        $blockService->validate($data['content']);
        $pageBlock->update($data);
    }
}
