<p>Dear {{ $name }},</p>

<p>This is timesheet approval reminder, and some user's timesheets require your approval. Kindly request your review for the documents.</p>

<a>Month Periodes:</a><br>
@foreach($month_periode as $mp)
@php
$year = substr($mp->month_periode, 0, 4);
$month = substr($mp->month_periode, 4, 2);

$year = (int) $year;
$month = (int) $month;
@endphp
    <a>{{ date("F", mktime(0, 0, 0, $month, 1)) }} : <a href="https://timereport.perdana.co.id/approval/timesheet/p?_token=Kn5zpeeBb4qPfgnPhqo8hwF00a1wtsw2OaartL6b&monthOpt={{ $month }}&showOpt=1&yearOpt={{ $year }}"><strong>Click Here</strong></a></a><br>
@endforeach

<br>
<p>Regards,<br><img src="{{ asset('img/PC-01Mailer.png') }}" style="height: 40px; width: 90px;" /> <br><strong>ESS Admin</strong></p><br>
<a style="color: grey;"><u><i>Isi email ini bersifat rahasia dan hanya ditujukan untuk penerima yang ditentukan dalam pesan. Dilarang keras membagikan bagian mana pun dari pesan ini dengan pihak ketiga mana pun, tanpa persetujuan tertulis dari pengirim. Jika Anda menerima pesan ini karena kesalahan, harap balas pesan ini dan ikuti penghapusannya, sehingga kami dapat memastikan kesalahan seperti itu tidak terjadi lagi di masa mendatang.</i></u></a><br><br>
<a style="color: red;"><u style="color: red;"><i style="color: red;">The content of this email is confidential and intended for the recipient specified in message only. It is strictly forbidden to share any part of this message with any third party, without a written consent of the sender. If you received this message by mistake, please reply to this message and follow with its deletion, so that we can ensure such a mistake does not occur in the future.</i></u></a>

