<h2>Список кошек</h2>
<a href="index.php?action=add">Добавить кошку</a>
<br><br>

<fieldset style="width: 837px">
    <legend>Фильтр</legend>
    <form method="get" action="index.php">
        <table width="100%">
            <tr>
                <td>
                    <label for="filter_gender">Пол:</label>
                    <select name="filter_gender" id="filter_gender">
                        <option value="">Любой</option>
                        <option value="male" <?= (($_GET['filter_gender'] ?? '') === 'male') ? 'selected' : '' ?>>
                            Мужской
                        </option>
                        <option value="female" <?= (($_GET['filter_gender'] ?? '') === 'female') ? 'selected' : '' ?>>
                            Женский
                        </option>
                    </select>
                </td>
                <td>
                    <label for="filter_age_from">Возраст от:</label>
                    <input type="number" name="filter_age_from" id="filter_age_from" min="0" class="filter-input-short"
                           value="<?= htmlspecialchars($_GET['filter_age_from'] ?? '') ?>">
                </td>
                <td>
                    <label for="filter_age_to">до:</label>
                    <input type="number" name="filter_age_to" id="filter_age_to" min="0" class="filter-input-short"
                           value="<?= htmlspecialchars($_GET['filter_age_to'] ?? '') ?>">
                </td>
                <td>
                    <button type="submit">Применить</button>
                    <a href="index.php">Сбросить</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
<br>

<div class="cards-container">
    <?php if (empty($cats)): ?>

        <div class="no-cats-message">
            <p>Пока нет ни одной кошки или по вашему фильтру ничего не найдено</p>
        </div>

    <?php else: ?>
        <?php foreach ($cats as $cat): ?>
            <?php
            // Определяем какое фото показывать
            $photo_path = !empty($cat['photo_filename'])
                ? 'public/images/' . $cat['photo_filename']
                : 'public/images/placeholder.webp';
            ?>

            <div class="cat-card">
                <div class="card-content-wrapper">
                    <div class="card-header">
                        <h3><?= htmlspecialchars($cat['name']) ?></h3>
                    </div>
                    <div class="card-body">
                        <p><strong>ID:</strong> <?= $cat['id'] ?></p>
                        <p><strong>Пол:</strong> <?= $cat['gender'] === 'male' ? 'Мужской' : 'Женский' ?></p>
                        <p><strong>Возраст:</strong> <?= $cat['age'] ?> лет</p>
                        <p><strong>Мать:</strong> <?= htmlspecialchars($cat['mother_name'] ?? '---') ?></p>
                    </div>
                    <div class="card-footer">
                        <a href="index.php?action=edit&id=<?= $cat['id'] ?>">Редактировать</a>
                        <form method="post" action="index.php" class="form-inline"
                              onsubmit="return confirm('Вы уверены?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <button type="submit" class="btn-delete">Удалить</button>
                        </form>
                    </div>
                </div>
                <div class="card-photo-wrapper">
                    <img src="<?= $photo_path ?>" alt="Фото кошки <?= htmlspecialchars($cat['name']) ?>"
                         class="card-photo">
                </div>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>
</div>