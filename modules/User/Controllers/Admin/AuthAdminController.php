<?php

declare(strict_types=1);

namespace Modules\User\Controllers\Admin;

use BasePackage\Shared\Presenters\Json;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\Handlers\DeleteUserHandler;
use Modules\User\Handlers\UpdateUserHandler;
use Modules\User\Presenters\AuthPresenter;
use Modules\User\Presenters\UserPresenter;
use Modules\User\Requests\Admin\AdminLoginRequest;
use Modules\User\Requests\Admin\UpdateUserRequest;
use Modules\User\Services\Admin\UserCRUDService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthAdminController extends Controller
{
    public function __construct(
        private UserCRUDService $userService,
        private UpdateUserHandler $updateUserHandler,
        private DeleteUserHandler $deleteUserHandler,
    ) {}

    /**
     * Login user and return JWT token
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        try {
            $user = $this->userService->login($credentials);
            
            if (!$user) {
                return Json::error('Invalid Admin credentials',401);
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
