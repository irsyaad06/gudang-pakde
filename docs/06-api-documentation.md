# 06 — Route / API Contract

**Architectural decision:** MVP uses Laravel **web routes + Inertia.js**. These are **not public JSON REST API endpoints**. Routes below are **planned**, not guaranteed implemented; align with actual starter-kit routes.

## Authentication
Use the existing Breeze/Laravel auth routes and session-based auth; inspect `php artisan route:list` for actual URLs. CSRF applies to state-changing web requests.

## Planned application routes
| Method | Path | Auth | Request | Success |
|---|---|---|---|---|
| GET | `/` | Public | `search?`, `category?`, `condition?`, `page?` | Inertia `Catalog/Index` |
| GET | `/listings/{listing}` | Public | Route ID | Inertia `Listings/Show` |
| GET | `/profile` | Member | None | Inertia profile page with owned listings |
| GET | `/my-listings` | Member | `page?` | Inertia `Listings/MyListings` |
| GET | `/listings/create` | Member | None | Inertia `Listings/Create` |
| POST | `/listings` | Member | `title`, `category_id`, `price`, `item_condition`, `description`, `location`, `image` | Redirect with flash success |
| GET | `/listings/{listing}/edit` | Owner | Route ID | Inertia `Listings/Edit` |
| PUT/PATCH | `/listings/{listing}` | Owner | Editable listing fields, optional image | Redirect with flash success |
| DELETE | `/listings/{listing}` | Owner | Route ID | Redirect with flash success |
| PATCH | `/listings/{listing}/status` | Owner | `status=available|sold` | Redirect with flash success |

**Routing caution:** Register `/listings/create` before `/listings/{listing}` to avoid collision; use numeric model constraints if appropriate. Avoid duplicate routes with existing profile endpoints.

## Form constraints
| Field | Constraint |
|---|---|
| `title` | Required string, max 200 |
| `category_id` | Required existing category |
| `price` | Required integer >= 0 |
| `item_condition` | One of `new`, `like_new`, `good`, `fair` |
| `description` | Required string, reasonable max length |
| `location` | Required string, max 255 |
| `image` | Required on create; optional on update; JPEG/PNG/WebP, max size e.g. 2 MB |
| `status` | `available` or `sold` only |

Server validation is mandatory. Image MIME and file extension must not be trusted without validation.

## Response and error behavior
- **GET page:** Inertia component + serializable props; catalog props include paginated listings and current filters.
- **Successful mutation:** redirect with flash success message (Post/Redirect/Get).
- **Invalid input:** Laravel validation errors displayed beside form fields.
- **Unauthenticated mutation:** redirect to login.
- **Unauthorized owner action:** HTTP `403`.
- **Missing listing:** HTTP `404`.
- **Unexpected error:** generic user-facing error, details only in server logs.

### Example planned Inertia page props
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
This is **illustrative**, not an actual runtime response. Avoid exposing sensitive seller fields in public list props.

## WhatsApp contact
The frontend opens a URL of the form `https://wa.me/<normalized_phone>?text=<url_encoded_message>`. The seller's valid contact number is taken from their profile; no API key or WhatsApp Business API. Do not assume message delivery.

## Future external API
If mobile clients are introduced, define `/api/v1/*` JSON endpoints and choose appropriate authentication (e.g., Sanctum). **Do not implement this in the one-day MVP unless explicitly requested.**
