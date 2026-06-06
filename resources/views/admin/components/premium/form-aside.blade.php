{{-- usage: @include('admin.components.premium.form-aside', ['icon' => '...', 'title' => '...', 'text' => '...', 'tips' => [...]]) --}}
<aside class="users-form-aside">
    <div class="users-form-aside__glow"></div>
    <div class="users-form-aside__icon">
        <i class="fas {{ $icon ?? 'fa-edit' }}"></i>
    </div>
    <h6 class="users-form-aside__title">{{ $title }}</h6>
    <p class="users-form-aside__text">{{ $text }}</p>
    @if (!empty($tips))
        <ul class="users-form-aside__tips">
            @foreach ($tips as $tip)
                <li><i class="fas fa-check"></i> {{ $tip }}</li>
            @endforeach
        </ul>
    @endif
</aside>
