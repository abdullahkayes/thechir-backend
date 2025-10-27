<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Password Reset</title>
  <style>
    body {
      background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    .email-wrapper {
      max-width: 600px;
      margin: 50px auto;
      background-color: #ffffff;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .email-header {
      background: linear-gradient(to right, #4a90e2, #007bff);
      padding: 30px;
      text-align: center;
      color: #ffffff;
    }

    .email-header img {
      max-width: 120px;
      margin-bottom: 10px;
    }

    .email-content {
      padding: 40px 30px;
    }

    h2 {
      margin-top: 0;
      color: #2e3a59;
    }

    p {
      font-size: 16px;
      line-height: 1.7;
      color: #4a4a4a;
    }

    .reset-button {
      display: inline-block;
      margin-top: 30px;
      padding: 14px 28px;
      background: #007bff;
      color: #ffffff !important;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    .reset-button:hover {
      background: #0056b3;
    }

    .email-footer {
      text-align: center;
      padding: 30px;
      font-size: 13px;
      color: #999999;
    }

    .footer-note {
      margin-top: 10px;
      font-style: italic;
    }
  </style>
</head>
<body>
  <div class="email-wrapper">
    <div class="email-header">
        {{ $token }}
      <h1>Password Reset</h1>
    </div>
    <div class="email-content">
      <h2>Hello, </h2>
      <p>We received a request to reset your password for your account. If you made this request, please click the button below to reset your password:</p>
      <a href="http://localhost:5173/#/password/reset/{{ $token }}" class="reset-button">Reset Password</a>
      <p>If you didn’t request a password reset, you can safely ignore this email. Your password will remain unchanged.</p>
    </div>
    <div class="email-footer">
      &copy;  Your Company Name. All rights reserved.
      <div class="footer-note">This is an automated message. Please do not reply.</div>
    </div>
  </div>
</body>
</html>
