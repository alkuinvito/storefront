<?php

namespace App\Http\Controllers\PageBlocks;

use App\Enums\BlockType;
use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\PageBlock;
use App\Models\Storefront;
use App\Services\PageBlocks\PageBlockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class PageBlockApiController extends Controller
{
    protected PageBlockService $pageBlockService;

    public function __construct(PageBlockService $pageBlockService)
    {
        $this->pageBlockService = $pageBlockService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Storefront $storefront)
    {
        $pageBlocks = $this->pageBlockService->getBlocks($storefront, true);
        return response()->json(['data' => $pageBlocks]);
    }

    public function show(Storefront $storefront, string $id)
    {
        $pageBlock = $this->pageBlockService->getBlockById($storefront, $id);
        return response()->json(['data' => $pageBlock]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Storefront $storefront)
    {
        $validator = Validator::make($request->only('type', 'index', 'is_active'), [
            'type' => ['required', new Enum(BlockType::class)],
            'index' => 'required|integer|min:0',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            throw new ApiException(ApiErrorCode::ErrValidation, null, 422, $validator->errors()->getMessages());
        }

        $this->pageBlockService->createBlock($storefront, $request->only('type', 'index', 'content', 'is_active'));

        return response()->json(['message' => 'Block created successfully'], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Storefront $storefront, PageBlock $block)
    {
        $validator = Validator::make($request->only('type', 'index', 'is_active'), [
            'type' => ['required', new Enum(BlockType::class)],
            'index' => 'required|integer|min:0',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            throw new ApiException(ApiErrorCode::ErrValidation, null, 422, $validator->errors()->getMessages());
        }

        $this->pageBlockService->updateBlock($block, $request->only('type', 'index', 'content', 'is_active'));

        return response()->json(['message' => 'Block updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Storefront $storefront, PageBlock $block)
    {
        $this->pageBlockService->deleteBlock($block);

        return response()->json(['message' => 'Block deleted successfully'], 200);
    }
}
