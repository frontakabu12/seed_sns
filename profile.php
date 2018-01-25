<?php

// session_start();
// GET送信されたmember_idを使ってプロフィール情報をmembersテーブルから取得

require('function.php');


require('dbconnect.php');


$sql = "SELECT * FROM `members` WHERE `member_id`=".$_GET["member_id"];

// $_GET["member_id"]は見たい人のid

$stmt = $dbh->prepare($sql);
$stmt->execute();

$profile_member = $stmt->fetch(PDO::FETCH_ASSOC);



// 自分もフォローしていたら1,フォローしていなかったら0を取得
$fl_flag_sql = "SELECT COUNT(*) as `cnt` FROM `follows` WHERE `member_id`=".$_SESSION["id"]." AND `follower_id`=".$_GET["member_id"];

        $fl_stmt = $dbh->prepare($fl_flag_sql);
        $fl_stmt->execute();
        $fl_flag = $fl_stmt->fetch(PDO::FETCH_ASSOC);


// フォロー処理
// profile.php?follow_id=9というリンクが押された＝フォローボタンが押された

if(isset($_GET["follow_id"])){
// follow情報を記録するSQL文を作成
  $sql = "INSERT INTO `follows` (`member_id`, `follower_id`) VALUES (?,?);";

  $data = array($_SESSION["id"],$_GET["follow_id"]);
  $fl_stmt = $dbh->prepare($sql);
  $fl_stmt->execute($data);

}

// フォロー解除処理
if(isset($_GET["unfollow_id"])){

  // 登録されているfollow情報をfollowsテーブルから削除
  $sql = "DELETE FROM `follows` WHERE `follower_id` =".$_GET["unfollow_id"]." AND `member_id`=".$_SESSION["id"];

  // SQL実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  header("Location: profile.php?member_id=".$_GET["member_id"]);

}

// $_GET["member_id"]のつぶやきを一覧で表示

$sql = "SELECT `tweets`.*,`members`.`nick_name`, `members`.`picture_path` FROM `tweets` INNER JOIN `members` ON `tweets`.`member_id` = `members`.`member_id` WHERE `delete_flag`=0 AND `tweets`.`member_id`=".$_GET["member_id"]." ORDER BY `modified` DESC ";

      
//SQL文を実行

$stmt = $dbh->prepare($sql);
$stmt->execute();

$member_tweet_list = array();


// 複数行のデータを取得するためのループ
    while (1) {
      $one_tweet = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($one_tweet == false) {
        break;
      }else{


        $member_tweet_list[] = $one_tweet;

      }
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
      <div class="col-md-3 content-margin-top">
        <img src="picture_path/<?php echo $profile_member["picture_path"]; ?>" width="250" height="250">
        <h3><?php echo $profile_member["nick_name"]; ?> </h3>
        <?php if($_SESSION["id"] != $profile_member["member_id"]){  ?>

        <?php if($fl_flag["cnt"] == 0) { ?>
        <!-- フォローボタン -->
        <a href="profile.php?member_id=<?php echo $profile_member["member_id"]; ?>&follow_id=<?php echo $profile_member["member_id"]; ?>">
          <button class="btn btn-block btn-default" >フォロー</button></a>

          <?php }else{  ?>

        <!-- フォロー解除ボタン -->
        <a href="profile.php?member_id=<?php echo $profile_member["member_id"]; ?>&unfollow_id=<?php echo $profile_member["member_id"]; ?>">
          <button class="btn btn-block btn-default">フォロー解除</button></a>
        <?php } ?>
        <?php } ?>
        <br>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>

      <div class="col-md-9 content-margin-top">
      <?php foreach ($member_tweet_list as $one_tweet) { ?>

        <!-- 繰り返すタグが書かれる場所 -->
        <div class="msg">
          <img src="picture_path/<?php echo $profile_member["picture_path"]; ?>" width="100" height="100">
          <p>投稿者 : <span class="name"> <?php echo $profile_member["nick_name"]; ?> </span></p>
          <p>
            つぶやき : <br>
            <?php echo $one_tweet["tweet"]; ?>
            
          </p>
          <p class="day">
            <?php 
              $modify_date = $one_tweet["modified"];

              // date関数　書式を時間に変更するとき
              // strtotime 文字型(string)のデータを日時型に変換できる
              // 24時間表記：H, 12時間表記：h　
              $modify_date = date("Y-m-d H:i", strtotime($modify_date));
             echo $modify_date ; ?>
             <?php if($_SESSION["id"] == $one_tweet["member_id"]){  ?>
             [<a onclick="return confirm('削除します、よろしいですか？');" href="delete.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>" style="color: #F33;">削除</a>]
            <?php }  ?>
          </p>
        </div>
        <?php } ?>


        <!-- <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="100" height="100">
          <p>投稿者 : <span class="name"> Seed kun </span></p>
          <p>
            つぶやき : <br>
            つぶやき４つぶやき４つぶやき４
          </p>
          <p class="day">
            2016-01-28 18:04
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
        <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="100" height="100">
          <p>投稿者 : <span class="name"> Seed kun </span></p>
          <p>
            つぶやき : <br>
            つぶやき４つぶやき４つぶやき４
          </p>
          <p class="day">
            2016-01-28 18:04
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
        <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="100" height="100">
          <p>投稿者 : <span class="name"> Seed kun </span></p>
          <p>
            つぶやき : <br>
            つぶやき４つぶやき４つぶやき４
          </p>
          <p class="day">
            2016-01-28 18:04
            [<a href="#" style="color: #F33;">削除</a>]
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
