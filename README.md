# soft332-api

This is mumblr, a simplistic Twitter-clone that has a service and client
application. It was originally built as a submission for a poorly-described
university module: SOFT332: Windows Application Programming (2012) (in
actuality, a better name would have been REST Web Service Development).

Made up of two parts - the service (a web application exposing only a REST API)
and a client (which is a web app purely hooking into the service). Designed as
Twitter did with #newtwitter (the web app is mostly just another API client).

It uses a naive client-authentication method - the original plan was to
implement OAuth but I ran out of time. Basically a shared token that is sent
with each request (use SSL!)

Service endpoints are described by `service/public/index.php` and each REST
resource in `service/app/Resource/A` (where `A` is version 1).

Documentation is generated into a PDF using the Makefile in `service/doc`.
