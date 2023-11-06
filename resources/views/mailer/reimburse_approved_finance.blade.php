<p>Dear, {{ $name }}</p>

<p>This is Reimbursement Admin, there are documents that already approved and need to be processed by Finance Department. Kindly request your review for the documents.</p>

<a>Reimbursement ID :</a><br>
@foreach($reimbReq as $rr)
    @php
    // Parse the 'created_at' timestamp using Carbon
    $createdAt = \Carbon\Carbon::parse($rr->created_at);
    
    // Extract the year and month
    $year = $createdAt->format('Y');
    $month = $createdAt->format('m');
    @endphp
    <a>{{ $rr->f_id  }} : <a href="https://timereport.perdana.co.id/reimbursement/manage?_token=QoAJvJBscuutE9E5PgNNmNJmU31rk4m2wlRWhaJL&showOpt=1&yearOpt={{ $year }}&monthOpt={{ $month }}"><strong>Click Here</strong></a></a><br>
@endforeach

<br>
<p>Regards,<br><img src="{{ asset('img/PC-01Mailer.png') }}" style="height: 40px; width: 90px;" /> <br><strong>ESS Admin</strong></p><br>
<a style="color: grey;"><u><i>Isi email ini bersifat rahasia dan hanya ditujukan untuk penerima yang ditentukan dalam pesan. Dilarang keras membagikan bagian mana pun dari pesan ini dengan pihak ketiga mana pun, tanpa persetujuan tertulis dari pengirim. Jika Anda menerima pesan ini karena kesalahan, harap balas pesan ini dan ikuti penghapusannya, sehingga kami dapat memastikan kesalahan seperti itu tidak terjadi lagi di masa mendatang.</i></u></a><br><br>
<a style="color: red;"><u style="color: red;"><i style="color: red;">The content of this email is confidential and intended for the recipient specified in message only. It is strictly forbidden to share any part of this message with any third party, without a written consent of the sender. If you received this message by mistake, please reply to this message and follow with its deletion, so that we can ensure such a mistake does not occur in the future.</i></u></a>

