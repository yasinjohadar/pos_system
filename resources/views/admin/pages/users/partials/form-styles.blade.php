.users-form-card__section {
    margin-bottom: 1.75rem;
    padding-bottom: 1.75rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
}
.users-form-card__section:last-of-type {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.users-form-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #4338ca;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.users-photo-upload {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}
.users-photo-upload__preview img,
.users-photo-upload__preview .users-photo-upload__placeholder {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(99, 102, 241, 0.15);
}
.users-photo-upload__placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #fff;
    font-size: 2rem;
    font-weight: 700;
}
.users-photo-upload__input {
    position: absolute;
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    z-index: -1;
}
.users-photo-upload__btn {
    cursor: pointer;
    margin: 0;
}
.users-premium .users-form-input:-webkit-autofill,
.users-premium .users-form-input:-webkit-autofill:hover,
.users-premium .users-form-input:-webkit-autofill:focus {
    -webkit-text-fill-color: var(--users-text, #1e293b);
    -webkit-box-shadow: 0 0 0 1000px var(--users-card, #fff) inset;
    box-shadow: 0 0 0 1000px var(--users-card, #fff) inset;
    transition: background-color 99999s ease-out 0s;
}
.users-roles-select + .select2-container {
    width: 100% !important;
}
[data-theme-mode="dark"] .users-form-section-title {
    color: #a5b4fc;
}
[data-theme-mode="dark"] .users-premium .users-form-input:-webkit-autofill,
[data-theme-mode="dark"] .users-premium .users-form-input:-webkit-autofill:hover,
[data-theme-mode="dark"] .users-premium .users-form-input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 1000px rgb(var(--body-bg-rgb, 25, 32, 47)) inset;
    box-shadow: 0 0 0 1000px rgb(var(--body-bg-rgb, 25, 32, 47)) inset;
    -webkit-text-fill-color: rgba(255, 255, 255, 0.92);
}
