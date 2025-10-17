<?php

namespace App\Services\PageBlocks;

use App\Services\PageBlocks\BasePageBlock;

class TextBlockService extends BasePageBlock
{
    protected function getContentValidationRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'text' => 'nullable|string|max:255',
            'title_font_size' => 'required|integer|in:8,12,14,20,24,32,40,48,64,72,80,96',
            'title_font_family' => 'required|string|in:Inter,Merriweather,Pacifico,Roboto,Roboto Mono',
            'text_font_size' => 'required_with:text|integer|in:8,12,14,20,24,32,40,48,64,72,80,96',
            'text_font_family' => 'required_with:text|string|in:Inter,Merriweather,Pacifico,Roboto,Roboto Mono',
        ];
    }
}
