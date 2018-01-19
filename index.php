<?php
// session_start();

require('function.php');

// ログインチェック
login_check();



// DBの接続
require('dbconnect.php');

// ログインチェック

// if(isset($_SESSION['id'])){
//   // ログインしている
// }else{
//   // ログインしていない
//   // ログイン画面へ飛ばす
//   header("Location: login.php");
//   exit();
// }




  // POST送信されていたら、つぶやきをINSERTで保存
  // $_POST["tweet"] =>"" $_POSTがemptyだと認識されない
  // $_POST["tweet"] =>"" $_POST["twwet"]が空だと認識される

  if (isset($_POST) && !empty($_POST["tweet"])) {
    //変数に入力された値を代入して扱いやすいようにする
    $tweet = $_POST['tweet'];  //ボタンを押したとき
    $member_id = $_SESSION['id'];
    
    
    
    try {
    //DBにつぶやきを登録するSQL文を作成
      // now() MySQLが用意してくれている関数。現在日時を取得できる
      // ?はsql injection防止のため

      $sql = "INSERT INTO `tweets`(`tweet`, `member_id`, `reply_tweet_id`, `created`, `modified`) VALUES (?,?,-1,now(),now()) ";

      
    //SQL文を実行
     
      $data = array($tweet, $member_id);    // $data = array($_POST['tweet'], $_SESSION["id"], -1);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);

    //$_SESSIONの情報を削除
      // unset 指定した変数を削除するという意味。SESSIONじゃなくても使える
      // unset($_SESSION["disc"]);

    //自分の画面に移動する(データの再送信防止)　index.phpへ遷移
      header('Location: index.php');
      exit();
      
    } catch (Exception $e) {
      
      
     }

    

  }



  // ----------表示用のデータ取得-------------


  try {
    // ログインしている人の情報を取得する
    $sql = "SELECT * FROM `members` WHERE `member_id`=".$_SESSION["id"] ;


    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $login_member = $stmt->fetch(PDO::FETCH_ASSOC);

    // 一覧用の情報を取得 
    // テーブル結合(複数のテーブルから関連したデータを取得)
    // ORDER BY `tweets`.`modified` DESC 最新順(降順)に並び替え

    // 論理削除に対応 delete_flag = 0 のものだけを取得

    $sql = "SELECT `tweets`.*,`members`.`nick_name`, `members`.`picture_path` FROM `tweets` INNER JOIN `members` ON `tweets`.`member_id` = `members`.`member_id` WHERE `delete_flag`=0 ORDER BY `tweets`.`modified` DESC";


    $stmt = $dbh->prepare($sql);
    $stmt->execute();


    // 一覧表示用の配列を用意
    $tweet_list = array();


    // 複数行のデータを取得するためのループ
    while (1) {
      $one_tweet = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($one_tweet == false) {
        break;
      }else{
        // like数を求めるSQL文
        $like_sql = "SELECT COUNT(*) as `like_count` FROM `likes` WHERE `tweet_id`=".$one_tweet["tweet_id"];

        // SQL文実行
        $like_stmt = $dbh->prepare($like_sql);
        $like_stmt->execute();

        $like_number = $like_stmt->fetch(PDO::FETCH_ASSOC);

        // $one_tweetの中身
        // $one_tweet["tweet"] つぶやき
        // $one_tweet["member_id"] つぶやいた人のid
        // $one_tweet["nick_name"] つぶやいた人のニックネーム
        // $one_tweet["picture_path"] つぶやいた人のプロフィール画像
        // $one_tweet["modified"] つぶやいた日時

        // 一行分のデータに新しいキーを用意して、like数を代入する
        $one_tweet["like_count"] = $like_number["like_count"];


        // ログインしている人がLikeしているかどうかの情報を取得
        $login_like_sql = "SELECT COUNT(*) as `like_count` FROM `likes` WHERE `tweet_id`=".$one_tweet["tweet_id"]." AND `member_id`= ".$_SESSION["id"];

        // SQL文の実行
        $login_like_stmt = $dbh->prepare($login_like_sql);
        $login_like_stmt->execute();

        // フェッチして取得
        $login_like_number = $login_like_stmt->fetch(PDO::FETCH_ASSOC);

        $one_tweet["login_like_flag"] = $login_like_number["like_count"];


        // データが取得できている
        $tweet_list[] = $one_tweet;

      }
    }



  } catch (Exception $e) {
    
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
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ  <?php echo $login_member["nick_name"];  ?> さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li><a href="index.php" class="btn btn-default">前</a></li>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <li><a href="index.php" class="btn btn-default">次</a></li>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">
        <?php foreach ($tweet_list as $one_tweet) { ?>
        

        <!-- 繰り返すタグが書かれる場所 -->
         <div class="msg">
          <img src="picture_path/<?php echo $one_tweet["picture_path"]; ?>" width="48" height="48">
          <p>
            <?php echo $one_tweet["tweet"]; ?><span class="name"> (<?php echo $one_tweet["nick_name"];  ?>) </span>
            [<a href="reply.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>">Re</a>] 
            <?php if($one_tweet["login_like_flag"]==0){ ?>
            <a href="like.php?like_tweet_id=<?php echo $one_tweet["tweet_id"]; ?>"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Like</a>
            <?php }else{ ?>
            <a href="like.php?unlike_tweet_id=<?php echo $one_tweet["tweet_id"]; ?>"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> UnLike</a>
            <?php } ?>

             <?php if($one_tweet["like_count"] >0){ echo $one_tweet["like_count"];} ?>
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>">
             <?php 
              $modify_date = $one_tweet["modified"];

              // date関数　書式を時間に変更するとき
              // strtotime 文字型(string)のデータを日時型に変換できる
              // 24時間表記：H, 12時間表記：h　
              $modify_date = date("Y-m-d H:i", strtotime($modify_date));
             echo $modify_date ; ?>
            </a>
            <?php if($_SESSION["id"] == $one_tweet["member_id"]){  ?>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a onclick="return confirm('削除します、よろしいですか？');" href="delete.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>" style="color: #F33;">削除</a>]
            <?php }  ?>
            <?php if($one_tweet["reply_tweet_id"] >0) { ?>
            [<a href="view.php?tweet_id=<?php echo $one_tweet["reply_tweet_id"] ?>" style="color: #a9a9a9;">返信元のメッセージを表示</a>]
            <?php } ?>
          </p>
        </div>

        
        <?php }?>
        
        
        
        


      </div>

    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
