<?php
require_once __DIR__ . "/../../utils/helpers.php";
require_once __DIR__ . "/../../entities/all_entites.php";

$postId = getLastWordOfCurrentUrlPath();
$post = $postsAPI->getPostById($postId);

if ($post->responding_to_id) {
    redirect("/forum/{$post->responding_to_id}");
    exit();
}

if (!$post) {
    redirect('/forum');
    exit();
}

$responses = $postsAPI->getResponseOfPost($postId);

?>
<div class="flex flex-col gap-4  md:mx-20 mx-4 my-20">
    <a href="/forum">
        <button class="inline-flex my-2 justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-eventit-500 hover:bg-eventit-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-eventit-500">
            Back to forum
        </button>
    </a>
    <main class="flex-1 flex flex-col gap-4 p-4 lg:gap-8 lg:p-6">
        <div>
            <header class="bg-white shadow-md py-4 px-8">
                <h1 class="text-3xl font-semibold"><?php echo $post->title; ?></h1>

                <div class="flex flex-row items-center gap-2 ">
                    <p>Posté par</p>
                    <img src="<?php echo $post->user->image_url ?>" width="32" height="32" class="rounded-full" alt="Avatar" style="aspect-ratio: 48 / 48; object-fit: cover;">
                    <p class="text-gray-700 font-bold"><?php echo $post->user->getFullName(); ?></p>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('F j, Y, g:i a', strtotime($post->created_at)); ?></span>
                <p class="text-gray-700 mt-2"><?php echo $post->content; ?></p>
            </header>

            <main class="max-w-3xl mx-auto mt-8">
                <section>
                    <div class="flex flex-row justify-center items-center">
                        <h2 class="text-xl font-semibold mb-4">Responses</h2>
                        <div class="ml-auto"><a href="/forum/create_post.php?responding=<?php echo $postId; ?>">
                                <div class="bg-eventit-500 text-white rounded-lg shadow-md p-4 mb-4 w-fit">
                                    <p class="font-bold">Respond to this post</p>
                                </div>

                            </a>
                        </div>
                    </div>
                    <?php

                    if (count($responses) == 0) {
                    ?>
                        <p class="text-gray-700">No responses found.</p>
                    <?php
                    }

                    foreach ($responses as $response) : ?>
                        <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                            <div class="flex flex-row items-center gap-2 my-2">

                                <img src="<?php echo $response->user->image_url ?>" width="48" height="48" class="rounded-full" alt="Avatar" style="aspect-ratio: 48 / 48; object-fit: cover;">

                                <p class="text-gray-700 font-bold"><?php echo $response->user->getFullName(); ?></p>
                            </div>
                            <p class="text-gray-700"><?php echo $response->content; ?></p>
                        </div>
                    <?php endforeach; ?>
                </section>
            </main>


        </div>
    </main>
</div>