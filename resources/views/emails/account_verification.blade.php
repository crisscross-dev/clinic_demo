<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Account Verification</title>
</head>

<body style="margin:0; padding:0; background-color:#eaf3fb; font-family:'Segoe UI', Arial, sans-serif; line-height:1.6;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="background-color:#f8f9fa; padding:30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600"
                    style="background-color:#ffffff; border-radius:16px; border:5px solid #1c6abb; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow:hidden;">
                    <!-- Header -->
                    <tr>
                        <td align="center"
                            style="background-color:#1c6abb; border-top-left-radius:12px; border-top-right-radius:12px; padding:20px 10px;">
                            <img src="{{ $message->embed(public_path('images/logo2_pdf.png')) }}" alt="Clinic System Demo Logo"
                                style="max-width:80px; border-radius:8px;">
                            <h1 style="color:#ffffff; font-size:20px; margin:10px 0 0 0; font-weight:600;">
                                Clinic System Demo
                            </h1>
                            <p style="color:#e0f0ff; font-size:13px; margin:4px 0 0 0;">
                                Medical Unit DEMO
                            </p>
                            <p style="color:#e0f0ff; font-size:12px; margin:0;">
                                CLINIC ADDRESS HERE
                            </p>
                        </td>
                    </tr>
                    <!-- End Header -->
                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; background-color:#ffffff;">
                            <div style="color:#333;">
                                @if (!isset($verified))
                                <h2 style="color:#1c6abb; text-align:center; margin-top:0;">
                                    Verify Your Account
                                </h2>
                                <p>Hi <strong style="color:#000;">{{ $firstName }}</strong>,</p>
                                <p>
                                    Thank you for registering. To complete your registration, please verify your email address by clicking the button below:
                                </p>
                                <div style="text-align:center; margin:30px 0;">
                                    <a href="{{ $verificationUrl }}" style="background:#1c6abb; color:#fff; padding:12px 28px; border-radius:6px; text-decoration:none; font-weight:600; font-size:16px;">Verify My Account</a>
                                </div>
                                <p style="font-size:13px; color:#555;">If you did not create an account, you can ignore this email.</p>
                                @else
                                <h2 style="color:#28a745; text-align:center; margin-top:0;">
                                    Account Verified!
                                </h2>
                                <p>Hi <strong style="color:#000;">{{ $firstName }}</strong>,</p>
                                <p>Your email <strong>{{ $email }}</strong> has been successfully verified. You can now log in to your account.</p>
                                <div style="text-align:center; margin:30px 0;">
                                    <a href="{{ route('unified.login') }}" style="background:#28a745; color:#fff; padding:12px 28px; border-radius:6px; text-decoration:none; font-weight:600; font-size:16px;">Go to Login</a>
                                </div>
                                @endif
                                <!-- Footer -->
                                <p style="margin-top:30px; text-align:center; font-size:12px; color:#555; background:#f8f9fa; padding:10px; border-radius:6px;">
                                    <strong>Note:</strong> This is an automated message. Please do not reply to this email as responses are not monitored.
                                </p>
                                <!-- End Footer -->
                            </div>
                        </td>
                    </tr>
                    <!-- End Body -->
                </table>
            </td>
        </tr>
    </table>
</body>

</html>