<p>Merhaba {{ $seminarRequest->user->name }},</p>

<p><strong>{{ $seminarRequest->seminarSubject->subject }}</strong> semineri için talebiniz alındı. Talebiniz değerlendirme sürecindedir; sizinle üyelik bilgilerinizdeki iletişim kanallarından dönüş yapacağız.</p>

<ul>
    <li><strong>Kurum/kuruluş:</strong> {{ $seminarRequest->organizationRecord?->name ?? $seminarRequest->organization }}</li>
    <li><strong>Yer:</strong> {{ $seminarRequest->location }}</li>
    <li><strong>Tercih edilen tarih:</strong> {{ $seminarRequest->seminar_date->format('d.m.Y') }}</li>
</ul>

<p>Linux Kullanıcıları Derneği</p>
