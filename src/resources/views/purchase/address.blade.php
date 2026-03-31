@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<main class="address-edit">
    <h2 class="address-edit__title">住所の変更</h2>

    <form action="{{ route('address.update', ['item_id' => $item_id]) }}" method="POST">
        @csrf
        <div class="address-edit__group">
            <label for="postcode" class="address-edit__label">郵便番号</label>
            <input type="text" name="postcode" id="postcode" class="address-edit__input" value="{{ old('postcode') }}">
            @error('postcode')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="address-edit__group">
            <label for="address" class="address-edit__label">住所</label>
            <input type="text" name="address" id="address" class="address-edit__input" value="{{ old('address') }}">
            @error('address')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="address-edit__group">
            <label for="building" class="address-edit__label">建物名</label>
            <input type="text" name="building" id="building" class="address-edit__input" value="{{ old('building') }}">
        </div>

        <button type="submit" class="address-edit__btn">更新する</button>
    </form>
</main>
@endsection