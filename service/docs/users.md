# Users

The **user** resource allows developers to create, view, edit and delete users.

## Endpoints

### GET /user/:username

This method allows you to get any user's information by their username.

Parameters:

*   `username` - the username of the user

Responses:

*   User information as JSON (200)
*   `error: "No user found with that username"` - no user was found.

### POST /user

This method allows you to register new users.

Parameters:

*   `name` - their username, must be lowercase and comprised of characters in the set a-z, _, 0-9. (required)
*   `password` - their password. (required)

Responses:

*   `message: "Created user"` - the user has been created. (200)
*   `error: "You cannot be authenticated and create another user"` - ensure you are not passing `X-Auth-Token` to the request. (401)
*   `error: "Supply a username and password"` - you are missing one of the two required fields. (406)

### POST /user/:username

This method allows you to update a user's details. You must first [authenticate](sessions.html) as the user.

Parameters:

*   `name` - their username, must be lowercase and comprised of characters in the set a-z, _, 0-9. (optional)
*   `password` - their password. (optional)

Responses:

*   `message: "Updated user"` - user was updated. (200)
*   `error: "You must be authenticated as the user you wish to edit"` - you need to be authenticated as this user. (401)

### DELETE /user/:username

This method allows you to delete the user account of the authenticated user.

Parameters:

*   `name` - their username.

Responses:

*   `message: "User (username) deleted" - user deleted. (200)
*   `message: "Failed to delete user (username)" - user was not deleted - an internal error.
*   `error: "You are not authorised to access this entity"` - you must be authenticated as this user. (401)
*   `error: "User not found"` - no user with that username was found. (404)
