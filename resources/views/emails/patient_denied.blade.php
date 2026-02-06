<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Patient Form Review Required</title>
</head>

<body style="margin:0; padding:0; background-color:#eaf3fb; font-family:'Segoe UI', Arial, sans-serif; line-height:1.6;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="background-color:#f8f9fa; padding:30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600"
                    style="background-color:#ffffff; border-radius:16px; border:5px solid #ff6b6b; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow:hidden;">

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
                                <h2 style="color:#dc3545; text-align:center; margin-top:0;">
                                    Patient Form Review Required
                                </h2>

                                <p>Greetings <strong style="color:#000;">{{ $patient->first_name }} {{ $patient->last_name }}</strong>,</p>

                                <p>
                                    We regret to inform you that your health information form requires additional review and could not be
                                    <strong style="color:#dc3545;">approved</strong> at this time.
                                </p>

                                <p>
                                    This may be due to incomplete information or the need for clarification on certain details.
                                    Your submission will remain pending until the necessary corrections are made.
                                </p>

                                <div style="text-align: center; margin: 30px 0;">
                                    <a href="{{ route('student.dashboard') }}"
                                        style="display: inline-block; padding: 12px 30px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                                        Resubmit Form
                                    </a>
                                </div>

                                <p>
                                    Please visit the Health and Wellness Service Unit during office hours or contact us
                                    for more information about what needs to be corrected.
                                </p>

                                <p style="margin-top:30px;">
                                    Kind regards,<br>
                                    <strong>Health and Wellness Service Unit</strong>
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