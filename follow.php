<?php

session_start();
// GET送信されたmember_idを使ってプロフィール情報をmembersテーブルから取得

require('dbconnect.php');

// ログインしている人のプロフィール情報をmembersテーブルから取得
$sql = "SELECT * FROM `members` WHERE `member_id`=".$_SESSION["id"];


$stmt = $dbh->prepare($sql);
$stmt->execute();

$profile_member = $stmt->fetch(PDO::FETCH_ASSOC);




// フォロー処理
// profile.php?follow_id=9というリンクが押された＝フォローボタンが押された

if(isset($_GET["follow_id"])){
  // follow情報を記録するSQL文を作成
  $sql = "INSERT INTO `follows` (`member_id`, `follower_id`) VALUES (?,?);";

  $data = array($_SESSION["id"],$_GET["follow_id"]);
  $fl_stmt = $dbh->prepare($sql);
  $fl_stmt->execute($data);

  // フォローボタンを押す前の状態に戻す(再読み込みで再度フォロー処理が動くのを防ぐ)
  header("Location: follow.php");
}

// フォロー解除処理
if(isset($_GET["unfollow_id"])){

  // 登録されているfollow情報をfollowsテーブルから削除
  $sql = "DELETE FROM `follows` WHERE `follower_id` =".$_GET["unfollow_id"]." AND `member_id`=".$_SESSION["id"];

  // SQL実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  // 一覧ページへ戻る
  header("Location: follow.php");



 }


// $_GET["member_id"]のつぶやきを一覧で表示

$sql = "SELECT * FROM `members` INNER JOIN `follows` ON `members`.`member_id`= `follows`.`member_id` WHERE `follows`.`follower_id`=".$_SESSION["id"]." ORDER BY `follows`.`created` DESC ";

      
//SQL文を実行

$stmt = $dbh->prepare($sql);
$stmt->execute();

// 一覧用の配列を用意
$member_tweet_list = array();


// 複数行のデータを取得するためのループ
    while (1) {
      $one_tweet = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($one_tweet == false) {
        break;
      }else{
        // following_flagを用意して、自分もフォローしていたら1,フォローしていなかったら0を代入する
        $fl_flag_sql = "SELECT COUNT(*) as `cnt` FROM `follows` WHERE `member_id`=".$_SESSION["id"]." AND `follower_id`=".$one_tweet["member_id"];

        $fl_stmt = $dbh->prepare($fl_flag_sql);
        $fl_stmt->execute();
        $fl_flag = $fl_stmt->fetch(PDO::FETCH_ASSOC);

        $one_tweet["following_flag"] = $fl_flag["cnt"];

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
        <a href="profile.php?member_id=<?php echo $profile_member["member_id"]; ?>&follow_id=<?php echo $profile_member["member_id"]; ?>">
        <button class="btn btn-block btn-default" >フォロー</button></a><?php } ?>
        <br>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>

      <div class="col-md-9 content-margin-top">
        <div class="msg_header">
          <a href="#">Followers<span class="badge badge-pill badge-default"><?php 
          // count 配列に幾つデータが存在しているか数えてくれる関数
          echo count($member_tweet_list); ?></span></a> 
        </div>
      <?php foreach ($member_tweet_list as $one_tweet) { ?>

        <!-- 繰り返すタグが書かれる場所 -->
        <div class="msg">
          <img src="picture_path/<?php echo $one_tweet["picture_path"]; ?>" width="48" height="48">
          <p> <span class="name"> <?php echo $one_tweet["nick_name"]; ?> </span></p>

        <?php if($one_tweet["following_flag"] == 0){  ?>
        <a href="follow.php?follow_id=<?php echo $one_tweet["member_id"]; ?>">
        <button class="btn btn-default" >フォロー</button></a>
        <?php }else{  ?>
          <a href="follow.php?unfollow_id=<?php echo $one_tweet["member_id"]; ?>">
          <button class="btn btn-default" >フォロー解除</button></a>
        <?php } ?>
          
         
         
        </div>
        <?php } ?>


       

          
        
      </div>
    </div>
 
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
