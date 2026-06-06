.users-report-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
}
.users-report-nav__link {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.55rem 1rem;
    border-radius: 0.625rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--users-text-muted, #64748b);
    background: #fff;
    border: 1px solid rgba(148, 163, 184, 0.35);
    text-decoration: none;
    transition: all 0.15s ease;
}
.users-report-nav__link:hover {
    color: var(--users-primary, #6366f1);
    border-color: rgba(99, 102, 241, 0.35);
    background: rgba(99, 102, 241, 0.04);
}
.users-report-nav__link.is-active {
    color: #fff;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}
[data-theme-mode="dark"] .users-report-nav__link {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.75);
}
[data-theme-mode="dark"] .users-report-nav__link.is-active {
    color: #fff;
}
