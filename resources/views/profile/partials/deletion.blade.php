{{-- KVKK deletion request on the signed-in person's profile page. $pending (?DataDeletionRequest) --}}
<div class="card mt-3 border-danger-subtle" id="deletion">
    <div class="card-header"><h3 class="card-title">Kişisel verilerimin silinmesi</h3></div>
    <div class="card-body">
        @if (session('deletion-status'))
            <div class="alert alert-info" role="alert">{{ session('deletion-status') }}</div>
        @endif

        @if ($pending)
            <p>{{ $pending->created_at->format('d.m.Y H:i') }} tarihli silme talebiniz <strong>değerlendirmede</strong>.</p>
            <form method="POST" action="{{ route('my-data-deletion.cancel') }}" onsubmit="return confirm('Silme talebinizden vazgeçilsin mi?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-secondary">Talebimden vazgeç</button>
            </form>
        @else
            <p class="text-secondary small">6698 sayılı KVKK kapsamında kişisel verilerinizin silinmesini isteyebilirsiniz. Talebiniz onaylanınca adınız, iletişim bilgileriniz, kimlik numaranız ve fotoğrafınız silinir; hesabınız kapatılır, sıfatlarınız ve kartlarınız sona erer. Yasal saklama yükümlülüğü olan kayıtlar (ör. sözleşme kabulleri) kimliğinizle ilişkilendirilmeden saklanır. Bu işlem geri alınamaz.</p>
            <form method="POST" action="{{ route('my-data-deletion.store') }}" onsubmit="return confirm('Kişisel verilerinizin silinmesini istediğinize emin misiniz?')">
                @csrf
                <div class="mb-2">
                    <label for="deletion-reason" class="form-label">Gerekçe <span class="text-secondary fw-normal">(isteğe bağlı)</span></label>
                    <textarea id="deletion-reason" name="reason" rows="2" maxlength="2000" class="form-control">{{ old('reason') }}</textarea>
                </div>
                <div class="mb-2">
                    <label for="deletion-password" class="form-label required">Parolanız</label>
                    <input id="deletion-password" type="password" name="password" class="form-control @error('password', 'deletion') is-invalid @enderror" required autocomplete="current-password">
                    @error('password', 'deletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-outline-danger">Verilerimin silinmesini talep et</button>
            </form>
        @endif
    </div>
</div>
