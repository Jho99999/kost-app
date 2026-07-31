<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:40px 0;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0"
                   style="background:#ffffff;border-radius:8px;padding:40px;">

                <tr>
                    <td>

                        <h2 style="margin-top:0;color:#222;">
                            Reset Password
                        </h2>

                        <p style="font-size:16px;color:#555;">
                            Halo <strong>{{ $user->name }}</strong>,
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.6;">
                            Kami menerima permintaan untuk mereset password akun Anda.
                        </p>

                        <p style="font-size:15px;color:#555;line-height:1.6;">
                            Klik tombol di bawah ini untuk membuat password baru.
                        </p>

                        

                        <div style="text-align:center;margin:35px 0;">

                            <a href="{{ $url }}"
                               style="
                                    background:#2563eb;
                                    color:#ffffff;
                                    text-decoration:none;
                                    padding:14px 28px;
                                    border-radius:6px;
                                    display:inline-block;
                                    font-size:16px;
                                    font-weight:bold;
                               ">
                                Reset Password
                            </a>

                        </div>

                        <p style="font-size:14px;color:#666;">
                            Atau salin tautan berikut ke browser Anda:
                        </p>

                        <p style="
                            word-break:break-all;
                            background:#f3f4f6;
                            padding:10px;
                            border-radius:4px;
                            font-size:13px;
                            color:#444;
                        ">
                            {{ $url }}
                        </p>

                        <hr style="margin:30px 0;border:none;border-top:1px solid #ddd;">

                        <p style="font-size:14px;color:#666;">
                            Link ini berlaku selama <strong>60 menit</strong>.
                        </p>

                        <p style="font-size:14px;color:#666;">
                            Jika Anda tidak meminta reset password,
                            abaikan email ini dan password Anda tidak akan berubah.
                        </p>

                        <br>

                        <p style="font-size:15px;color:#444;">
                            Salam,<br>
                            <strong>{{ config('app.name') }}</strong>
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>