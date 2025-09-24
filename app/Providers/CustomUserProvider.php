<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomUserProvider extends EloquentUserProvider
{
    protected $role;

    public function __construct($hasher, $model, $role = null)
    {
        parent::__construct($hasher, $model);
        $this->role = $role;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();
        $query = $model->newQuery()->where($model->getAuthIdentifierName(), $identifier);
        
        if ($this->role) {
            $query->where('role', $this->role);
        }
        
        return $query->where($model->getRememberTokenName(), $token)->first();
    }

    /**
     * Retrieve a user by the given credentials.
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return;
        }

        $model = $this->createModel();
        $query = $model->newQuery();

        foreach ($credentials as $key => $value) {
            if (! str_contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        // Filter by role if specified
        if ($this->role) {
            $query->where('role', $this->role);
        }

        return $query->first();
    }

    /**
     * Retrieve a user by their unique identifier.
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();
        $query = $model->newQuery()->where($model->getAuthIdentifierName(), $identifier);
        
        if ($this->role) {
            $query->where('role', $this->role);
        }
        
        return $query->first();
    }
}
