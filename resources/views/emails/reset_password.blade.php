<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
</head>

<body style="margin:0; padding:0; background-color:#eaf3fb; font-family:'Segoe UI', Arial, sans-serif; line-height:1.6;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="background-color:#f8f9fa; padding:30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600"
                    style="background-color:#ffffff; border-radius:16px; border:5px solid #ffd75e; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow:hidden;">

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
                                <h2 style="color:#f7b500; text-align:center; margin-top:0;">
                                    Password Reset Request
                                </h2>

                                <p>Dear <strong style="color:#000;">{{ $user->name ?? $user->email ?? 'User' }}</strong>,</p>

                                <p>
                                    We received a request to reset your password for your Clinic System Demo account.
                                </p>

                                <p style="text-align:center; margin:30px 0;">
                                    <a href="{{ $resetUrl }}" style="background:#f7b500; color:#fff; text-decoration:none; padding:12px 32px; border-radius:6px; font-weight:600; font-size:16px; display:inline-block;">Reset Password</a>
                                </p>

                                <p>If you did not request a password reset, please ignore this email. This link will expire in 60 minutes for your security.</p>

                                <p style="margin-top:30px;">
                                    Kind regards,<br>
                                    <strong>Medical Unit DEMO</strong>
                                </p>

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