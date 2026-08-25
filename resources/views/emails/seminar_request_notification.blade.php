<p>Yeni bir seminer talebi oluşturuldu.</p>

<ul>
    <li><strong>Seminer:</strong> {{ $seminarRequest->seminarSubject->subject }}</li>
    <li><strong>Kurum/kuruluş:</strong> {{ $seminarRequest->organizationRecord?->name ?? $seminarRequest->organization }}</li>
    <li><strong>Yer:</strong> {{ $seminarRequest->location }}</li>
    <li><strong>Tarih:</strong> {{ $seminarRequest->seminar_date->format('d.m.Y') }}</li>
    <li><strong>Talep sahibi:</strong> {{ $seminarRequest->user->name }} {{ $seminarRequest->user->surname }}</li>
    <li><strong>E-posta:</strong> {{ $seminarRequest->user->email }}</li>
    <li><strong>Telefon:</strong> {{ $seminarRequest->user->phone_number }}</li>
</ul>
