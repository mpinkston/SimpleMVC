<?php

// Include the model(s) used in this controller
include MODEL_DIR . '/BlogEntry.php';

class DefaultController
{
    /**
     * This is the default action
     */
    public function indexAction()
    {
        // this is where you might make an sql query to
        // get the data for the blog entry

        $model = new BlogEntry();
        $model->setTitle('A Blog Entry');
        $model->setText(
            'This is a very simple MVC framework. <br> ' .
            'Try <a href="/news/article">this link</a> to go to another action.'
        );

        // Now load the view you'd like to use to display the model.
        include VIEW_DIR . '/Default/index.phtml';
    }
}