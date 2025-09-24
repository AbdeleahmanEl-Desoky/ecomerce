<?php

declare(strict_types=1);

namespace Modules\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // No additional validation needed for cancellation
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
