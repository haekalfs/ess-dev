<p>Dear {{ $name }},</p>
@php
    $totalApprovedAmount = 0;

    foreach ($formCreator->items as $item) {
        // Remove the comma and convert the string to a float
        $approvedAmount = floatval(str_replace(',', '', $item->approved_amount));

        // Add the numeric value to the totalApprovedAmount
        $totalApprovedAmount += $approvedAmount;
    }
@endphp
<p>We are pleased to inform you that your Reimbursement Request for {{ $formCreator->f_type }} has been fully approved. The total approved amount for all items in your request is Rp {{ number_format($totalApprovedAmount, 0, ',', '.') }}.</p>

<p>Please review the details of the approved reimbursement by clicking the link below:</p>
<p><a href="{{ $link }}"><strong>Click Here</strong></a></p>

<p>If you have any questions or require further clarification regarding your reimbursement, feel free to reach out to us.</p>

<p>Regards,<br>
<img src="{{ asset('img/PC-01Mailer.png') }}" alt="ESS Admin" style="height: 40px; width: 90px;" /><br>
<strong>ESS Admin</strong></p>
<br>
<a style="color: grey;"><u><i>Isi email ini bersifat rahasia dan hanya ditujukan untuk penerima yang ditentukan dalam pesan. Dilarang keras membagikan bagian mana pun dari pesan ini dengan pihak ketiga mana pun, tanpa persetujuan tertulis dari pengirim. Jika Anda menerima pesan ini karena kesalahan, harap balas pesan ini dan ikuti penghapusannya, sehingga kami dapat memastikan kesalahan seperti itu tidak terjadi lagi di masa mendatang.</i></u></a><br><br>
<a style="color: red;"><u style="color: red;"><i style="color: red;">The content of this email is confidential and intended for the recipient specified in message only. It is strictly forbidden to share any part of this message with any third party, without a written consent of the sender. If you received this message by mistake, please reply to this message and follow with its deletion, so that we can ensure such a mistake does not occur in the future.</i></u></a>

