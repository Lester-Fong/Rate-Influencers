# AGENTS.md

## Project
RateInfluencers is a simple Laravel 11 + Vue 3 review platform for influencers.

The goal is a small MVP inspired by Earth Reviews:
- public influencer grid
- influencer detail page
- 5-star reviews
- admin CRUD for influencers
- admin approve/reject reviews

Do not add advanced features unless explicitly requested.

## Read first
Before coding, read:
- .agents/core-architecture.md
- .agents/backend-rules.md
- .agents/frontend-rules.md
- .agents/mvp-scope.md
- .agents/api-contract.md
- .agents/database-plan.md

## Rules
- Keep changes small.
- Do not rewrite unrelated code.
- Do not add new packages without asking.
- Backend logic goes in Services, not fat controllers.
- Vue components should stay simple and reusable.
- Public users only see approved reviews.
- Influencer rating is calculated from approved reviews only.
- Always check existing files before creating new ones.
- Use .agents/mvp-checklist.md to track progress. After every task, update the checklist.

## Verification
After backend changes:
- run Laravel tests if available
- run php artisan route:list
- check migrations

After frontend changes:
- run npm run build
- check console errors

