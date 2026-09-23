<p>Yeni bir seminer talebi oluşturuldu.</p>

<ul>
    <li><strong>Seminer:</strong> {{ $seminarRequest->seminarSubject->subject }}</li>
    <li><strong>Kurum/kuruluş:</strong> {{ $seminarRequest->organizationRecord?->name ?? $seminarRequest->organization }}</li>
    <li><strong>Tür:</strong> {{ $seminarRequest->seminar_type === 'online' ? 'Online' : 'Yüz yüze' }}</li>
    @if($seminarRequest->seminar_type === 'in_person')<li><strong>Yer:</strong> {{ $seminarRequest->location }}</li>@endif
    <li><strong>Tarih aralığı:</strong> {{ $seminarRequest->seminar_start_date->format('d.m.Y') }}@if(!$seminarRequest->seminar_start_date->isSameDay($seminarRequest->seminar_end_date)) – {{ $seminarRequest->seminar_end_date->format('d.m.Y') }}@endif</li>
    <li><strong>Talep sahibi:</strong> {{ $seminarRequest->user->name }} {{ $seminarRequest->user->surname }}</li>
    <li><strong>E-posta:</strong> {{ $seminarRequest->user->email }}</li>
    <li><strong>Telefon:</strong> {{ $seminarRequest->user->phone_number }}</li>
</ul>
