<?php

namespace Resource\A;

class Stream extends Resource
{
    public function GET() {
        $mapper = new \Model\Mapper\Post;
        $posts = $mapper->fetchAllByPosted("desc", 30, 0);

        foreach ($posts as $i => $p) {
            $posts[$i] = $p->toArray();
        }
        return $posts;
    }
}
