@php
    $user = $user ?? null;
    $isEdit = (bool) $user;
    $initial = $user ? strtoupper(substr($user->name, 0, 1)) : 'U';
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data"
    class="users-form-card__body" autocomplete="off" novalidate data-form-type="other">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- منع الإكمال التلقائي في Chrome --}}
    <input type="text" name="prevent_autofill_username" tabindex="-1" autocomplete="username"
        aria-hidden="true" style="position:absolute;left:-9999px;width:0;height:0;opacity:0;pointer-events:none;">
    <input type="password" name="prevent_autofill_password" tabindex="-1" autocomplete="current-password"
        aria-hidden="true" style="position:absolute;left:-9999px;width:0;height:0;opacity:0;pointer-events:none;">

    <div class="users-form-card__section">
        <h6 class="users-form-section-title"><i class="fas fa-user"></i> المعلومات الأساسية</h6>
        <div class="users-form-grid">
            <div class="users-form-group">
                <label for="name" class="users-form-label">
                    <i class="fas fa-id-card"></i> الاسم الكامل <span class="users-form-required">*</span>
                </label>
                <input type="text" class="users-form-input @error('name') is-invalid @enderror"
                    id="name" name="name" value="{{ old('name', $user?->name) }}"
                    placeholder="الاسم الكامل" required autocomplete="off">
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="users-form-group">
                <label for="username" class="users-form-label">
                    <i class="fas fa-at"></i> اسم المستخدم
                </label>
                <input type="text" class="users-form-input @error('username') is-invalid @enderror"
                    id="username" name="username" value="{{ old('username', $user?->username) }}"
                    placeholder="اسم المستخدم" autocomplete="off" autocapitalize="off" spellcheck="false"
                    data-lpignore="true" data-1p-ignore data-form-type="other">
                @error('username')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="users-form-group">
                <label for="email" class="users-form-label">
                    <i class="fas fa-envelope"></i> البريد الإلكتروني <span class="users-form-required">*</span>
                </label>
                <input type="email" class="users-form-input @error('email') is-invalid @enderror"
                    id="email" name="email" value="{{ old('email', $user?->email) }}"
                    placeholder="example@domain.com" required autocomplete="off" dir="ltr" style="text-align: right;">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            @include('admin.components.premium.phone-input', [
                'name' => 'phone',
                'value' => old('phone', $user?->phone),
                'label' => 'رقم الهاتف',
            ])
        </div>
    </div>

    @if (!$isEdit)
    <div class="users-form-card__section">
        <h6 class="users-form-section-title"><i class="fas fa-lock"></i> كلمة المرور</h6>
        <div class="users-form-grid">
            <div class="users-form-group">
                <label for="password" class="users-form-label">
                    <i class="fas fa-key"></i>
                    كلمة المرور <span class="users-form-required">*</span>
                </label>
                <input type="password" class="users-form-input users-no-autofill @error('password') is-invalid @enderror"
                    id="password" name="password"
                    placeholder="كلمة المرور"
                    autocomplete="new-password" required
                    data-lpignore="true" data-1p-ignore data-form-type="other">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="users-form-group">
                <label for="password_confirmation" class="users-form-label">
                    <i class="fas fa-key"></i>
                    تأكيد كلمة المرور <span class="users-form-required">*</span>
                </label>
                <input type="password" class="users-form-input users-no-autofill @error('password_confirmation') is-invalid @enderror"
                    id="password_confirmation" name="password_confirmation"
                    placeholder="تأكيد كلمة المرور" autocomplete="new-password" required
                    data-lpignore="true" data-1p-ignore data-form-type="other">
                @error('password_confirmation')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    @endif

    <div class="users-form-card__section">
        <h6 class="users-form-section-title"><i class="fas fa-sliders-h"></i> الحساب والصلاحيات</h6>
        <div class="users-form-grid">
            <div class="users-form-group">
                <label class="users-form-label"><i class="fas fa-camera"></i> صورة المستخدم</label>
                <div class="users-photo-upload">
                    <div class="users-photo-upload__preview" id="user-photo-preview-wrap">
                        @if ($user?->photo)
                            <img id="user-photo-preview" src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}">
                        @else
                            <div id="user-photo-preview" class="users-photo-upload__placeholder">{{ $initial }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="photo-input" class="users-btn-secondary users-photo-upload__btn">
                            <i class="fas fa-camera"></i> اختر صورة
                        </label>
                        <input type="file" name="photo" id="photo-input" accept="image/*" class="users-photo-upload__input">
                        <p class="users-muted-text mb-0" style="margin-top: 0.35rem; font-size: 0.8125rem;">PNG, JPG — حتى 2MB</p>
                    </div>
                </div>
                @error('photo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="users-form-group">
                <label for="status" class="users-form-label"><i class="fas fa-circle"></i> حالة المستخدم</label>
                <select id="status" name="status" class="users-form-select @error('status') is-invalid @enderror" autocomplete="off">
                    <option value="active" {{ old('status', $user?->status ?? 'active') === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $user?->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="banned" {{ old('status', $user?->status) === 'banned' ? 'selected' : '' }}>محظور</option>
                </select>
                @error('status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="users-form-group">
                <label class="users-form-label"><i class="fas fa-toggle-on"></i> تفعيل الحساب</label>
                <label class="users-form-toggle">
                    <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input"
                        {{ old('is_active', $user?->is_active ?? true) ? 'checked' : '' }}>
                    <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                    <span class="users-form-toggle-label">الحساب مفعّل</span>
                </label>
            </div>

            <div class="users-form-group users-form-group--full">
                <label for="roles" class="users-form-label"><i class="fas fa-user-shield"></i> الأدوار</label>
                <select id="roles" name="roles[]" class="users-form-select users-roles-select @error('roles') is-invalid @enderror"
                    multiple autocomplete="off">
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ in_array($role->name, old('roles', $isEdit ? $user->getRoleNames()->toArray() : []), true) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <p class="users-muted-text mb-0" style="margin-top: 0.35rem; font-size: 0.8125rem;">يمكن اختيار أكثر من دور</p>
                @error('roles')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="users-form-actions">
        <button type="submit" class="users-btn-submit">
            <i class="fas fa-check"></i> {{ $submitLabel }}
        </button>
        <a href="{{ route('users.index') }}" class="users-btn-secondary">إلغاء</a>
    </div>
</form>
