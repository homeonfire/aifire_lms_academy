<?php
require_once "app/core/Controller.php";

class LandController extends Controller {
    public function index() {
        $this->render("land/index");
    }
}