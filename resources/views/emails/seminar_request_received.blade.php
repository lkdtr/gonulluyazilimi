<p>Merhaba {{ $seminarRequest->user->name }},</p>

<p><strong>{{ $seminarRequest->seminarSubject->subject }}</strong> semineri için talebiniz alındı. Talebiniz değerlendirme sürecindedir; sizinle üyelik bilgilerinizdeki iletişim kanallarından dönüş yapacağız.</p>

<ul>
    <li><strong>Kurum/kuruluş:</strong> {{ $seminarRequest->organizationRecord?->name ?? $seminarRequest->organization }}</li>
    <li><strong>Tür:</strong> {{ $seminarRequest->seminar_type === 'online' ? 'Online' : 'Yüz yüze' }}</li>
    @if($seminarRequest->seminar_type === 'in_person')<li><strong>Yer:</strong> {{ $seminarRequest->location }}</li>@endif
    <li><strong>Tercih edilen tarih aralığı:</strong> {{ $seminarRequest->seminar_start_date->format('d.m.Y') }}@if(!$seminarRequest->seminar_start_date->isSameDay($seminarRequest->seminar_end_date)) – {{ $seminarRequest->seminar_end_date->format('d.m.Y') }}@endif</li>
</ul>

<p>Linux Kullanıcıları Derneği</p>
