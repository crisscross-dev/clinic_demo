<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Consent Form Access Granted</title>
</head>

<body style="margin:0; padding:0; background-color:#eaf3fb; font-family:'Segoe UI', Arial, sans-serif; line-height:1.6;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="background-color:#f8f9fa; padding:30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600"
                    style="background-color:#ffffff; border-radius:16px; border:5px solid #28a745; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow:hidden;">

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
                                <h2 style="color:#28a745; text-align:center; margin-top:0;">
                                    Consent Form Access Granted
                                </h2>

                                <p>Greetings <strong style="color:#000;">{{ $patient->full_name }}</strong>,</p>

                                <p>
                                    We are pleased to inform you that your consent form has been
                                    <strong style="color:#28a745;">unlocked</strong>.
                                    You may now access and review the document at your convenience.
                                </p>

                                <p>
                                    If you have any questions or require further assistance, please contact the clinic
                                    administration through the official channels.
                                </p>

                                <p style="margin-top:30px;">
                                    Kind regards,<br>
                                    <strong>Medical Unit DEMO</strong>
                                </p>

                                <!-- Footer -->
                                <p style="margin-top:30px; text-align:center; font-size:12px; color:#555; background:#f8f9fa; padding:10px; border-radius:6px;">
                                    <strong>Note:</strong> This is an automated message. Please do not reply to this
                                    email as responses are not monitored.
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