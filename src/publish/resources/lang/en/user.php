<?php

return [
    'title' => 'Users',
    'createForm' => [
        'title' => 'Create New User',
        'login' => 'Email/Login',
        'name' => 'User Name',
        'role' => 'Role',
        'password' => 'Password',
        'confirmPassword' => 'Confirm Password'
    ],
    'editForm' => [
        'title' => 'Edit User'
    ],
    'list' => 'User List',
    'changePasswordForm' => [
        'title' => 'Change Password',
        'currentPassword' => 'Current Password',
        'newPassword' => 'New Password',
        'confirmNewPassword' => 'Confirm New Password',
        'action' => 'Change Password'
    ],
    'messages' => [
        'success' => [
            'created' => 'User was created successfully',
            'updated' => 'User was updated successfully',
            'deleted' => 'User was deleted successfully',
            'activated' => 'User access was granted',
            'deactivated' => 'User access was denied',
            'passwordUpdated' => 'Password was updated successfully'
        ]
    ]
];
