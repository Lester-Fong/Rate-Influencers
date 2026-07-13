# Database Plan

## administrators
- id
- fullname
- email
- password
- timestamps

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
- timestamps

## reviews
- id
- influencer_id
- reviewer_name
- rating
- review
- status: pending, approved, rejected
- timestamps

## Relationship
Influencer has many Reviews.
Review belongs to Influencer.