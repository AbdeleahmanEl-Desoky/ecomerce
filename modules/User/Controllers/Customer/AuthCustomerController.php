<?php

declare(strict_types=1);

namespace Modules\User\Controllers\Customer;

use BasePackage\Shared\Presenters\Json;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\User\Handlers\DeleteUserHandler;
use Modules\User\Handlers\UpdateUserHandler;
use Modules\User\Models\User;
use Modules\User\Presenters\AuthPresenter;
use Modules\User\Presenters\UserPresenter;
use Modules\User\Requests\Admin\AdminLoginRequest;
use Modules\User\Requests\Customer\CustomerLoginRequest;
use Modules\User\Requests\Customer\LoginRequest;
use Modules\User\Requests\Customer\RegisterRequest;
use Modules\User\Requests\Customer\UpdateUserRequest;
use Modules\User\Services\Customer\UserCustomerService;
use Modules\User\Services\UserCRUDService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthCustomerController extends Controller
{
    public function __construct(
        private UserCustomerService $userService,
        private UpdateUserHandler $updateUserHandler,
        private DeleteUserHandler $deleteUserHandler,
    ) {}
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $createdItem = $this->userService->create($request->createCreateUserDTO());

        $presenter = new AuthPresenter($createdItem);

        return Json::item($presenter->getData());
    }

    /**
     * Login user and return JWT token
     */
    public function login(CustomerLoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        try {
            $user = $this->userService->login($credentials);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid customer credentials'
                ], 401);
            }

            $presenter = new AuthPresenter($user);

            return Json::item($presenter->getData());
            
        } catch (\Exception $e) {
            return Json::error( $e->getMessage(), 500, 'Could not create token' );
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile(): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $presenter = new AuthPresenter($user, (string) JWTAuth::getToken());
            return Json::item($presenter->getData());

        } catch (JWTException $e) {
            return Json::error( $e->getMessage(), 401, 'Token is invalid or expired');
        }
    }
    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $command = $request->createUpdateUserCommand();
        $this->updateUserHandler->handle($command);

        $item = $this->userService->get($command->getId());

        $presenter = new UserPresenter($item);

        return Json::item($presenter->getData());
    }
    /**
     * Logout user (invalidate token)
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return Json::success('User logged out successfully');

        } catch (JWTException $e) {
            return Json::error( $e->getMessage(), 500, 'Could not invalidate token');
        }
    }
}
