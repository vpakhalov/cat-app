<?php

namespace App\Models;

use Exception;
use PDO;

class Cat
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT c.*, TIMESTAMPDIFF(YEAR, c.birth_date, CURDATE()) AS age, m.name as mother_name
                FROM cats c
                LEFT JOIN cats m ON c.mother_id = m.id";

        $whereClauses = [];
        $params = [];

        if (!empty($filters['gender'])) {
            $whereClauses[] = "c.gender = :gender";
            $params['gender'] = $filters['gender'];
        }
        if (!empty($filters['age_from'])) {
            $whereClauses[] = "TIMESTAMPDIFF(YEAR, c.birth_date, CURDATE()) >= :age_from";
            $params['age_from'] = (int)$filters['age_from'];
        }
        if (!empty($filters['age_to'])) {
            $whereClauses[] = "TIMESTAMPDIFF(YEAR, c.birth_date, CURDATE()) <= :age_to";
            $params['age_to'] = (int)$filters['age_to'];
        }
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $sql .= " ORDER BY c.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получает список кошек указанного пола для выбора родителя
    public function getByGender(string $gender): array
    {
        $sql = "SELECT id, name FROM cats WHERE gender = :gender ORDER BY name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['gender' => $gender]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получает массив ID потенциальных отцов для указанного котёнка
    public function getPotentialFatherIds(int $kittenId): array
    {
        $sql = "SELECT father_id FROM kitten_possible_fathers WHERE kitten_id = :kitten_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['kitten_id' => $kittenId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    //  Создает новую кошку и связывает ее с родителями
    public function create(array $data): bool
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO cats (name, gender, birth_date, mother_id, photo_filename) 
                    VALUES (:name, :gender, :birth_date, :mother_id, :photo_filename)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'name' => $data['name'],
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'],
                'mother_id' => $data['mother_id'] ?: null,
                'photo_filename' => $data['photo_filename'] ?: null,
            ]);

            // Вставка записей о возможных отцах
            $kittenId = $this->pdo->lastInsertId();
            if (!empty($data['father_ids'])) {
                $sqlFathers = "INSERT INTO kitten_possible_fathers (kitten_id, father_id) VALUES (:kitten_id, :father_id)";
                $stmtFathers = $this->pdo->prepare($sqlFathers);
                foreach ($data['father_ids'] as $fatherId) {
                    $stmtFathers->execute(['kitten_id' => $kittenId, 'father_id' => $fatherId]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // Обновляет информацию о кошке и ее родительские связи
    public function update(int $id, array $data): bool
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "UPDATE cats SET name = :name, gender = :gender, birth_date = :birth_date, mother_id = :mother_id, photo_filename = :photo_filename 
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'],
                'mother_id' => $data['mother_id'] ?: null,
                'photo_filename' => $data['photo_filename'] ?: null,
            ]);

            // Удаляем старые связи отцов, затем добавляем новые
            $this->pdo->prepare("DELETE FROM kitten_possible_fathers WHERE kitten_id = :id")->execute(['id' => $id]);

            if (!empty($data['father_ids'])) {
                $sqlInsertFathers = "INSERT INTO kitten_possible_fathers (kitten_id, father_id) VALUES (:kitten_id, :father_id)";
                $stmtInsert = $this->pdo->prepare($sqlInsertFathers);
                foreach ($data['father_ids'] as $fatherId) {
                    $stmtInsert->execute(['kitten_id' => $id, 'father_id' => $fatherId]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // Получает информацию о кошке по ее ID
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM cats WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Удаляет кошку из БД по ее ID
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM cats WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}