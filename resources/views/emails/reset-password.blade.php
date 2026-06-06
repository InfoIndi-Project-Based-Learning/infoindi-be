<!DOCTYPE html>
<html>
<head>
    <title>Reset Password Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Hello {{ $user->name }},</h2>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>
        <a href="{{ $url }}" style="display: inline-block; padding: 10px 20px; background-color: #000; color: #fff; text-decoration: none; border-radius: 5px;">Reset Password</a>
    </p>
    <p>This password reset link will expire in 60 minutes.</p>
    <p>If you did not request a password reset, no further action is required.</p>
    <br>
    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
