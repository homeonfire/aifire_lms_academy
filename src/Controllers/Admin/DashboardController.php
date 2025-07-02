<?php
// src/Controllers/Admin/DashboardController.php
require_once __DIR__ . '/AdminController.php';

class DashboardController extends AdminController {

    public function index() {
        $this->render('admin/dashboard/index', ['title' => 'Панель администратора']);
    }
}