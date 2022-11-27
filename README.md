## Social Media API
this is a simple social media api , somting like facebook , with the following features :
- ### registration and logging
- ### api authentification using `sanctum`
- ### stories system 
where user can create story and delete it , and the story will be deleted when it expire
- ### posts system
where users can create , update , get and delete posts , with authorization so that only the creator can update or delete the post
- ### groups systhem
with roles system, where user can create group , update it and delete it , but only if he is the super admin who is the creator,
the creator can add members or remove them , and upgrade them to be admins, admins can delete posts in group and accept or deny join requests
# installing
1 `composer install`
2 `php artisan migrate`
3 `php artisan serve`
16and that is it .

# endpoints

### register
`http://localhost:8000/api/register`

### login
`http://localhost:8000/api/login`

### logout
`http://localhost:8000/api/logout`

### get all users
`http://localhost:8000/api/users`

### get specific user
`http://localhost:8000/api/users/{id}`

### update user profile
`http://localhost:8000/api/users/{id}/profile_image`

### create post
`http://localhost:8000/api/posts`

### update post
`http://localhost:8000/api/posts/{id}`

### delete post
`http://localhost:8000/api/posts/{id}`

### get all posts
`http://localhost:8000/api/posts`

### get one post
`http://localhost:8000/api/posts/{id}`

### like post or dislike
`http://localhost:8000/api/posts/{id}/like`

### create comment
`http://localhost:8000/api/posts/{id}/comments`

### get,  update or delete comment
`http://localhost:8000/api/posts/{id}/comments/{id}`

### send friend request or delete it
`http://localhost:8000/api/friends/requests/toggle/{id}`

### accept friend request
`http://localhost:8000/api/friends/requests/{id}/accept`

### refuse friend request
`http://localhost:8000/api/friends/requests/{id}/deny`

### get all friend request
`http://localhost:8000/api/friends/requests`

### create group
`http://localhost:8000/api/groups`

### get , update , or delete group
`http://localhost:8000/api/groups/{id}`

### update group cover
`http://localhost:8000/api/groups/{id}/cover`

### send join request to group
`http://localhost:8000/api/groups/{group}/join`

### get join requests 
`http://localhost:8000/api/groups/{group}/requests`

### accept join request
`http://localhost:8000/api/groups/{group}/requests/{id}/accept`

### refuse join request
`http://localhost:8000/api/groups/{group}/requests/{id}/deny`

### get group members
`http://localhost:8000/api/groups/{group}/members`

### upgrade member to be admin , or if he is admin make it normal member
`http://localhost:8000/api/groups/{group}/members/{id}/upgrade`

### create post in group
`http://localhost:8000/api/groups/{group}/posts`

### get , update or delete post in group
`http://localhost:8000/api/groups/{group}/posts/{id}`

### get all posts in group
`http://localhost:8000/api/groups/{group}/posts`

### create story
`http://localhost:8000/api/story`

### get or delete story (update not supported)
`http://localhost:8000/api/story/{id}`

