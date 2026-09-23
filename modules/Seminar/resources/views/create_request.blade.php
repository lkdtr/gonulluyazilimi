@extends($inIframe ? 'layouts.iframe' : 'layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-secondary">
                <div class="card-header text-white bg-secondary">{{ trans("panel.create_seminar_request_title") }}</div>
                <div class="card-body">

                    @if (session('success-status'))
                        <div class="alert alert-success d-flex" role="alert">
                            <svg style="height: 20px;width: 20px;" class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                            <div>{{ session('success-status') }}</div>
                        </div>
                    @endif
                    <p class="text-end"><a href="{{ route('create-seminar-offer', $inIframe ? ['in-iframe' => 1] : []) }}">Seminer vermek ister misiniz? Başvuru formunu doldurun.</a></p>

                    @guest
                        <p>Talep oluşturabilmek için üye girişi gereklidir. İletişim bilgileriniz üyelik kaydınızdan alınır.</p>
                        <h5>Verilebilecek seminerler</h5>
                        <ul>
                            @forelse ($seminarSubjects as $seminarSubject)
                                <li><strong>{{ $seminarSubject->subject }}</strong> — {{ $seminarSubject->summary }} ({{ $seminarSubject->duration }} saat)</li>
                            @empty
                                <li>Şu anda listelenen bir seminer bulunmuyor.</li>
                            @endforelse
                        </ul>
                        <a class="btn btn-primary" href="{{ route('seminar-request.start', $inIframe ? ['in-iframe' => 1] : []) }}">Giriş yaparak talep oluştur</a>
                        <a class="btn btn-outline-secondary" href="{{ route('register') }}">Üye ol</a>
                    @else
                        <form method="POST" action="{{ route('seminar-request.store', $inIframe ? ['in-iframe' => 1] : []) }}">
                            @csrf
                            <div class="row mb-3">
                                <label for="seminar_subject_id" class="col-md-3 col-form-label text-md-end">Seminer</label>
                                <div class="col-md-7">
                                    <select id="seminar_subject_id" name="seminar_subject_id" class="form-select @error('seminar_subject_id') is-invalid @enderror" required>
                                        <option value="">Seminer seçin</option>
                                        @foreach ($seminarSubjects as $seminarSubject)
                                            <option value="{{ $seminarSubject->id }}" @selected(old('seminar_subject_id') == $seminarSubject->id)>{{ $seminarSubject->subject }} ({{ $seminarSubject->duration }} saat)</option>
                                        @endforeach
                                    </select>
                                    @error('seminar_subject_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="organization" class="col-md-3 col-form-label text-md-end">Kurum / kuruluş</label>
                                <div class="col-md-7"><input id="organization" name="organization" list="organizations" value="{{ old('organization') }}" class="form-control @error('organization') is-invalid @enderror" required><datalist id="organizations">@foreach ($organizations as $organization)<option value="{{ $organization->name }}">@endforeach</datalist><small class="form-text text-muted">Listede yoksa kurum adını yazın; kurum havuzuna eklenecektir.</small><span class="invalid-feedback">@error('organization'){{ $message }}@enderror</span></div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label text-md-end">Seminer türü</label>
                                <div class="col-md-7">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="seminar_type" id="seminar_type_in_person" value="in_person" @checked(old('seminar_type', 'in_person') === 'in_person')>
                                        <label class="form-check-label" for="seminar_type_in_person">Yüz yüze</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="seminar_type" id="seminar_type_online" value="online" @checked(old('seminar_type') === 'online')>
                                        <label class="form-check-label" for="seminar_type_online">Online</label>
                                    </div>
                                    @error('seminar_type')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row mb-3" id="location-field">
                                <label for="location" class="col-md-3 col-form-label text-md-end">Seminer verilecek yer</label>
                                <div class="col-md-7"><input id="location" name="location" value="{{ old('location') }}" placeholder="İl, ilçe ve açık adres" class="form-control @error('location') is-invalid @enderror" required><span class="invalid-feedback">@error('location'){{ $message }}@enderror</span></div>
                            </div>
                            <div class="row mb-3">
                                <label for="seminar_start_date" class="col-md-3 col-form-label text-md-end">Seminer tarih aralığı</label>
                                <div class="col-md-7">
                                    <label class="form-label" for="seminar_start_date">Başlangıç</label>
                                    <input id="seminar_start_date" type="date" min="{{ $minimumSeminarDate }}" name="seminar_start_date" value="{{ old('seminar_start_date') }}" class="form-control @error('seminar_start_date') is-invalid @enderror" required>
                                    @error('seminar_start_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    <label class="form-label mt-2" for="seminar_end_date">Bitiş</label>
                                    <input id="seminar_end_date" type="date" min="{{ $minimumSeminarDate }}" name="seminar_end_date" value="{{ old('seminar_end_date') }}" class="form-control @error('seminar_end_date') is-invalid @enderror" required>
                                    @error('seminar_end_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    <small class="form-text text-muted">Başlangıç tarihi en erken {{ \Carbon\Carbon::parse($minimumSeminarDate)->format('d.m.Y') }} olabilir. Bitiş tarihi başlangıçtan önce olamaz.</small>
                                </div>
                            </div>
                            <div class="row"><div class="col-md-7 offset-md-3"><button class="btn btn-primary" type="submit">Talep oluştur</button></div></div>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@auth
<script>
    const seminarTypeInputs = document.querySelectorAll('input[name="seminar_type"]');
    const locationField = document.getElementById('location-field');
    const locationInput = document.getElementById('location');
    const updateLocationField = () => {
        const isOnline = document.querySelector('input[name="seminar_type"]:checked').value === 'online';
        locationField.classList.toggle('d-none', isOnline);
        locationInput.required = !isOnline;
    };
    seminarTypeInputs.forEach((input) => input.addEventListener('change', updateLocationField));
    updateLocationField();
</script>
@endauth
@endsection
