<?php
// Recipe model
class Recipe {
    public $id;
    public $title;
    public $description;
    public $image;
    // ...other properties...
    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->image = $data['image'] ?? '';
    }
}
