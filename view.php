<?php  
  // 宿題：個別ページの表示を完成させる
  // ヒント：$_GET["tweet_id"]の中に、表示したいつぶやきのtweet_idが格納されている
  // ヒント：送信されているtweet_idを使用して、SQL文でDBからデータを１件取得
  // ヒント：取得できたデータを、一覧の一行文の表示を参考に表示してみる

  // session_start();

  require('dbconnect.php');




  try {
    //DBのテーブル結合
     
      $sql = "SELECT `tweets`.*,`members`.`nick_name`, `members`.`picture_path` FROM `tweets` INNER JOIN `members` ON `tweets`.`member_id` = `members`.`member_id` WHERE `tweet_id`=".$_GET["tweet_id"];

      
    //SQL文を実行
      $data = array($_GET["tweet_id"]);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);

      $tweet_id = $stmt->fetch(PDO::FETCH_ASSOC);
     
      
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
      <div class="col-md-4 col-md-offset-4 content-margin-top">
        <div class="msg">
          <img src="picture_path/<?php echo $tweet_id["picture_path"]; ?>" width="100" height="100">
          <p>投稿者 : <span class="name"> <?php echo $tweet_id["nick_name"]; ?> </span></p>
          <p>
            つぶやき : <br>
            <?php echo $tweet_id["tweet"]; ?>
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $tweet_id["tweet_id"]; ?>">
            <?php 
              $modify_date = $tweet_id["modified"];

              // date関数　書式を時間に変更するとき
              // strtotime 文字型(string)のデータを日時型に変換できる
              // 24時間表記：H, 12時間表記：h　
              $modify_date = date("Y-m-d H:i", strtotime($modify_date));
             echo $modify_date ; ?>
            </a>
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
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
