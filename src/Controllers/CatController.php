<?php

namespace App\Controllers;

use App\Models\Cat;
use JetBrains\PhpStorm\NoReturn;
use PDO;

class CatController
{
    private PDO $pdo;
    private Cat $catModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->catModel = new Cat($this->pdo);
    }

    // Отображение списка всех кошек с учётом филтра
    public function index(): void
    {
        // Собираем параметры фильтра из GET запроса
        $filters = [
            'gender' => $_GET['filter_gender'] ?? null,
            'age_from' => $_GET['filter_age_from'] ?? null,
            'age_to' => $_GET['filter_age_to'] ?? null
        ];

        // Передаем фильтры в модель для получения отфильтрованного списка
        $cats = $this->catModel->getAll($filters);

        $view = 'cat_list.php';
        require __DIR__ . '/../../views/layout.php';
    }

    // Показывает форму добавления новой кошки
    public function add(): void
    {
        $mothers = $this->catModel->getByGender('female');
        $fathers = $this->catModel->getByGender('male');

        $view = 'cat_form.php';
        require __DIR__ . '/../../views/layout.php';
    }

    // Сохраняет новую кошку в БД
    #[NoReturn] public function store(): void
    {
        if (!empty($_POST['name'])) {
            $data = [
                'name' => $_POST['name'],
                'gender' => $_POST['gender'],
                'birth_date' => $_POST['birth_date'],
                'mother_id' => $_POST['mother_id'] ?? null,
                'father_ids' => $_POST['father_ids'] ?? [],
                'photo_filename' => $_POST['photo_filename'] ?? null,
            ];
            $this->catModel->create($data);
        }
        header("Location: index.php");
        exit();
    }

    // Показывает форму для редактирования кошки
    public function edit(int $id): void
    {
        $cat = $this->catModel->getById($id);
        if (!$cat) {
            header("Location: index.php");
            exit();
        }

        // Получаем списки родителей, исключая саму редактируемую кошку
        $mothers = array_filter($this->catModel->getByGender('female'), fn($m) => $m['id'] != $id);
        $fathers = array_filter($this->catModel->getByGender('male'), fn($f) => $f['id'] != $id);

        // Получаем ID уже выбранных отцов для этой кошки
        $selected_father_ids = $this->catModel->getPotentialFatherIds($id);

        $view = 'cat_form.php';
        require __DIR__ . '/../../views/layout.php';
    }

    // Обновляет данные кошки в БД
    #[NoReturn] public function update(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id && !empty($_POST['name'])) {
            $data = [
                'name' => $_POST['name'],
                'gender' => $_POST['gender'],
                'birth_date' => $_POST['birth_date'],
                'mother_id' => $_POST['mother_id'] ?? null,
                'father_ids' => $_POST['father_ids'] ?? [],
                'photo_filename' => $_POST['photo_filename'] ?? null,
            ];
            $this->catModel->update($id, $data);
        }
        header("Location: index.php");
        exit();
    }

    #[NoReturn] public function delete(int $id): void
    {
        $this->catModel->delete($id);

        header("Location: index.php");
        exit();
    }
}