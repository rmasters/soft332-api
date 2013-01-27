# Mumblr REST API Documentation

Mumblr is a micro-blogging service that allows it's users to publish short
messages that can be seen by anybody.

## Resource endpoints

This is a list of all endpoints exposed by the application. All endpoints need
to be made to a domain and prefixed by an API version. For this version of the
API these requests will need to be sent to:

    http://api.mumblr.rossmasters.com/1/

So, for example, a request to get information about the user `rossmasters`:

    http://api.mumblr.rossmasters.com/1/user/rossmasters

Each version of the API will increment the version number used in the URI for
all requests (a changelog will be available to describe which endpoints have
actually changed and the details).

Endpoints marked with an asterisk (*) will require authentication to use. This
is further explained in the [session](sessions.html) resource documentation.

### [Users](users.html)

#### GET /user/:id

Show information about the user with the username (id) given.

#### POST /user/:id *

Update user information for a user (which you will need to be authenticated as).

#### POST /user

Create a new user.

#### DELETE /user/:id *

Delete a user (which you will need to be authenticated as).

### [Session (Authentication)](sessions.html)

#### GET /session

Get session information for a session token.

#### POST /session

Create a new session by authenticating as a user.

#### DELETE /session *

End a session by deleting it's token.

### [Posts](posts.html)

#### POST /post *

Post a new message as a user.

#### GET /post/:id

Get an individual post.

#### DELETE /post/:id *

Delete a post made by a user (who you will need to be authenticated as).

### [Stream](stream.html)

#### GET /stream

Get the 30 most recently made posts by all users.

## Generic errors

A few errors that can be emitted by all requests include:

*   `No resource found for your request` - attempted to access a URL that doesn't have a resource attached to it. Check your URL. (404)
*   `No use for (verb) on (resource)` - attempting to use a verb (e.g. POST) on a resource that has no implementation for this case. (404)
