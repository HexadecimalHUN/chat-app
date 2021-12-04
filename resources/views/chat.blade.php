<!-- resources/views/chat.blade.php -->

@extends('layouts.app')

@section('content')
<chat-body :user="{{ Auth::user() }}"></chat-body>
@endsection