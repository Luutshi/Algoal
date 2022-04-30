<?php

namespace Mvc\Controllers;

use Config\Controller;
use Mvc\Models\DataModel;

class PageController extends Controller
{
    private DataModel $dataModel;

    public function __construct()
    {
        $this->dataModel = new DataModel();
        parent::__construct();
    }

    public function base()
    {
        echo $this->twig->render('Page/page.html.twig');
    }
}