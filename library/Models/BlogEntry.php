<?php

/**
 * This is a class that might appear in a class diagram.
 */
class BlogEntry
{
    protected $title;

    protected $text;

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }
}