@extends('layouts.agreements')

@section('title')
{{$title}}
@endsection

@section('content')
<h1>{{$title}}</h1>
{!! $content !!}
@endsection
