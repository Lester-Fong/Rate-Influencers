# API Contract

## Public

GET /api/v1/influencers
Returns influencer cards.

GET /api/v1/influencers/{slug}
Returns influencer details.

GET /api/v1/influencers/{slug}/reviews
Returns approved reviews only.

POST /api/v1/influencers/{slug}/reviews
Creates a pending review.

## Auth

GET /sanctum/csrf-cookie
POST /api/v1/auth/login
POST /api/v1/auth/logout
GET /api/v1/auth/me

## Admin

GET /api/v1/admin/influencers
POST /api/v1/admin/influencers
PATCH /api/v1/admin/influencers/{id}
DELETE /api/v1/admin/influencers/{id}

GET /api/v1/admin/reviews
POST /api/v1/admin/reviews/{id}/approve
POST /api/v1/admin/reviews/{id}/reject