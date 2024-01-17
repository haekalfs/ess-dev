<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employment Status Notification</title>
</head>
<body>
    <p>Dear {{ $HRD }},</p>

    <p>This is a notification regarding the employment status of the employee:</p>

    <ul>
        <li><strong>Name:</strong> {{ $user->name }}</li>
        <li><strong>Employee ID:</strong> {{ $user->employee_id }}</li>
        <li><strong>Status:</strong> {{ $status }}</li>
        <li><strong>Hired Date:</strong> {{ $hiredDate->format('Y-m-d') }}</li>
        <li><strong>Months Since Hired:</strong> {{ $hiredDate->diffInMonths(now()) }} months</li>
    </ul>

    <p>The employment status ({{ $status }}) for {{ $user->name }} is approaching the specified timeframe. Please take necessary actions accordingly.</p>

    <p>Best regards,<br> Administrator</p><br>
    <a style="color: grey;"><u><i>Isi email ini bersifat rahasia dan hanya ditujukan untuk penerima yang ditentukan dalam pesan. Dilarang keras membagikan bagian mana pun dari pesan ini dengan pihak ketiga mana pun, tanpa persetujuan tertulis dari pengirim. Jika Anda menerima pesan ini karena kesalahan, harap balas pesan ini dan ikuti penghapusannya, sehingga kami dapat memastikan kesalahan seperti itu tidak terjadi lagi di masa mendatang.</i></u></a><br><br>
    <a style="color: red;"><u style="color: red;"><i style="color: red;">The content of this email is confidential and intended for the recipient specified in message only. It is strictly forbidden to share any part of this message with any third party, without a written consent of the sender. If you received this message by mistake, please reply to this message and follow with its deletion, so that we can ensure such a mistake does not occur in the future.</i></u></a>
</body>
</html>
