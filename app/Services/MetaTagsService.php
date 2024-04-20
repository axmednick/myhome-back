<?php

namespace App\Services;

class MetaTagsService
{

    public $title;
    public $description;


    public function setMetaTags($query){

    }

    public function getMetaTags($query){
        $this->setMetaTags($query);

        return [
            'title'=>$this->title,
            'description'=>$this->description
        ];
    }

}
