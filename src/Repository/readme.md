# Repository

Repository provides interface for entities retrieving, persisting and removing.

## The Right Way

Implement separate database logic functions for all your needs inside the specific repositories, 
so your service classes/controllers end up looking like this:

```php
$users = $userRepository->registerUser('admin', 'secret', 'mail@exmaple.com');
$posts = $postRepository->cleanPostsBeforeDate('2017-01-18 19:21:20');
```

This way all the database logic is moved to the specific repository 
and I can type hint it's returned models. This methodology also results in cleaner 
easier to read code and further separates your business logic from the ORM / QueryBuilder.
