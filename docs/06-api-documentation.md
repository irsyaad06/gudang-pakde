# 06 — Route / API Contract

**Implementation status:** MVP uses Laravel **web routes + Inertia.js**, not public JSON REST API endpoints. Routes below are implemented in `routes/web.php`; auth routes remain in `routes/auth.php`.

## Authentication
Use the existing Breeze/Laravel auth routes and session-based auth; inspect `php artisan route:list` for actual URLs. CSRF applies to state-changing web requests.

## Implemented application routes
| Method | Path | Auth | Request | Success |
|---|---|---|---|---|
| GET | `/` | Public | `search?`, `category?`, `condition?`, `location?`, `page?` | Inertia `Catalog/Index` |
| GET | `/api/locations` | Public | `search?` or `q?`, `province?`, `type=provinces?` | JSON list of Indonesian regencies and provinces |
| GET | `/listings/{listing}` | Public | Route ID | Inertia `Listings/Show` |
| GET | `/profile` | Member | None | Inertia `Profile/Edit` with paginated owned listings and account settings |
| GET | `/my-listings` | Member | `page?` | Inertia `Listings/MyListings` |
| GET | `/listings/create` | Member | None | Inertia `Listings/Create` |
| POST | `/listings` | Member with valid WhatsApp number | `title`, `category_id`, `price`, `item_condition`, `description`, `location`, `images[]` or `image` | Redirect to detail with flash success |
| GET | `/listings/{listing}/edit` | Owner | Route ID | Inertia `Listings/Edit` |
| PUT/PATCH | `/listings/{listing}` | Owner | Editable listing fields, optional `images[]`, `delete_image_ids[]`, or single `image` | Redirect with flash success |
| DELETE | `/listings/{listing}` | Owner | Route ID | Redirect with flash success |
| PATCH | `/listings/{listing}/status` | Owner | `status=available|sold` | Redirect with flash success |

`/listings/{listing}` accepts numeric IDs only. Authenticated `/dashboard` redirects to the public catalog. Existing `PATCH /profile` supports optional `phone` (`62...` or `+62...`, stored as digits); `DELETE /profile` returns a validation error if the account has listing history (including soft-deleted listings).

## Form constraints
| Field | Constraint |
|---|---|
| `title` | Required string, max 200 |
| `category_id` | Required existing category |
| `price` | Required integer >= 0 |
| `item_condition` | One of `new`, `like_new`, `good`, `fair` |
| `description` | Required string, reasonable max length |
| `location` | Required string, max 255 |
| `images` / `image` | Minimal 1 foto barang (maksimal 5 foto); JPEG/PNG/WebP, max 2 MB per foto; form edit mendukung tambah foto baru dan `delete_image_ids` via `POST` dengan `_method=patch` |
| `status` | `available` or `sold` only |

Server validation is mandatory. Image MIME and file extension must not be trusted without validation.

## Response and error behavior
- **GET page:** Inertia component + serializable props; catalog props include paginated listings (default sort: status available first, most viewed/`views_count` DESC, newest created_at DESC) and current filters.
- **Location API:** `GET /api/locations?search={keyword}` returns JSON array of Indonesian provinces and cities/regencies for autocomplete.
- **Successful mutation:** redirect with flash success message (Post/Redirect/Get).
- **Invalid input:** Laravel validation errors displayed beside form fields.
- **Unauthenticated mutation:** redirect to login.
- **Unauthorized owner action:** HTTP `403`.
- **Missing listing:** HTTP `404`.
- **Unexpected error:** generic user-facing error, details only in server logs.

### Example Inertia page props
```json
{
  "listings": {
    "data": [
      {
        "id": 1,
        "title": "Sepeda bekas",
        "price": 1200000,
        "item_condition": "good",
        "status": "available",
        "location": "Bandung",
        "image_url": "/storage/listings/example.webp"
      }
    ],
    "current_page": 1,
    "last_page": 1
  },
  "filters": {"search": "", "category": null, "condition": null}
}
```
This is illustrative of the implemented prop structure; pagination also includes Laravel paginator metadata. Cards include `category: {id, name, slug}`, detail additionally includes `description`, `user: {name}`, and `whatsapp_url`. Public list/detail props do not expose seller phone or email. The profile and `/my-listings` props contain `ownedListings` and `listings` paginators respectively.

## WhatsApp contact
The detail page opens `https://wa.me/<normalized_phone>?text=<url_encoded_message>` for available items. The URL is built server-side using the seller's validated profile number, title and listing ID; sold items return `whatsapp_url: null`. No seller phone is exposed as a separate public prop. No API key or WhatsApp Business API; do not assume message delivery.

## Future external API
If mobile clients are introduced, define `/api/v1/*` JSON endpoints and choose appropriate authentication (e.g., Sanctum). **Do not implement this in the one-day MVP unless explicitly requested.**
