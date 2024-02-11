<p>Dear, {{ $name }}</p>

<p>{{ $formCreator->user->name }} has requested reimbursement for: {{ $formCreator->request->f_type }}, and it has been partially approved by {{ $formCreator->RequestTo }} with a total approved amount of: Rp {{ $formCreator->approved_amount }}. We are awaiting your final approval.</p>

<p>Please review the request and see the attached notes, if any.</p>

<p>Please open the link below to review the request:</p>
<p><a href="{{ $link }}"><strong>Click Here</strong></a></p>

<p>If you have any questions or need further assistance, please don't hesitate to contact us.</p>

<p>Regards,<br>
<img src="{{ asset('img/PC-01Mailer.png') }}" alt="ESS Admin" style="height: 40px; width: 90px;" /><br>
<strong>ESS Admin</strong></p><br>
<a style="color: grey;"><u><i>Isi email ini bersifat rahasia dan hanya ditujukan untuk penerima yang ditentukan dalam pesan. Dilarang keras membagikan bagian mana pun dari pesan ini dengan pihak ketiga mana pun, tanpa persetujuan tertulis dari pengirim. Jika Anda menerima pesan ini karena kesalahan, harap balas pesan ini dan ikuti penghapusannya, sehingga kami dapat memastikan kesalahan seperti itu tidak terjadi lagi di masa mendatang.</i></u></a><br><br>
<a style="color: red;"><u style="color: red;"><i style="color: red;">The content of this email is confidential and intended for the recipient specified in message only. It is strictly forbidden to share any part of this message with any third party, without a written consent of the sender. If you received this message by mistake, please reply to this message and follow with its deletion, so that we can ensure such a mistake does not occur in the future.</i></u></a>

