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

                    <p>Talep oluşturabilmek için üye girişi gereklidir. İletişim bilgileriniz üyelik kaydınızdan alınır.</p>

                    <h5>Verilebilecek seminerler</h5>
                    <ul>
                        @forelse ($seminarSubjects as $seminarSubject)
                            <li><strong>{{ $seminarSubject->subject }}</strong> — {{ $seminarSubject->summary }} ({{ $seminarSubject->duration }} saat)</li>
                        @empty
                            <li>Şu anda listelenen bir seminer bulunmuyor.</li>
                        @endforelse
                    </ul>

                    @guest
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
                                <label for="location" class="col-md-3 col-form-label text-md-end">Seminer verilecek yer</label>
                                <div class="col-md-7"><input id="location" name="location" value="{{ old('location') }}" placeholder="İl, ilçe ve açık adres" class="form-control @error('location') is-invalid @enderror" required><span class="invalid-feedback">@error('location'){{ $message }}@enderror</span></div>
                            </div>
                            <div class="row mb-3">
                                <label for="seminar_date" class="col-md-3 col-form-label text-md-end">Tercih edilen tarih</label>
                                <div class="col-md-7"><input id="seminar_date" type="date" min="{{ $minimumSeminarDate }}" name="seminar_date" value="{{ old('seminar_date') }}" class="form-control @error('seminar_date') is-invalid @enderror" required><small class="form-text text-muted">Tarih en erken {{ \Carbon\Carbon::parse($minimumSeminarDate)->format('d.m.Y') }} olabilir.</small><span class="invalid-feedback">@error('seminar_date'){{ $message }}@enderror</span></div>
                            </div>
                            <div class="row"><div class="col-md-7 offset-md-3"><button class="btn btn-primary" type="submit">Talep oluştur</button></div></div>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
