<?php

declare(strict_types=1);

namespace Modules\RateLimit\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveFromBlacklistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can remove IPs from blacklist
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ip' => 'required|ip',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'ip.required' => 'IP address is required',
            'ip.ip' => 'Please provide a valid IP address',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'ip' => 'IP address',
        ];
    }

    /**
     * Get the IP address from the request
     */
    public function getIp(): string
    {
        return $this->get('ip');
    }
}
