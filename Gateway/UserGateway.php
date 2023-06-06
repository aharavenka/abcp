<?php

namespace Gateway;

use PDO;

class UserGateway
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Возвращает список пользователей старше заданного возраста.
     * @param int $ageFrom
     * @param int $limit
     * @return array
     */
    public function getUsersOlderThan(int $ageFrom, int $limit): array
    {
        $stmt = $this->pdo->prepare("SELECT id, name, lastName, `from`, age, settings FROM Users WHERE age > :ageFrom LIMIT :limit");
        $stmt->bindParam(':ageFrom', $ageFrom, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            if (isset($row['settings'])) {
                $settings = json_decode($row['settings'], true);
                $key = $settings['key'] ?? null;
            } else {
                $key = null;
            }
            $users[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'lastName' => $row['lastName'],
                'from' => $row['from'],
                'age' => $row['age'],
                'key' => $key,
            ];
        }

        return $users;
    }

    /**
     * Возвращает пользователя по имени.
     * @param array $names
     * @return array|null
     */
    public function getUsersByNames(array $names): ?array
    {
        $in  = str_repeat('?,', count($names) - 1) . '?';
        $stmt = $this->pdo->prepare("SELECT id, name, lastName, `from`, age FROM Users WHERE name IN ($in)");
        $stmt->execute($names);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            $users[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'lastName' => $row['lastName'],
                'from' => $row['from'],
                'age' => $row['age'],
            ];
        }

        return $users;
    }

    /**
     * Добавляет пользователей в базу данных.
     * Только нужна ли тут транзакция?
     * @param array $users
     * @return array
     */
    public function addUsers(array $users): array
    {
        $ids = [];
        $this->pdo->beginTransaction();
        foreach ($users as $user) {
            try {
                $ids[] = $this->addUser($user['name'], $user['lastName'], $user['age']);
            } catch (\Exception $e) {
                $this->pdo->rollBack();
            }
        }

        return $ids;
    }

    /**
     * Добавляет пользователя в базу данных.
     * @param string $name
     * @param string $lastName
     * @param int $age
     * @return int
     */
    public function addUser(string $name, string $lastName, int $age): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO Users (name, lastName, age) VALUES (:name, :lastName, :age)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':age', $age, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }
}
