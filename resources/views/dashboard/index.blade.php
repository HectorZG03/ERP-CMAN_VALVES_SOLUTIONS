@extends('layouts.app')

@section('content')
<div class="space-y-8">

    @include('dashboard.partials.header')

    @include('dashboard.partials.stats')

    @include('dashboard.partials.quick-actions')

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush