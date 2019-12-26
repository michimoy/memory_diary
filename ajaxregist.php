<?PHP
require('function.php');

session_save_path("/var/tmp/");
session_start();

$post_kerword = filter_input(INPUT_POST,"kerword");
$post_sort = filter_input(INPUT_POST,"sort");
$post_category = filter_input(INPUT_POST,"category");
$post_character = filter_input(INPUT_POST,"character");

$docategory=filter_input(INPUT_POST,"docategory");
$docharacter=filter_input(INPUT_POST,"docharacter");
$dokerword=filter_input(INPUT_POST,"dokerword");
$dosort=filter_input(INPUT_POST,"dosort");

$category=[];
$character=[];

header("Access-Control-Allow-Origin: http://localhost:8888/memorydiary");
header('Access-Control-Allow-Credentials: true');


if(isset($_SESSION["category"])){
  if(is_array($_SESSION["category"])){
    $category=$_SESSION["category"];
  }else{
    unset($_SESSION["category"]);
  }
}

if(isset($_SESSION["character"])){
  if(is_array($_SESSION["character"])){
    $character=$_SESSION["character"];
  }else{
    unset($_SESSION["character"]);
  }
}

if (!is_null($post_category)) {
  echo json_encode($post_category);
array_session_push($post_category,'category',$docategory,$category);
}

if (!is_null($post_character)) {
array_session_push($post_character,'character',$docharacter,$character);
}

if (!is_null($post_kerword)) {
array_session_push($post_kerword,'kerword',$dokerword);
}

if (!is_null($post_sort)) {
array_session_push($post_sort,'sort',$dosort);
}

function array_session_push($post_item,$session_name,$do_item,$array_item = ''){

  if (is_array($array_item)) {
    if($do_item=="del" and in_array($post_item,$array_item)){
      $array_item=array_filter($array_item,function($x) use($post_item){return $x!==$post_item;});
      $array_item=array_values($array_item);
    }
    if($do_item=="add" and !in_array($post_item,$array_item)){
      array_push($array_item,$post_item);
    }
    $_SESSION[$session_name]=$array_item;
    echo json_encode($_SESSION[$session_name]);
  }else{
    if($do_item=="del"){
      unset($_SESSION[$session_name]);
    }if($do_item=="add"){
      $_SESSION[$session_name] = $post_item;
      echo json_encode($_SESSION[$session_name]);
    }
  }
}
?>
