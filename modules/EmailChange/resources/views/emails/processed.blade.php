@if($emailChangeRequest->status === 'approved')
<p>E-posta değişikliği talebiniz onaylandı. Üyelik e-posta adresiniz <strong>{{ $emailChangeRequest->requested_email }}</strong> olarak güncellendi.</p>
@else
<p>E-posta değişikliği talebiniz yönetim tarafından onaylanmadı. Üyelik e-posta adresiniz değiştirilmedi.</p>
@endif
