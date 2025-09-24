<?php

declare(strict_types=1);

namespace Modules\RateLimit\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlacklistIpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can blacklist IPs
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ip' => 'required|ip',
            'duration_minutes' => 'integer|min:1|max:1440', // Max 24 hours
            'reason' => 'string|max:255',
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
            'duration_minutes.integer' => 'Duration must be a number',
            'duration_minutes.min' => 'Duration must be at least 1 minute',
            'duration_minutes.max' => 'Duration cannot exceed 24 hours (1440 minutes)',
            'reason.string' => 'Reason must be a string',
            'reason.max' => 'Reason cannot exceed 255 characters',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'ip' => 'IP address',
            'duration_minutes' => 'duration',
            'reason' => 'reason',
        ];
    }

    /**
     * Get the IP address from the request
     */
    public function getIp(): string
    {
        return $this->get('ip');
    }

    /**
     * Get the duration from the request with default value
     */
    public function getDuration(): int
    {
        return $this->get('duration_minutes', 60);
    }

    /**
     * Get the reason from the request with default value
     */
    public function getReason(): string
    {
        return $this->get('reason', 'Manual blacklist');
    }
}
