# Posts

The **post** resource allows creation, viewing and deletion of messages.

## Endpoints

### GET /post/:id

Retrieves a single message.

Parameters:

*   `id` - the post ID number. (required)

Responses:

*   Post information (200)
*   `error: "Post not found"` - no post with that ID. (404)

### POST /post

Allows you to post a message, when you are [authenticated](sessions.html) as a user.

Parameters:

*   `message` - the message to post. (required)

Responses:

*   `message: "Posted successfully"` (200)
*   `error: "Missing message parameter"` - you are missing the message parameter. (406)
*   `error: "You are not authorised to access this entity"` - you have not passed an authentication token. (401)

### DELETE /post/:id

Deletes a post the authenticated user has made.

Parameters:

*   `id` - the post ID number. (required)

Responses:

*   `message: "Post deleted"` (200)
*   `message: "Failed to delete post"` - an internal error
*   `error: "Post not found"` - no post with that ID. (404)
*   `error: "You are not authorised to access this entity"` - you have not passed an authentication token. (401)
