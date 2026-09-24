@php($redirects = $request->user_id ? \Modules\MailForwarding\Models\EmailRedirects::where('user_id', $request->user_id)->where('status', 1)->pluck('email_alias') : collect())
@if ($redirects->isNotEmpty())
    <div class="alert alert-warning mb-2">
        <strong>Aktif e-posta yönlendirmesi:</strong> <code>{{ $redirects->implode(', ') }}</code>.
        Onaydan önce bu adresleri PostfixAdmin'de kaldırın; onay yalnız portaldaki kayıtları siler, sunucudaki yönlendirmeler çalışmaya devam eder.
    </div>
@endif
