<p>Yeni bir e-posta değişikliği talebi var.</p>
<ul>
    <li>Üye: {{ $emailChangeRequest->user->name }} {{ $emailChangeRequest->user->surname }}</li>
    <li>Mevcut e-posta: {{ $emailChangeRequest->current_email }}</li>
    <li>İstenen e-posta: {{ $emailChangeRequest->requested_email }}</li>
    @if($emailChangeRequest->reason)<li>Açıklama: {{ $emailChangeRequest->reason }}</li>@endif
</ul>
