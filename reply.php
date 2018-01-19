<?php  
  
  
  require('function.php');

  // ログインチェック
  login_check();



  // 宿題：個別ページの表示を完成させる
  // ヒント：$_GET["tweet_id"]の中に、表示したいつぶやきのtweet_idが格納されている
  // ヒント：送信されているtweet_idを使用して、SQL文でDBからデータを１件取得
  // ヒント：取得できたデータを、一覧の一行文の表示を参考に表示してみる

  // session_start();

  // DBの接続
  require('dbconnect.php');

   // POST送信されていたら、つぶやきをINSERTで保存
   // $_POST["tweet"] =>"" $_POSTがemptyだと認識されない
   // $_POST["tweet"] =>"" $_POST["twwet"]が空だと認識される

  if (isset($_POST) && !empty($_POST["tweet"])) {
    //変数に入力された値を代入して扱いやすいようにする
    // $tweet = $_POST['tweet'];  //ボタンを押したとき
    // $member_id = $_SESSION['id'];
    
    if($_POST["tweet"] == ""){
      $error["tweet"] = "blank";
    }

    if(!isset($error)){


    //DBにつぶやきを登録するSQL文を作成
      // now() MySQLが用意してくれている関数。現在日時を取得できる
      // ?はsql injection防止のため

      $sql = "INSERT INTO `tweets`(`tweet`, `member_id`, `reply_tweet_id`, `created`) VALUES (?,?,?,now())";

      
    //SQL文を実行
     
      $data = array($_POST['tweet'],$_SESSION['id'], $_GET["tweet_id"]);    // $data = array($_POST['tweet'], $_SESSION["id"], -1);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);

    //$_SESSIONの情報を削除
      // unset 指定した変数を削除するという意味。SESSIONじゃなくても使える
      // unset($_SESSION["disc"]);

    //一覧へ移動する(データの再送信防止) index.phpへ遷移
      header('Location: index.php');
      exit();
      
    
}
    

  }





  
    // SQL文の作成
    //DBのテーブル結合
     
      $sql = "SELECT `tweets`.*,`members`.`nick_name`, `members`.`picture_path` FROM `tweets` INNER JOIN `members` ON `tweets`.`member_id` = `members`.`member_id` WHERE `tweet_id`=".$_GET["tweet_id"];

      
    //SQL文を実行
      // $data = array($_GET["tweet_id"]);
      $stmt = $dbh->prepare($sql);
      $stmt->execute();

    // 個別ページに表示するデータを取得
      $tweet_id = $stmt->fetch(PDO::FETCH_ASSOC);

      $reply_msg = "@".$tweet_id["tweet"]."(".$tweet_id["nick_name"].")";

    
      

  



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
        <h4>つぶやきに返信しましょう</h4>
        <div class="msg">
          <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやきに返信</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo $reply_msg; ?></textarea>
              </div>
            </div>
            <ul class="paging">
              <input type="submit" class="btn btn-info" value="返信としてつぶやく">
            </ul>
          </form>
        </div>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
  </body>
</html>
