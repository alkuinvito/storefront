<?php

namespace App\Services\PageBlocks;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Validator;

abstract class BasePageBlock
{
    abstract protected function getContentValidationRules(): array;

    public function validate(array $data)
    {
        $contentRules = $this->getContentValidationRules();

        $validator = Validator::make($data, $contentRules);

        if ($validator->fails()) {
            throw new ApiException(ApiErrorCode::ErrValidation, null, 422, $validator->errors()->getMessages());
        }
    }
}
