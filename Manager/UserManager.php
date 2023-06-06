<?php

namespace Manager;

use Gateway\UserGateway;

class UserManager
{
    const USER_LIMIT = 10;

    private UserGateway $userGateway;

    public function __construct(UserGateway $userGateway)
    {
        $this->userGateway = $userGateway;
    }

    /**
     * Возвращает пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    public function getUsersOlderThan(int $ageFrom): array
    {
        return $this->userGateway->getUsersOlderThan($ageFrom, self::USER_LIMIT);
    }

    /**
     * Возвращает пользователей по списку имен.
     * @param array $names
     * @return array
     */
    public function getUsersByNames(array $names): array
    {
        return $this->userGateway->getUsersByNames($names);
    }

    /**
     * Добавляет пользователей в базу данных.
     * @param array $users
     * @return array
     */
    public function addUsers(array $users): array
    {
        return $this->userGateway->addUsers($users);
    }
}
