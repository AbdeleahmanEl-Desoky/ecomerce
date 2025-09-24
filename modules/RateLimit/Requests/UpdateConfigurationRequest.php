<?php

declare(strict_types=1);

namespace Modules\RateLimit\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can update rate limit configuration
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'limiter' => 'required|string|in:api,auth,orders,products,admin,guest',
            'max_attempts' => 'required|integer|min:1|max:1000',
            'decay_minutes' => 'required|integer|min:1|max:60',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'limiter.required' => 'Limiter type is required',
            'limiter.string' => 'Limiter must be a string',
            'limiter.in' => 'Invalid limiter type. Allowed types are: api, auth, orders, products, admin, guest',
            'max_attempts.required' => 'Maximum attempts is required',
            'max_attempts.integer' => 'Maximum attempts must be a number',
            'max_attempts.min' => 'Maximum attempts must be at least 1',
            'max_attempts.max' => 'Maximum attempts cannot exceed 1000',
            'decay_minutes.required' => 'Decay minutes is required',
            'decay_minutes.integer' => 'Decay minutes must be a number',
            'decay_minutes.min' => 'Decay minutes must be at least 1',
            'decay_minutes.max' => 'Decay minutes cannot exceed 60',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'limiter' => 'limiter type',
            'max_attempts' => 'maximum attempts',
            'decay_minutes' => 'decay minutes',
        ];
    }

    /**
     * Get the limiter type from the request
     */
    public function getLimiter(): string
    {
        return $this->get('limiter');
    }

    /**
     * Get the max attempts from the request
     */
    public function getMaxAttempts(): int
    {
        return $this->get('max_attempts');
    }

    /**
     * Get the decay minutes from the request
     */
    public function getDecayMinutes(): int
    {
        return $this->get('decay_minutes');
    }
}
