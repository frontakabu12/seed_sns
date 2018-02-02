<?php
require('dbconnect.php'); 

$tag_id = $_GET["tag_id"];

// tag名の取得
$get_tag_sql = "SELECT * FROM `tags` WHERE `id`=".$tag_id ;

// SQL実行
// $data = array($_GET["tag_id"]);
$get_tag_stmt = $dbh->prepare($get_tag_sql);
$get_tag_stmt->execute();

// フェッチ
$tag_name = $get_tag_stmt->fetch(PDO::FETCH_ASSOC);



// tagを含んだ一覧の取得
$tag_search_sql = "SELECT `tweets`.*,`members`.`nick_name`,`members`.`picture_path` FROM `tweets` INNER JOIN `tweet_tags` ON `tweets`.`tweet_id` = `tweet_tags`.`tweet_id` INNER JOIN `members` ON `tweets`.`member_id` = `members`.`member_id` WHERE `tag_id` =".$tag_id;

// // // SQL実行
$tag_search_stmt = $dbh->prepare($tag_search_sql);
$tag_search_stmt->execute();

// フェッチ


$tag_tweet_list = array();
    while(1){
      $tag_tweet = $tag_search_stmt->fetch(PDO::FETCH_ASSOC);

      if($tag_tweet == false){
        break;
      }

      $tag_tweet_list[] = $tag_tweet;
    }


?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <h4>#<?php echo $tag_name["tag"]; ?>の検索結果</h4>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
        <?php foreach ($tag_tweet_list as $tag_tweet) { ?>
        
        <!-- 繰り返すタグが書かれる場所 -->
        <div class="msg">
          <img src="picture_path/<?php echo $tag_tweet["picture_path"]; ?>" width="48" height="48">
          <p>
            <?php echo $tag_tweet["tweet"]; ?><span class="name"> (<?php echo $tag_tweet["nick_name"]; ?>) </span>
            [<a href="reply.php?tweet_id=<?php echo $tag_tweet["tweet_id"]; ?>">Re</a>]
          </p>
          <p class="day">
            <!-- <a href="view.html"> -->
              <?php 
              $modify_date = $tag_tweet["modified"];
              // date関数　書式を時間に変更するとき
              // strtotime 文字型(string)のデータを日時型に変換できる
              // 24時間表記：H, 12時間表記：h　
              $modify_date = date("Y-m-d H:i", strtotime($modify_date));
             echo $modify_date ; ?>
            <!-- </a> -->
          </p>
        </div>
        <?php } ?>

       <!--  <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            つぶやき４<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.html">
              2016-01-28 18:04
            </a>
          </p>
        </div> -->
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>