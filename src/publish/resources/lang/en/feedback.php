<?php

return [
    'title' => 'Comment',
    'validation' => [
        'recaptchaFailed' => 'reCaptcha is not validated. Please confirm you are not a bot.'
    ],
    'form' => [
        'email' => 'Email',
        'date' => 'Date',
        'message' => 'Message'
    ],
    'preferences' => [
        'title' => 'Feedback Settings',
        'noModeration' => 'Feedback is visible just after publishing',
        'moderation' => 'Admin must confirm feedback publishing',
        'successMessage' => 'Message after successful feedback publishing',
        'notifyMe' => 'Notify me by email about new feedback'
    ]
];