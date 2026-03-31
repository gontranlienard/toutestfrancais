@extends('layouts.app')

@section('content')

<div class="brands-page">

    {{-- 🔤 NAV ALPHABET --}}
    <div class="brands-letters">
        @foreach($letters as $letter)
            <a href="#letter-{{ $letter }}">{{ $letter }}</a>
        @endforeach
    </div>

    {{-- 📦 LISTE --}}
    <div class="brands-list">

    @foreach($letters as $letter)

        @if(isset($grouped[$letter]))
            <div class="brand-group" id="letter-{{ $letter }}">

                <h2>{{ $letter }}</h2>

                <ul>
                    @foreach($grouped[$letter] as $brand)
                        <li>
                            <a href="{{ route('brand.show', $brand->slug) }}">
                                {{ $brand->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>

            </div>
        @endif

    @endforeach

</div>

</div>

@endsection