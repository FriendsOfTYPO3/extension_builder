import { css } from 'lit';

/**
 * Bootstrap 5-compatible button styles for Lit Shadow DOM components.
 *
 * Uses --bs-* CSS custom properties set by the TYPO3 backend in :root (which
 * pierce the shadow boundary) with hardcoded fallbacks for standalone use.
 *
 * Provides: .btn, .btn-sm, .btn-primary, .btn-default, .btn-danger, .btn-warning
 */
export const buttonStyles = css`
    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        user-select: none;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: var(--bs-body-font-size, 0.875rem);
        line-height: 1.5;
        border-radius: var(--bs-border-radius, 0.25rem);
        transition:
            color 0.15s ease-in-out,
            background-color 0.15s ease-in-out,
            border-color 0.15s ease-in-out,
            box-shadow 0.15s ease-in-out;
        text-decoration: none;
    }

    .btn:focus {
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgb(0 123 255 / 25%);
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: var(--bs-body-font-size-sm, 0.75rem);
        border-radius: var(--bs-border-radius-sm, 0.2rem);
    }

    /* Primary — TYPO3 orange */
    .btn-primary {
        color: #fff;
        background-color: var(--eb-brand-color, #ff8700);
        border-color: var(--eb-brand-color, #ff8700);
    }

    .btn-primary:hover {
        color: #fff;
        background-color: color-mix(in srgb, var(--eb-brand-color, #ff8700) 85%, #000);
        border-color: color-mix(in srgb, var(--eb-brand-color, #ff8700) 80%, #000);
    }

    /* Default — light gray with border (TYPO3-specific, not in vanilla Bootstrap 5) */
    .btn-default {
        color: var(--bs-body-color, #333);
        background-color: var(--bs-secondary-bg, #e9ecef);
        border-color: var(--bs-border-color, #dee2e6);
    }

    .btn-default:hover {
        color: var(--bs-body-color, #333);
        background-color: color-mix(in srgb, var(--bs-secondary-bg, #e9ecef) 85%, #000);
        border-color: var(--bs-border-color, #dee2e6);
    }

    /* Danger — red */
    .btn-danger {
        color: #fff;
        background-color: var(--bs-danger, #dc3545);
        border-color: var(--bs-danger, #dc3545);
    }

    .btn-danger:hover {
        color: #fff;
        background-color: color-mix(in srgb, var(--bs-danger, #dc3545) 85%, #000);
        border-color: color-mix(in srgb, var(--bs-danger, #dc3545) 80%, #000);
    }

    /* Warning — yellow */
    .btn-warning {
        color: var(--bs-dark, #212529);
        background-color: var(--bs-warning, #ffc107);
        border-color: var(--bs-warning, #ffc107);
    }

    .btn-warning:hover {
        color: var(--bs-dark, #212529);
        background-color: color-mix(in srgb, var(--bs-warning, #ffc107) 85%, #000);
        border-color: color-mix(in srgb, var(--bs-warning, #ffc107) 80%, #000);
    }
`;
