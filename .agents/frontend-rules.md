# Frontend Rules

Use:
- Vue 3 Composition API
- Pinia stores
- Axios API layer
- Vue Router

## Structure
Views handle page layout.
Components handle reusable UI.
Stores handle shared state and API calls.

## Public pages
- HomeView.vue
- InfluencerDetailView.vue
- AboutView.vue

## Admin pages
- LoginView.vue
- AdminDashboardView.vue
- AdminInfluencersView.vue
- AdminReviewsView.vue

## Rules
Do not hard-code influencer rating, review count, or links.
Use API data.
Show loading, empty, and error states.