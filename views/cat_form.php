<?php
// Текущая дата
$today = date('Y-m-d');

// Определяем это форма добавления или редактирования
$isEdit = isset($cat);
$formAction = $isEdit ? "index.php?action=update" : "index.php?action=store";
$buttonText = $isEdit ? "Сохранить изменения" : "Добавить кошку";

// Определяем ID матери, который должен быть выбран
$selectedMotherId = $cat['mother_id'] ?? null;

// Всегда массив
$selected_father_ids = $selected_father_ids ?? [];

// Массив доступных фотографий кошек
$available_photos = ['cat-1.webp', 'cat-2.webp', 'cat-3.webp', 'cat-4.webp'];
?>
<h2><?= $isEdit ? "Редактировать кошку: " . htmlspecialchars($cat['name']) : "Добавить новую кошку" ?></h2>

<form action="<?= $formAction ?>" method="post">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
    <?php endif; ?>

    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
    <?php endif; ?>

    <p>
        <label for="name">Кличка:</label>
        <input type="text" id="name" name="name" required value="<?= $isEdit ? htmlspecialchars($cat['name']) : '' ?>">
    </p>

    <p>
        <label>Пол:</label>
    <div class="radio-group-container">
        <div class="radio-option">
            <input type="radio" id="male" name="gender"
                   value="male" <?= (!$isEdit || $cat['gender'] === 'male') ? 'checked' : '' ?>>
            <label for="male">Мужской</label>
        </div>
        <div class="radio-option">
            <input type="radio" id="female" name="gender"
                   value="female" <?= ($isEdit && $cat['gender'] === 'female') ? 'checked' : '' ?>>
            <label for="female">Женский</label>
        </div>
    </div>
    </p>

    <p>
        <label for="birth_date">Дата рождения:</label>
        <input type="date" id="birth_date" name="birth_date" required
               value="<?= $isEdit ? $cat['birth_date'] : $today ?>" max="<?= $today ?>">
    </p>

    <hr>
    <h3>Фотография</h3>
    <div class="photo-selector">
        <!-- Опция "Без фото" -->
        <label class="photo-option">
            <input type="radio" name="photo_filename"
                   value="" <?= (!$isEdit || empty($cat['photo_filename'])) ? 'checked' : '' ?>>
            <img src="public/images/placeholder.webp" alt="Без фото">
        </label>

        <?php foreach ($available_photos as $photo): ?>
            <label class="photo-option">
                <input type="radio" name="photo_filename"
                       value="<?= $photo ?>" <?= ($isEdit && $cat['photo_filename'] === $photo) ? 'checked' : '' ?>>
                <img src="public/images/<?= $photo ?>" alt="<?= $photo ?>">
            </label>
        <?php endforeach; ?>
    </div>

    <hr>
    <h3>Родители</h3>
    <p>
        <label for="mother_id">Мать:</label>
        <select id="mother_id" name="mother_id">
            <option value="">Неизвестна</option>
            <?php if (!empty($mothers)): ?>
                <?php foreach ($mothers as $mother): ?>
                    <option value="<?= $mother['id'] ?>" <?= ($mother['id'] == $selectedMotherId) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mother['name']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </p>
    <p>
        <label for="father_ids">Возможные отцы:</label>
        <select id="father_ids" name="father_ids[]" multiple class="fathers-select-multiple">
            <?php if (!empty($fathers)): ?>
                <?php foreach ($fathers as $father): ?>
                    <option value="<?= $father['id'] ?>" <?= in_array($father['id'], $selected_father_ids) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($father['name']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <br><small>Удерживайте Ctrl\Cmd, чтобы выбрать несколько.</small>
    </p>
    <hr>

    <p>
        <button type="submit"><?= $buttonText ?></button>
        <a href="index.php">Отмена</a>
    </p>
</form>