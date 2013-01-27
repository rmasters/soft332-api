# Sessions

The **session** resource handles the authentication of a user, which is required for some requests.

## Authentication method

To authenticate with the API, you will need a pair of user credentials (username and password).

1.  Send a POST request to `/session` with the username and password (see below).
2.  You will receive a message to confirm it's success, a 128 character token and the session information.
3.  Store this token in your application, you will need to pass it to future requests by sending a HTTP header `X-Auth-Token: abcd...12f` with requests that require it.
4.  You can use this token as long as the session is valid (which will be until it's expiry date, included in the response from the POST to `/session`.

Sessions last approximately one week before the user must re-authenticate.

## Endpoints

### POST /session

This method allows you to authenticate as a user and make requests specific to their account.

Parameters:

*   `username` - the user's username. (required)
*   `password` - the user's password. (required)

Ensure you do not have a token set in X-Auth-Token before making this request.

Responses:

*   `message: "Authenticated as (username)"` - successfully authenticated, followed with session information. Remember to keep the token from this response! (200)
*   `error: "Credentials were invalid"` - no match was found for the credentials supplied. (401)
*   `error: "username and password must be supplied"` - missing a parameter. (406)

### GET /session
### GET /session/:token

This method will give you information about your session token for this user.

Parameters:

*   `token` - a session token, as provided by `POST /session`. (required, or passed as a `X-Auth-Token` header)

If a token was not in the URL, but was available in X-Auth-Token that will be used instead.

Responses:

*   `status: "Session valid"` - session has not expired (accompanied by user info, creation and expiration time). (200)
*   `status: "Session expired"` - session has expired - authenticate the user again. (200)
*   `error: "Invalid session token"` - no session was found with that token. (404)

### DELETE /session
### DELETE /session/:token

This method allows you to mark the session as closed, so that the token cannot be used for further authenticated requests.

Parameters:

*   `token` - a session token, as provided by `POST /session`. (required, or passed as a `X-Auth-Token` header)

If a token was not in the URL, but was available in X-Auth-Token that will be used instead.

Responses:

*   `status: "Session ended"` - session closed successfully. (200)
*   `status: "Session not ended, probably already closed."` - session not closed, however this is usually due to it already being deleted internally. (200)
