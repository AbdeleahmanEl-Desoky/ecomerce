<?php

declare(strict_types=1);

namespace Modules\User\Controllers\Admin;

use BasePackage\Shared\Presenters\Json;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Handlers\DeleteUserHandler;
use Modules\User\Handlers\UpdateUserHandler;
use Modules\User\Presenters\UserPresenter;
use Modules\User\Requests\Admin\CreateUserRequest;
use Modules\User\Requests\Admin\DeleteUserRequest;
use Modules\User\Requests\Admin\GetUserListRequest;
use Modules\User\Requests\Admin\GetUserRequest;
use Modules\User\Requests\Admin\UpdateUserRequest;
use Modules\User\Services\Admin\UserCRUDService;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    public function __construct(
        private UserCRUDService $userService,
        private UpdateUserHandler $updateUserHandler,
        private DeleteUserHandler $deleteUserHandler,
    ) {}

    public function index(GetUserListRequest $request): JsonResponse
    {
        $list = $this->userService->list(
            (int) $request->get('page', 1),
            (int) $request->get('per_page', 10)
        );

        return Json::item(UserPresenter::collection($list['data']), $list['pagination']);
    }

    public function show(GetUserRequest $request): JsonResponse
    {
        $item = $this->userService->get(Uuid::fromString($request->route('id')));

        $presenter = new UserPresenter($item);

        return Json::item($presenter->getData());
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $createdItem = $this->userService->create($request->createCreateUserDTO());

        $presenter = new UserPresenter($createdItem);

        return Json::item($presenter->getData());
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $command = $request->createUpdateUserCommand();
        $this->updateUserHandler->handle($command);

        $item = $this->userService->get($command->getId());

        $presenter = new UserPresenter($item);

        return Json::item($presenter->getData());
    }

    public function delete(DeleteUserRequest $request): JsonResponse
    {
        $this->deleteUserHandler->handle(Uuid::fromString($request->route('id')));

        return Json::deleted();
    }

    /**
     * Get all users including soft deleted
     */
    public function indexWithTrashed(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        
        $list = $this->userService->listWithTrashed($page, $perPage);
        return Json::item(UserPresenter::collection($list['data']), $list['pagination']);
    }

    /**
     * Get only soft deleted users
     */
    public function indexOnlyTrashed(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        
        $list = $this->userService->listOnlyTrashed($page, $perPage);
        return Json::item(UserPresenter::collection($list['data']), $list['pagination']);
    }

    /**
     * Restore a soft deleted user
     */
    public function restore(Request $request): JsonResponse
    {
        $id = Uuid::fromString($request->route('id'));
        
        $restored = $this->userService->restore($id);
        
        if ($restored) {
            return Json::success('User restored successfully');
        }
        
        return Json::error('Failed to restore user', 400);
    }

    /**
     * Permanently delete a user
     */
    public function forceDelete(Request $request): JsonResponse
    {
        $id = Uuid::fromString($request->route('id'));
        
        $deleted = $this->userService->forceDelete($id);
        
        if ($deleted) {
            return Json::success('User permanently deleted');
        }
        
        return Json::error('Failed to delete user', 400);
    }
}
