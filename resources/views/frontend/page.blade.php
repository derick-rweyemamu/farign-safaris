@extends('layouts.frontend')

@section('title', $page->title . ' - ' . config('app.name', ' '))

@section('content')
    {!! $page->getRenderedHtml() !!}
@endsection