<?php

class Post
{
    public $post_id;
    public $user_id;
    public $responding_to_id;
    public $created_at;
    public $content;

    /**
     * @var UserModel
     */
    public $user;
}

class PostAPI
{
    private $pdo;

    /**
     * @var UserAPI
     */
    private $userAPI;

    public function __construct($pdo, $userAPI)
    {
        $this->pdo = $pdo;
        $this->userAPI = $userAPI;
    }

    public function getPostById($post_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Posts WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $post_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $post = new Post();
        $post->post_id = $row['post_id'];
        $post->user_id = $row['user_id'];
        $post->responding_to_id = $row['responding_to_id'];
        $post->created_at = $row['created_at'];
        $post->content = $row['content'];

        return $post;
    }

    public function createPost($user_id, $content, $responding_to_id = null)
    {
        $post_id = uniqid();

        $stmt = $this->pdo->prepare("INSERT INTO Posts (post_id, user_id, responding_to_id, created_at, content) VALUES (:post_id, :user_id, :responding_to_id, NOW(), :content)");
        $stmt->execute([
            'post_id' => $post_id,
            'user_id' => $user_id,
            'responding_to_id' => $responding_to_id,
            'content' => $content
        ]);

        return $post_id;
    }

    public function editPost($post_id, $content)
    {
        $stmt = $this->pdo->prepare("UPDATE Posts SET content = :content WHERE post_id = :post_id");
        $stmt->execute([
            'content' => $content,
            'post_id' => $post_id
        ]);

        return $stmt->rowCount() > 0;
    }

    // order by date desc

    public function getAllPostsNotResponding()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Posts WHERE responding_to_id IS NULL ORDER BY created_at DESC");
        $stmt->execute();
        $posts = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $post = new Post();
            $post->post_id = $row['post_id'];
            $post->user_id = $row['user_id'];
            $post->user = $this->userAPI->getUserById($post->user_id);
            $post->created_at = $row['created_at'];
            $post->content = $row['content'];

            $posts[] = $post;
        }

        return $posts;
    }
}

const QUERY_CREATE_TABLE_POSTS = "CREATE TABLE Posts (
    post_id VARCHAR(255) PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL,
    responding_to_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    content TEXT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (responding_to_id) REFERENCES Posts(post_id)
);";

const QUERY_DEFAULT_POSTS = "INSERT INTO Posts (post_id, user_id, responding_to_id, content) VALUES
('1', '6606932a09bb9', NULL, 'This is the first post.'),
('2', '6606932a09bb9', NULL, 'Hello everyone!'),
('3', '6606932a09bb9', '1', 'Reply to the first post.'),
('4', '6606932a09bb9', NULL, 'Just joined the community.'),
('5', '6606932a09bb9', '3', 'Reply to the reply.'),
('6', '6606932a09bb9', NULL, 'Another post from user1.');";
