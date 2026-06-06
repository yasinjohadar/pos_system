@forelse ($users as $user)
    @php
        $userSessions = $sessions->get($user->id);
        $lastSession = $userSessions ? $userSessions->first() : null;
        $nameParts = preg_split('/\s+/u', trim($user->name), -1, PREG_SPLIT_NO_EMPTY);
        $initials = collect($nameParts)->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->join('');
    @endphp
    <tr>
        <th scope="row" class="users-row-index">{{ $users->firstItem() + $loop->index }}</th>

        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    @if ($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <a href="{{ route('users.show', $user->id) }}" class="users-user-name">
                    {{ $user->name }}
                </a>
            </div>
        </td>

        <td>
            @if ($user->email)
                <div class="users-email-cell">
                    <a href="mailto:{{ $user->email }}" class="users-email-link" title="إرسال بريد إلكتروني">
                        {{ $user->email }}
                    </a>
                    <button type="button" class="users-copy-btn" data-copy="{{ $user->email }}"
                        title="نسخ البريد" aria-label="نسخ البريد">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>

        <td>
            @if ($user->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}"
                    target="_blank" class="users-phone-cell" title="فتح WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                    <span>{{ $user->phone }}</span>
                </a>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>

        <td>
            @if ($lastSession)
                <span class="users-muted-text">
                    {{ \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans() }}
                </span>
            @else
                <span class="users-muted-text">لا توجد جلسات</span>
            @endif
        </td>

        <td>
            @forelse ($user->getRoleNames() as $role)
                <span class="users-badge users-badge--role">{{ $role }}</span>
            @empty
                <span class="users-muted-text">—</span>
            @endforelse
        </td>

        <td>
            @if ($user->status === 'active')
                <span class="users-badge users-badge--active">مفعل</span>
            @elseif ($user->status === 'inactive')
                <span class="users-badge users-badge--inactive">موقوف</span>
            @elseif ($user->status === 'banned')
                <span class="users-badge users-badge--banned">محظور</span>
            @else
                <span class="users-badge users-badge--unknown">غير معروف</span>
            @endif
        </td>

        <td>
            <label class="users-toggle">
                <input type="checkbox"
                    class="users-toggle-input"
                    data-user-id="{{ $user->id }}"
                    data-toggle-url="{{ route('users.toggle-status', $user->id) }}"
                    {{ $user->is_active ? 'checked' : '' }}>
                <span class="users-toggle-track">
                    <span class="users-toggle-thumb"></span>
                </span>
                <span class="users-toggle-label">
                    {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                </span>
            </label>
        </td>

        <td>
            <div class="users-actions">
                <a class="users-action-btn users-action-btn--edit"
                    href="{{ route('users.edit', $user->id) }}"
                    title="تعديل المستخدم">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <button type="button" class="users-action-btn users-action-btn--delete"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteConfirmModal"
                    data-delete-action="{{ route('users.destroy', $user->id) }}"
                    data-delete-title="حذف المستخدم"
                    data-delete-message="هل أنت متأكد من حذف هذا المستخدم؟"
                    data-delete-item="{{ $user->name }}"
                    title="حذف المستخدم">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
                <button type="button" class="users-action-btn users-action-btn--password"
                    data-bs-toggle="modal"
                    data-bs-target="#changePasswordModal"
                    data-password-action="{{ route('users.update-password', $user->id) }}"
                    data-password-user="{{ $user->name }}"
                    title="تعديل كلمة السر">
                    <i class="fa-solid fa-key"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="users-empty">لا توجد بيانات متاحة</td>
    </tr>
@endforelse
