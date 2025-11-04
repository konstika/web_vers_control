<h1>Добро пожаловать, <?php echo htmlspecialchars($login); ?>!</h1>

<p>Это ваша **главная страница** и список проектов.</p>

<div style="border: 1px solid #ccc; padding: 15px; margin-top: 20px;">
    <h3>Нет доступных проектов</h3>
    <p>Чтобы начать работу, создайте свой первый проект:</p>
    <a href="/project/new" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
        Создать новый проект
    </a>
</div>

<?php
// В будущем здесь будет логика отображения:
/* if (count($projects) > 0) {
    echo '<h2>Ваши проекты:</h2>';
    // foreach ($projects as $project) { ... }
}
*/
?>