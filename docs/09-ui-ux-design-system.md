# 09 — UI/UX Design System: Gudang Pakde

> **Status:** Proposed, implementation-ready design specification. This document translates the previously agreed **clean, modern, minimalist, responsive secondhand marketplace** direction into concrete UI rules. It is **not** a pixel-perfect reproduction of an approved Figma/screenshot: no authoritative design asset is attached. If one is later provided, that visual reference takes precedence over aesthetic defaults here.
>
> **Scope:** Laravel 13 + Vue 3 + Inertia.js + Tailwind CSS. MVP must remain achievable in one day. No UI framework or icon dependency is mandatory; reuse what is already installed.

## 1. Product experience principles

1. **Barang dulu:** visitors immediately see search and actual listings; no oversized marketing hero.
2. **Mudah dipercaya:** show clear price, condition, location, status, and seller identity; no fabricated ratings or verification.
3. **Satu tujuan per layar:** primary CTA is obvious: browse, post a listing, save, or contact seller.
4. **Mobile-first:** full functionality at 360px wide; no horizontal overflow.
5. **Accessible and predictable:** semantic HTML, keyboard access, visible focus, text labels, honest loading/error/empty states.
6. **Lean MVP:** prefer standard components over animations, charts, and decorative dashboards.

## 2. Visual identity — proposed tokens

Brand name in UI: **Gudang Pakde**. Tone: approachable, trustworthy, practical, local-community marketplace. UI language: **Bahasa Indonesia**.

| Token | Value | Use |
|---|---|---|
| `brand-50` | `#ECFDF5` | Subtle selected backgrounds |
| `brand-100` | `#D1FAE5` | Soft emphasis |
| `brand-600` | `#059669` | Primary buttons, links |
| `brand-700` | `#047857` | Hover/active |
| `brand-800` | `#065F46` | Strong text emphasis |
| `ink-900` | `#111827` | Primary text |
| `ink-600` | `#4B5563` | Secondary text |
| `ink-500` | `#6B7280` | Muted labels |
| `surface` | `#FFFFFF` | Cards/forms |
| `canvas` | `#F9FAFB` | Page background |
| `border` | `#E5E7EB` | Dividers, controls |
| `danger` | `#DC2626` | Validation/destructive |
| `warning` | `#B45309` | Sold / attention |
| `focus` | `#10B981` | Focus ring |

**Note:** Color tokens are *new project design decisions*, not colors extracted from a screenshot. Use semantic CSS variables or Tailwind theme mapping to avoid scattered hard-coded values. Verify WCAG contrast (especially white text on brand buttons and secondary text).

### Typography
- Primary: `Inter`, fallback `ui-sans-serif, system-ui, sans-serif`; use system fonts if Inter is not already bundled.
- Page title: 28–32px desktop / 24px mobile, weight 700.
- Section title: 20–24px, weight 650–700.
- Card title: 15–16px, weight 600, max 2 lines.
- Body: 14–16px, line-height 1.5–1.65.
- Helper/metadata: 12–14px, not smaller than 12px.
- Price: 18–22px, weight 700; Indonesian rupiah formatting, e.g. `Rp1.250.000`.

### Layout and rhythm
- Spacing scale: Tailwind 1/2/3/4/6/8/10/12; base vertical section gap 24–32px.
- Container: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`.
- Header: ~64px height; sticky optional, never cover content.
- Cards: white background, `rounded-xl`, subtle `border`, optional `shadow-sm`; hover effect only on devices with hover.
- Buttons/inputs: minimum 44px touch target; `rounded-lg` or `rounded-xl`, consistent across screens.
- Icons: consistent 18–20px line icons; always pair critical actions with labels.
- Image ratio: **4:3** in catalog cards, `object-cover`; show neutral placeholder when absent.
- Avoid gradients, glassmorphism, heavy shadows, arbitrary animations, and dense admin-style tables.

## 3. Responsive behavior

| Viewport | Catalog columns | Header/search | Filters | Detail layout |
|---|---|---|---|---|
| 320–639px | 2 where content fits; 1 at very narrow widths if necessary | Compact header; search full width below | Inline horizontal chips or collapsible panel | Single column, CTA full width |
| 640–1023px | 2–3 | Search prominent | Compact filter row | Single or 2 columns as space allows |
| 1024px+ | 4 (max 5 only with sufficient card width) | Brand, search, navigation, post CTA | Horizontal filter bar or left sidebar | Gallery left, info right |

- Cards must not truncate prices into ambiguous values.
- Prefer responsive grid with `minmax(0,1fr)` behavior.
- Navigation remains accessible by keyboard and mobile touch.
- Avoid sticky bottom CTA obscuring content; if used, add bottom padding.

## 4. Core component contracts

| Component | Content | States | Notes |
|---|---|---|---|
| `AppHeader` | Brand, search/navigation, login/profile, **Pasang Barang** | Guest/member/mobile | No duplicated nav on every page |
| `SearchInput` | Keyword, submit/clear | Idle, focused, populated | Debounce optional; enter-to-search sufficient |
| `CategoryFilter` | All categories + seeded categories | Selected, unselected | Persist selected filter in URL |
| `ConditionFilter` | Semua, Baru, Seperti Baru, Bagus, Cukup | Selected | Values map to DB enum-like strings |
| `ListingCard` | Photo, title, price, condition, location, status | Default, hover, sold | Whole card can link to detail; avoid nested clickable controls |
| `StatusBadge` | Tersedia/Terjual | Available/sold | Use text + color; not color only |
| `SellerSummary` | Name, location if available | Present/missing contact | Never invent verified seller status |
| `WhatsAppCTA` | “Hubungi Penjual” | Enabled/disabled | Only valid phone; show fallback if missing |
| `FormField` | Label, control, help, error | Default, focus, error, disabled | Accessible label/description |
| `ImageUploader` | Choose file, preview, remove/replace | Empty, selected, error, uploading | JPG/PNG/WebP, max 2 MB (planned) |
| `EmptyState` | Short message + next action | No listings/no results/my listings empty | Specific to context |
| `ConfirmDialog` | Destructive confirmation | Open/closed/busy | Required for delete |
| `Pagination` | Page controls | First/middle/last | Preserve filter query parameters |
| `Toast/Flash` | Success/error | Visible/dismissed | Announce important feedback |

Reuse existing Breeze/Inertia layout and components when sensible; don't add a full component library merely for appearance.

## 5. Interaction and UX rules

### Search/filter
- Filters and keyword can be combined.
- Selected values persist in URL/query string; browser back restores state.
- Clear filters is always available when active.
- Show “Tidak ada barang yang cocok” with a reset action for zero results.
- Preserve input focus and avoid excessive full-page loading.

### Listing forms
- Required fields marked clearly; show server validation errors next to fields.
- Preview uploaded image before submission.
- Disable submit while processing; prevent duplicate submissions.
- After success, redirect to appropriate listing/detail page with feedback.
- If editing, preserve existing photo unless user explicitly replaces it.

### Sold status
- Badge “Terjual” shown in cards and detail.
- Contact button may remain visible or be disabled based on a documented business rule; **MVP default: disable contact for sold items**, with explanatory text. Align code and requirements if changed.

### Authentication
- Clear login/register paths and contextual redirects.
- Guest attempting to post is redirected to login and returns to intended flow when supported.
- Do not hide validation details behind generic alerts.

### WhatsApp
- Label CTA explicitly “Hubungi via WhatsApp”.
- Use normalized international number and URL-encoded message.
- No claim that the message was sent automatically.
- If seller phone is unavailable, show “Kontak penjual belum tersedia” rather than a broken link.

## 6. Accessibility and quality

- Target WCAG 2.2 AA where feasible: text contrast, keyboard navigation, focus visible, semantic landmarks.
- Images have meaningful alt text based on listing title.
- Form labels connected to inputs; errors referenced via `aria-describedby`.
- No icon-only primary action without accessible label.
- Respect reduced-motion preference; avoid unnecessary animation.
- Use `Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 })` or equivalent.
- Do not show fake reviews, trust badges, user counts, or discounts.
- Optimize images and avoid cumulative layout shift using fixed aspect ratio.

## 7. Visual acceptance checklist

- [ ] Home immediately shows search and listing cards.
- [ ] UI uses consistent semantic tokens and Tailwind classes.
- [ ] Responsive at 360px, 768px, and 1440px without horizontal scroll.
- [ ] All required listing fields and metadata visible.
- [ ] Form errors, loading, empty states, sold states are designed.
- [ ] All actions have keyboard-visible focus and usable touch targets.
- [ ] UI uses Bahasa Indonesia consistently.
- [ ] No invented features, seller ratings, payment, or AI widgets.
- [ ] Screenshot comparison performed against approved reference **if supplied**.

## 8. Agent implementation guidance

Build in order: shared layout → catalog card/search/filter → detail → listing form → my listings/profile → auth polish. Keep design tokens centralized. Avoid page-specific arbitrary colors/spacing. Implement and test behavior before decorative polish. Read `10-page-specifications.md` for per-screen layouts and acceptance.
