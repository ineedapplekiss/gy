<?php
namespace Admin\Controller;
use Think\Controller;
class TestController extends Controller {
    public function index(){
    	dump(C("MENU"));
    }
  
}