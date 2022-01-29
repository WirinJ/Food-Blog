<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
    <?php
    include 'connection.php';

    try {
        $posts = $conn->query('SELECT * FROM `posts` INNER JOIN auteurs ON posts.auteur_id = auteurs.id ORDER BY likes DESC;')->fetchAll();
        $populair = $conn->query('SELECT foodblog.auteurs.naam, likes AS "totaal_likes" FROM posts INNER JOIN auteurs ON posts.auteur_id = auteurs.id HAVING likes > 10;')->fetchAll();

        if (isset($_POST['like'])) {
            $sql = "UPDATE posts SET likes = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_POST['like'] + 1, $_POST['id']]);
            header("Refresh: 0");
        }
    } catch (Exception $e) {
        die("oeps, er is iets fout gegaan! probeer het later opnieuw.");
    }
        
    ?>

    <div class="container">

        <div id="header">
            <h1>Foodblog</h1>
            <a href="new_post.php"><button>Nieuwe post</button></a>
        </div>

        <b>Populaire chefs</b>
          <ul>
            <?php

            foreach ($populair as $auteur) {
                echo '<li>' . $auteur['naam'] . '</li>';
            }

            ?>
          </ul> 
        <br/>

        <?php
        foreach ($posts as $post) {
            $tags = $conn->query('SELECT * FROM `posts_tags` INNER JOIN tags ON posts_tags.tag_id = tags.id WHERE `post_id` = ' . $post[0] . ';')->fetchAll();
            echo '<div class="post">

                    <div class="header">
                        <h2>' . $post['titel'] . '</h2>
                        <img src=' . $post['img_url'] . ' />
                    </div>

                    <span class="right">
                        <form action="index.php" method="post">
                            <button type="submit" value="' . $post['likes'] . '" name="like">' . $post['likes'] . ' likes
                            </button>
                            <input type="text" hidden name="id" value=' . $post[0] . '>
                        </form>
                    </span>

                    <span class="details">Geschreven op: ' . $post['datum'] . ' door <b> ' . $post['naam'] . '</b></span>';

            if (!($tags == [])) {
                echo '<span class="details">Tags: ';
                foreach ($tags as $tag) {
                    echo '<a href="lookup.php?tag=' . $tag['titel'] . '">#' . $tag['titel'] . '</a> ';
                }
            }
                    
            echo '</b></span>
            <p>' . $post['inhoud'] . '</p>
            </div>';
        }
        ?>

    </div>
    </body>
</html>