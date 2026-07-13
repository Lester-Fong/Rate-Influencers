
# RateInfluencers — Core System Architecture (MVP)

## Vision
A fun discovery platform inspired by Earth Reviews where users can explore influencers, read community reviews, and submit their own opinions about the creator's **public content**.

**Tech Stack**
- Laravel 11
- Vue 3
- Pinia
- Vue Router
- Axios
- Tailwind CSS
- MySQL
- Laravel Sanctum

---

# Core Features

## Public
- Browse influencer grid
- Search influencers
- Influencer detail page
- Average 5-star rating
- Read approved reviews
- Submit a review (pending approval)

## Admin
- Login/Logout
- Dashboard
- Create/Edit/Delete Influencers
- Approve/Reject Reviews

---

# Architecture

```
Browser
    │
Vue 3 SPA
    │
Axios
    │
Laravel API
    │
Services
    │
Eloquent
    │
MySQL
```

## Backend Structure

```
Controllers
    ↓
Form Requests
    ↓
Services
    ↓
Models
    ↓
Resources
```

Controllers stay thin.
Business logic belongs in Services.

---

# Database

## administrators
- id
- fullname
- email
- password

## influencers
- id
- name
- slug
- bio
- profile_picture
- rating
- review_count
- facebook_link
- youtube_link
- tiktok_link
- instagram_link
- created_at
- updated_at

## reviews
- id
- influencer_id
- reviewer_name
- rating
- review
- status (pending, approved, rejected)
- created_at

Relationship

```
Influencer
    1
    │
    └────── *
         Reviews
```

---

# Public API

```
GET    /api/v1/influencers
GET    /api/v1/influencers/{slug}

GET    /api/v1/influencers/{slug}/reviews
POST   /api/v1/influencers/{slug}/reviews
```

Authentication

```
GET    /sanctum/csrf-cookie
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
GET    /api/v1/auth/me
```

Admin

```
GET    /api/v1/admin/influencers
POST   /api/v1/admin/influencers
PATCH  /api/v1/admin/influencers/{id}
DELETE /api/v1/admin/influencers/{id}

GET    /api/v1/admin/reviews
POST   /api/v1/admin/reviews/{id}/approve
POST   /api/v1/admin/reviews/{id}/reject
```

---

# Rating Logic

- Reviews are submitted as **pending**
- Only **approved** reviews appear publicly
- Influencer rating = average of approved reviews
- Review count = approved reviews only

---

# Vue Pages

Public

- Home
- Influencer Detail
- About

Admin

- Login
- Dashboard
- Influencers
- Reviews

---

# Development Order

1. Fix authentication (`/auth/me`)
2. Influencer CRUD
3. Public influencer listing
4. Review submission
5. Review moderation
6. Finish UI polish

---

# Project Goal

Keep the project simple, fun, and visually engaging like Earth Reviews while focusing on one thing:

> Help users discover influencers and decide whether their content is worth following based on community-approved reviews.
