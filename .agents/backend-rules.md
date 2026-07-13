# Backend Rules

Use this pattern:

Controller -> Form Request -> Service -> Model -> Resource

## Controllers
Controllers should only:
- receive request
- call service
- return resource/response

## Services
Services contain business logic:
- create influencer
- update influencer
- submit review
- approve review
- reject review
- recalculate rating

## Public data rule
Never return pending or rejected reviews in public endpoints.

## Rating rule
Influencer rating = average of approved reviews only.
Review count = count of approved reviews only.

## API version
Use /api/v1 routes.
Avoid generic /api/{slug} routes.