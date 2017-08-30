@extends('layout.leaders.master')
@section('title')
    Leader
@stop
@section('content')
    @include('layout.leaders.widget.header')
    @include('layout.leaders.widget.navbar')
    @if(!empty(Auth::user()->group))
        @if(Auth::user()->group->type=="base")
            <script>
                window.location.href = "{{route('listBaseMember')}}";
            </script>
        @endif
        @if(Auth::user()->group->type=="first")
            <script>
                window.location.href = "{{route('leaderFirstGetOrder')}}";
            </script>
        @endif
    @endif
@stop