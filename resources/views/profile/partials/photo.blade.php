{{--
    Profile photo on the profile page. $approved and $upload (?ContactPhoto);
    $editable: the signed-in person's own profile, with upload and delete.
--}}
<div class="card mb-3" id="photo">
    <div class="card-header"><h3 class="card-title">Fotoğraf</h3></div>
    <div class="card-body">
        @if ($editable)
            <p class="text-secondary">Fotoğrafınız kimlik kartlarınızda kullanılır. Yüzünüzün net göründüğü, önden çekilmiş vesikalık (5×6) bir fotoğraf yükleyin. Fotoğraf yönetici onayından sonra görünür; yalnız siz ve yetkili yöneticiler görebilir.</p>
        @endif

        <div class="row g-4 align-items-start">
            <div class="col-sm-auto text-center">
                <div class="form-label">Onaylı fotoğraf</div>
                @if ($approved)
                    <img src="{{ route('photos.show', $approved) }}" alt="Onaylı fotoğraf" class="rounded border" style="width: 150px; height: 180px; object-fit: cover;">
                @else
                    <div class="rounded border bg-body-secondary d-flex align-items-center justify-content-center text-secondary" style="width: 150px; height: 180px;">
                        <i class="ti ti-user" style="font-size: 3rem;"></i>
                    </div>
                @endif
            </div>

            @if ($upload)
                <div class="col-sm-auto text-center">
                    <div class="form-label">Son yükleme</div>
                    @if ($upload->isPending())
                        <img src="{{ route('photos.show', $upload) }}" alt="Onay bekleyen fotoğraf" class="rounded border" style="width: 150px; height: 180px; object-fit: cover;">
                        <div class="mt-2"><span class="badge bg-yellow-lt">Onay bekliyor</span></div>
                    @else
                        <div class="rounded border bg-body-secondary d-flex align-items-center justify-content-center text-danger" style="width: 150px; height: 180px;">
                            <i class="ti ti-photo-x" style="font-size: 3rem;"></i>
                        </div>
                        <div class="mt-2"><span class="badge bg-red-lt">Onaylanmadı</span></div>
                        @if ($upload->rejection_reason)
                            <div class="small text-secondary mt-1" style="max-width: 150px;">{{ $upload->rejection_reason }}</div>
                        @endif
                    @endif
                </div>
            @endif

            @if ($editable)
                <div class="col">
                    <form method="POST" action="{{ route('my-photo.store') }}" enctype="multipart/form-data">
                        @csrf
                        <label for="photo-file" class="form-label">{{ $approved || $upload ? 'Yeni fotoğraf yükle' : 'Fotoğraf yükle' }}</label>
                        <input id="photo-file" type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="form-control @error('photo') is-invalid @enderror" required>
                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-hint">JPEG, PNG veya WebP; en az 240×240 piksel, en çok 4 MB.</div>
                        <button type="submit" class="btn btn-primary mt-3"><i class="ti ti-upload icon"></i> Onaya gönder</button>
                    </form>

                    @if ($approved || $upload)
                        <form method="POST" action="{{ route('my-photo.destroy') }}" class="mt-3" onsubmit="return confirm('Fotoğraflarınız silinsin mi?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost-danger btn-sm"><i class="ti ti-trash icon"></i> Fotoğrafımı sil</button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
