@php($redirect = $request->user_id ? \Modules\MailForwarding\Models\EmailRedirects::where('user_id', $request->user_id)->where('status', 1)->first() : null)
@if ($redirect)
    <div class="alert alert-warning mb-2">
        <strong>Aktif e-posta yönlendirmesi:</strong> <code>{{ $redirect->email_alias }}</code>.
        Onaydan önce bu adresi PostfixAdmin'de kaldırın; onay yalnız portaldaki kaydı siler, sunucudaki yönlendirme çalışmaya devam eder.
    </div>
@endif
