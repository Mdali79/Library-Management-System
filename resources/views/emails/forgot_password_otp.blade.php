<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .email-container { background-color: #ffffff; border-radius: 10px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #2563eb; }
        .header h1 { color: #2563eb; margin: 0; font-size: 28px; }
        .otp-box { background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: white; padding: 30px; border-radius: 10px; text-align: center; margin: 30px 0; }
        .otp-code { font-size: 48px; font-weight: bold; letter-spacing: 10px; margin: 20px 0; font-family: 'Courier New', monospace; }
        .content { margin: 20px 0; }
        .info-box { background-color: #f8f9fa; border-left: 4px solid #2563eb; padding: 15px; margin: 20px 0; }
        .warning-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📚 eLibrary</h1>
            <p>Password Reset</p>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $name }}</strong>,</p>
            <p>You requested to reset your password. Use the verification code below on the password reset page:</p>
            <div class="otp-box">
                <p style="margin: 0 0 10px 0; font-size: 16px;">Your Verification Code</p>
                <div class="otp-code">{{ $otp }}</div>
                <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">This code expires in {{ $expiryMinutes }} minutes</p>
            </div>
            <div class="info-box">
                <strong>📝 Instructions:</strong>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li>Enter the 6-digit code on the password reset page</li>
                    <li>Set your new password and confirm it</li>
                </ol>
            </div>
            <div class="warning-box">
                <strong>⚠️ Security:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Never share this code with anyone</li>
                    <li>If you didn't request a password reset, ignore this email</li>
                </ul>
            </div>
            <p>Best regards,<br><strong>eLibrary Management System</strong></p>
        </div>
        <div class="footer">
            <p>This is an automated email. Please do not reply.</p>
            <p>&copy; {{ date('Y') }} eLibrary. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
