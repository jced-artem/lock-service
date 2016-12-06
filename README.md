# lock-service
Symfony LockHandler analog, but for multi-server architecture.
In case when you have several servers which runs several workers or jobs you might want to lock them to prevent launch same jobs in many servers.

# Require
`symfony/filesystem`

# Install
```
composer require jced-artem/lock-service
```
