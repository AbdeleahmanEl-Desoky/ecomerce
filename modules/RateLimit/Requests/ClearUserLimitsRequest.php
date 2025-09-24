<?php

declare(strict_types=1);

namespace Modules\RateLimit\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClearUserLimitsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can clear rate limits
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|uuid|exists:users,id',
            'actions' => 'array',
            'actions.*' => 'string|in:api,auth,orders,products,admin',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.uuid' => 'User ID must be a valid UUID',
            'user_id.exists' => 'User not found',
            'actions.array' => 'Actions must be an array',
            'actions.*.string' => 'Each action must be a string',
            'actions.*.in' => 'Invalid action. Allowed actions are: api, auth, orders, products, admin',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'user ID',
            'actions' => 'actions',
            'actions.*' => 'action',
        ];
    }

    /**
     * Get the user ID from the request
     */
    public function getUserId(): string
    {
        return $this->get('user_id');
    }

    /**
     * Get the actions from the request with default values
     */
    public function getActions(): array
    {
        return $this->get('actions', ['api', 'auth', 'orders', 'products']);
    }
}
