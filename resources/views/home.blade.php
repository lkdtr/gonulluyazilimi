@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Kaydınız başarı ile alındı</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <br/><br/><br/>
                    <div class="alert alert-success" role="alert">Kaydınız başarı ile alınmıştır. Etkinlikte görüşmek üzere</div>
                    <br/><br/><br/>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
