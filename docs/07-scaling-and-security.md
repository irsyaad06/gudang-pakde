# 07 — Scaling, Reliability & Security

## Traffic assumptions (architecture exercise, not measured capacity)
| Parameter | Assumption |
|---|---|
| Daily active users | 100,000 |
| Application requests per user per day | 20 |
| Total application requests/day | 2,000,000 |
| Average requests/second | ~23.1 |
| Peak multiplier | 10x |
| Estimated peak RPS | ~231 |
| Media delivery | CDN/object storage, excluded from application RPS |

These numbers must be validated with representative load tests. Do not claim the deployed app supports 100K DAU without benchmark evidence.

## Performance strategy
1. Paginate catalog and user listing queries.
2. Eager load `category`, `user`, and relevant image records.
3. Composite indexes based on real WHERE/ORDER BY patterns.
4. Cache stable categories and popular listing reads in Redis; invalidate on edits/status changes.
5. Use CDN/object storage for media and static assets.
6. Offload image processing to queue workers if needed.
7. Scale stateless Laravel instances horizontally behind HTTPS LB.
8. Use read replica only when metrics show read pressure; account for replication lag.
9. Monitor p95 latency, 5xx, throughput, cache hit ratio, DB slow queries, connections, queue lag, CPU/RAM.

## Reliability and recovery
- Load balancer health checks and remove unhealthy nodes.
- Shared Redis sessions and shared media before multi-instance rollout.
- Automated database backups; choose RPO/RTO explicitly with stakeholders.
- Point-in-time recovery where supported; perform restore drills.
- Alert on error spikes, queue backlog, database saturation, storage failures.
- Deploy with rollback and compatible migrations.

## Security controls
| Boundary | Controls |
|---|---|
| Browser ↔ edge | HTTPS, secure cookies, CSRF, browser-safe rendering |
| Edge / LB | WAF, DDoS protection, rate limits, health checks |
| Laravel | Authentication, authorization Policies, FormRequest validation, output escaping |
| File upload | MIME/size validation, random names, storage outside executable path |
| MySQL | Private network, least privilege, encrypted connections as required, backups |
| Redis | Private access, authentication/TLS where supported |
| Secrets | `.env` excluded from Git, production secret manager |
| Logs | Avoid passwords, tokens, and sensitive user data |
| User privacy | Seller contact info only where intended, consent/notice for display |

## Threat examples and mitigations
- **IDOR:** attacker edits another member's listing → Policy authorization on every mutation.
- **SQL injection:** unsafe string concatenation → Eloquent/bound parameters.
- **XSS:** unsafe listing description HTML → render escaped text; avoid untrusted `v-html`.
- **Malicious upload:** executable disguised as image → validate MIME/size and isolate storage.
- **Brute-force login:** repeated attempts → throttling/rate limiting.
- **Session inconsistency across nodes:** local sessions → shared Redis session store.
- **Data loss:** accidental deletion or database failure → backups, restore tests, soft delete.

## Validation plan
- Feature tests for guest/member/owner/nonowner.
- Validation tests for bad fields and invalid image.
- Test search/filter combinations and pagination.
- Inspect query counts and query plans.
- Run load tests before sizing production infrastructure.
