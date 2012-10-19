<?php

// Include the model(s) used in this controller
include MODEL_DIR . '/BlogEntry.php';

class NewsController
{
    public function articleAction()
    {
        $model = new BlogEntry();
        $model->setTitle('Article');
        $model->setText(
            'This could be a news article. <br>' .
            'Now try <a href="/game/go">this</a>'
        );

        include VIEW_DIR . '/News/article.phtml';
    }
}