<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email Address</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Hello {{ $user->name }},</h2>
    <p>Please click the button below to verify your email address.</p>
    <p>
        <a href="{{ $url }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Verify Email Address</a>
    </p>
    <p>If you did not create an account, no further action is required.</p>
    <br>
    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
