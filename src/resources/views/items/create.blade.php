@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h2 class="page-title">商品の出品</h2>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>商品画像</label>
            <div class="image-upload-area">
                <label for="image-input" class="image-upload-label">
                    <span class="upload-button">画像を選択する</span>
                </label>
                <input type="file" id="image-input" name="image" style="display:none;">
                @error('image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <h3 class="section-title">商品の詳細</h3>

        <div class="form-group">
            <label>カテゴリー</label>
            <div class="category-grid">
                @foreach($categories as $category)
                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="cat-{{ $category->id }}" class="category-checkbox" {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                    <label for="cat-{{ $category->id }}" class="category-badge">{{ $category->name }}</label>
                @endforeach
            </div>
            @error('category_ids')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>商品の状態</label>
            <div class="select-wrapper">
                <select name="condition_id" id="condition-select" class="{{ old('condition_id') ? '' : 'is-empty' }}">
                    <option value="" hidden>選択してください</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>{{ $condition->name }}</option>
                    @endforeach
                </select>
            </div>
            @error('condition_id')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <h3 class="section-title">商品名と説明</h3>

        <div class="form-group">
            <label>商品名</label>
            <input type="text" name="name" class="full-input" value="{{ old('name') }}">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>ブランド名</label>
            <input type="text" name="brand" class="full-input" value="{{ old('brand') }}">
        </div>

        <div class="form-group">
            <label>商品の説明</label>
            <textarea name="description" rows="5" class="full-input">{{ old('description') }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>販売価格</label>
            <div class="price-input-container">
                <span class="currency-symbol">¥</span>
                <input type="text" id="price-input" class="price-input-field" value="{{ (old('price') && is_numeric(old('price'))) ? number_format(old('price')) : old('price') }}">
                <input type="hidden" name="price" id="price-hidden" value="{{ old('price') }}">
            </div>
            @error('price')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="submit-button">出品する</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const imageInput = document.getElementById('image-input');
    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const reader = new FileReader();
            const area = document.querySelector('.image-upload-area');

            reader.onload = function (e) {
                const oldImg = area.querySelector('.preview-img');
                if (oldImg) oldImg.remove();
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('preview-img');
                img.style.maxWidth = '100%';
                img.style.maxHeight = '200px';
                img.style.display = 'block';
                img.style.margin = '0 auto 15px';

                area.prepend(img);
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        });
    }

    const displayInput = document.getElementById('price-input');
    const hiddenInput = document.getElementById('price-hidden');

    if (displayInput && hiddenInput) {
        displayInput.addEventListener('input', function(e) {
            let value = e.target.value;

            value = value.replace(/[０-９]/g, function(s) {
                return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
            });

            let plainValue = value.replace(/,/g, '');

            if (plainValue !== '' && /^\d+$/.test(plainValue)) {
                displayInput.value = Number(plainValue).toLocaleString();
                hiddenInput.value = plainValue;
            } else {
                displayInput.value = value;
                hiddenInput.value = value;
            }
        });
    }

    const conditionSelect = document.getElementById('condition-select');
    if (conditionSelect) {
        conditionSelect.addEventListener('change', function() {
            if (this.value !== "") {
                this.classList.remove('is-empty');
            } else {
                this.classList.add('is-empty');
            }
        });
    }
});
</script>
@endsection