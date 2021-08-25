<?php

return [
    'title' => 'Користувачі',
    'createForm' => [
        'title' => 'Створити нового користувача',
        'login' => 'Email/Логін',
        'name' => "Ім'я користувача",
        'role' => 'Роль',
        'password' => 'Пароль',
        'confirmPassword' => 'Підтвердження паролю'
    ],
    'editForm' => [
        'title' => 'Редагувати дані користувача'
    ],
    'list' => 'Список корисувачів',
    'changePasswordForm' => [
        'title' => 'Змінити пароль',
        'currentPassword' => 'Поточний пароль',
        'newPassword' => 'Новий пароль',
        'confirmNewPassword' => 'Підтвердження нового паролю',
        'action' => 'Змінити пароль'
    ],
    'messages' => [
        'success' => [
            'created' => 'Новий користувач успішно створений',
            'updated' => 'Дані користувача оновлені',
            'deleted' => 'Користувач успішно видалений',
            'activated' => 'Доступ користувача відкрито',
            'deactivated' => 'Доступ користувача заблоковано',
            'passwordUpdated' => 'Пароль успішно оновлено'
        ]
    ]
];
