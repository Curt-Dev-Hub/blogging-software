<!-- Page for displaying the Post List 
Use a loop to iterate over the fetched posts and display them in a list format.
Include links to view and edit each post.
-->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="my-posts.css">
    <link rel="stylesheet" href="<?php echo "../../fonts/roboto.css" ?>">
    <title>My Posts</title>
</head>
<body>
    <Main>
        <h1>Here are your posts</h1>
        <section class="post-list">
            <h2>These are your published Posts:</h2>
        </section>
        <section class="post-list">
            <h2>These posts are your unpublished drafts:</h2>
        </section>
    </Main>
</body>

</html>