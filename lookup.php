<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>

	<?php
	require 'connection.php';

	try {
		$tag = $_GET['tag'];
		$sql = 'SELECT posts.id, posts.titel, posts.auteur_id, posts.datum, posts.inhoud, posts.likes, auteurs.naam
				FROM posts 
				JOIN posts_tags ON posts.id = posts_tags.post_id 
				JOIN tags ON posts_tags.tag_id = tags.id 
				INNER JOIN auteurs ON posts.auteur_id = auteurs.id 
				WHERE tags.titel = :tag';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['tag' => $tag]);
		$posts = $stmt->fetchAll(PDO::FETCH_OBJ);
	} catch (PDOException $e) {
		echo "Posts ophalen mislukt: " . $e->getMessage();
	}

	?>

	<div class="container">

		<div id="header">
			<h1>Tag: <?php echo $tag; ?> </h1>
			<a href="index.php"><button>Alle posts</button></a>
		</div>

		<?php foreach ($posts as $post) {
			$sql = 'SELECT * FROM posts_tags INNER JOIN tags ON posts_tags.tag_id=tags.id WHERE posts_tags.post_id = :post_id';
			$stmt = $conn->prepare($sql);
			$stmt->execute(['post_id' => $post->id]);
			$tags = $stmt->fetchAll(PDO::FETCH_OBJ);
			?>

			<div class="post">
				<div class="header">
					<h2><?php echo $post->titel ?></h2>
					<img src="<?php echo $post->img_url ?>" />
					<span class="right"><form action="index.php" method="post">
						<button type="submit" value="<?php echo $post->id; ?>" name="like"><?php echo $post->likes; ?> likes</button></form></span>
				</div>
				<span class="details">Geschreven op: <?php echo $post->datum . 
				' door <b>' . 
				$post->naam . '</b>';  ?></span>
				<span class="details">Tags:
					<?php foreach ($tags as $tag) { ?>
					<a href="search.php?tag=<?php echo $tag->titel; ?>"><?php echo $tag->titel;?></a>
						<?php
					}
					?></span>
				<p><?php echo $post->inhoud; ?></p>
			</div>

		<?php } ?>
	</div>
	</body>
</html>
