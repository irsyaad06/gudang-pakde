# 09 — UI/UX Design System: Gudang Pakde

> **Status:** Updated to follow the **Sellora commerce screenshot supplied by the user in conversation**. That reference supersedes the earlier green marketplace direction: light-gray canvas, white rounded panels, pill navigation, charcoal active states and primary actions, violet brand/focus accents, and large inset product imagery. The screenshot is visual direction, not a requirement to copy Sellora's dashboard features or claim pixel-perfect fidelity. Only FR-01–FR-09 are implemented.
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
| `brand-50` | `#F5F3FF` | Subtle violet backgrounds |
| `brand-100` | `#EDE9FE` | Soft emphasis |
| `brand-600` | `#6D5CE7` | Brand mark and focus accents |
| `brand-700` | `#5B4BC4` | Accent hover |
| `brand-800` | `#4637A5` | Strong accent text |
| `ink-900` | `#202024` | Primary text, dark buttons and active pills |
| `ink-600` | `#62626B` | Secondary text |
| `ink-500` | `#777780` | Muted labels |
| `surface` | `#FFFFFF` | Content panels/cards/forms |
| `canvas` | `#F3F3F5` | Application background |
| `photo` | `#F7F7F8` | Inset image stages |
| `border` | `#E8E8EC` | Subtle dividers/controls |
| `danger` | `#DC2626` | Validation/destructive |
| `success` | `#15803D` | Available status text |
| `success-soft` | `#F0FDF4` | Available status background |
| `warning` | `#A16207` | Sold status text |
| `sold` | `#FFFBEB` | Sold status background |
| `focus` | `#6D5CE7` | Focus ring |

Tokens are centralized in `tailwind.config.js` and interpret the supplied reference rather than claiming sampled exact colors. Use charcoal with white text for primary actions, violet for brand/focus, and green/amber only for honest availability feedback. Verify contrast in context.

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
- Application container: maximum 1440px with responsive 12/24/32px side gutters.
- Header: three floating white pill groups (brand, functional navigation, search/account). Navigation wraps to a second row on mobile; no fake Sellora menus.
- Content panels: white, 24–32px corners, subtle borders/shadow; catalog and owner management share a panel-based composition.
- Cards: inset image stage, thin border, `rounded-xl`, restrained elevation. Status is a full-width text strip in catalog cards.
- Buttons/inputs: minimum 44px touch target; pill primary/secondary buttons, rounded 12–16px fields, shared focus treatment.
- Icons: consistent 18–20px line icons; always pair critical actions with labels.
- Catalog image stage: **4:3**, inset with `object-contain` so the whole product stays visible, neutral placeholder when absent. Detail/upload use a larger proportional stage; mobile/tablet upload preview height is capped for efficient scrolling.
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
- [ ] Visual review against the user-supplied Sellora direction; do not claim exact pixel matching or copy features beyond the MVP.

## 8. Agent implementation guidance

Build in order: shared layout → catalog card/search/filter → detail → listing form → my listings/profile → auth polish. Keep design tokens centralized. Avoid page-specific arbitrary colors/spacing. Implement and test behavior before decorative polish. Read `10-page-specifications.md` for per-screen layouts and acceptance.

## 9. Implemented redesign and verification

- Shared `AppIcon`, `BrandMark`, `StatusBadge`, listing formatters, input/button controls, `ListingForm`, and `OwnedListings` keep visual and interaction behavior reusable without a UI-library dependency.
- The redesign retains Laravel routes, Inertia page contracts, authentication, upload rules, and marketplace business logic (FR-01–FR-09).
- Chrome/CDP visual review covered catalog, detail, create, edit, my listings, profile, login, and register at 360/768/1440 CSS px. Catalog renders 2/3/4 columns. Create/edit stack on mobile/tablet and use two columns on desktop. No horizontal overflow, broken images, JavaScript exceptions, or native-modal errors were detected in reviewed states.
- Browser interactions checked combined search/category/condition filters and reset, account navigation, login/logout, guest-post redirect, and modal cancel/Escape/focus return. This is not a full accessibility certification or complete browser mutation suite; no real WhatsApp message was sent.
- Latest validation: `php artisan test` passed (36 tests, 371 assertions), `npm run build` passed, and `git diff --check` passed.
- Local screenshots/measurements are ignored development artifacts under `storage/app/ui-review/sellora-*`. Synthetic QA records were removed after capture; existing user records were not edited by the review. Demo imagery remains clearly documented original illustrations, not actual product photography from the Sellora reference.
