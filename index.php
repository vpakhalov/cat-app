<?php

use Random\RandomException;

require_once __DIR__ . '/vendor/autoload.php';

session_start();

/**
 * Генерирует, сохраняет в сессию и возвращает CSRF-токен.
 * @return string
 * @throws RandomException
 */
function generateCsrfToken(): string
{
    // Генерируем токен, только если его еще нет в сессии
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Проверяет CSRF-токен из POST-запроса.
 */
function verifyCsrfToken(): void
{
    // Проверяем, что токен в сессии и в POST существует и они равны
    // в противном случае удаляем
    if (
        empty($_SESSION['csrf_token']) ||
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        unset($_SESSION['csrf_token']);
        die('CSRF token validation failed.');
    }
    // После успешной проверки генерируем токен заново для следующего запроса
    unset($_SESSION['csrf_token']);
}

// Подключаем конфиг и создаем соединение с БД
$dbConfig = require __DIR__ . '/config/db.php';
try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8",
        $dbConfig['user'],
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Определяем запрошенное действие (маршрут)
$action = $_GET['action'] ?? 'index';
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $action = 'delete';
}

$controller = new  App\Controllers\CatController($pdo);

// Маршрутизатор
switch ($action) {
    case 'add':
        $controller->add();
        break;
    case 'store':
        verifyCsrfToken();
        $controller->store();
        break;
    case 'edit':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $controller->edit($id);
        } else {
            header("Location: index.php");
            exit();
        }
        break;
    case 'update':
        verifyCsrfToken();
        $controller->update();
        break;
    case 'delete':
        verifyCsrfToken();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $controller->delete($id);
        } else {
            header("Location: index.php");
            exit();
        }
        break;
    default:
        $controller->index();
        break;
}