<?php
  session_start(); //SESSIONを使うときは絶対必要

  // SESSION変数は、サイトを訪れた個々のユーザーのデータを個別に管理する機能を提供する　
  // SESSION変数は、連続するリクエストにまたがって特定のデータを保持する
  
  // 書き直し処理（check.phpで書き直し、というボタンが押された時）
  if (isset($_GET['action']) && $_GET['action'] =='rewrite'){

    // 書き直すために初期表示する情報を変数に格納
    $nick_name = $_SESSION['join']['nick_name'];
    $email = $_SESSION['join']['email'];
    $password = $_SESSION['join']['password'];



  }else{
    // 通常の初期表示
    $nick_name = '';
    $email = '';
    $password = '';
  }





// POST送信された時
// if文の中身-> $_POSTという変数が存在している、かつ、$_POSTという変数の中身が空ではないとき
// !-> 逆の意味
// emptyは中身が空か判定する。0,"",null,falseというものを全て空と認識する
 if(isset($_POST) && !empty($_POST)){

 // 入力チェック
  // ニックネームが空だったら$errorという、エラーの情報を格納する変数にnick_nameはblankだったというマークを保存しておく
  if ($_POST["nick_name"]==''){
    $error['nick_name'] = 'blank';
  }
  
  if ($_POST["email"]==''){
    $error['email'] = 'blank';
  }
  // password
  // strlen 文字の長さ（文字数）を数字で返しえてくれる関数
  if ($_POST["password"]==''){
    $error['password'] = 'blank';
  } elseif (strlen($_POST["password"]) < 4){
    $error["password"] = 'length';
  }

  // 入力チェック後、エラーが何もなければ、check.phpに移動
  // $errorという変数が存在していなかった場合、入力が正常と認識
  if (!isset($error)){

    // 画像のアップロード処理
    // 例）kana1.pngを指定した場合、$picture_nameの中身は20171222142530kana1.pngというような文字列が代入される
    // YmdHis = Year, month, day, Hour, i...minutes, second
    // ファイル名の決定
    // 下記はindex.phpでしか使えない。
    $picture_name = date('YmdHis') . $_FILES['picture_path']['name'];

    // アップロード(フォルダに書き込み権限がないと保存されない！！書き込み権限は必須)
    // move_uploaded_file(アップロードしたいファイル, サーバーのどこにどういう名前でアップロードしたいか相対パスで指定)
    move_uploaded_file($_FILES['picture_path']['tmp_name'], '../picture_path/' . $picture_name);



    // SESSION変数に入力された値を保存（変数をSESSIONに登録する）(どこの画面からでも利用できる！)
    // 注意！必ずファイルの一番上にsession_start();と書く
    // POST送信された情報をjoinというキー指定で保存
    $_SESSION['join'] = $_POST;
    $_SESSION['join']['picture_path'] = $picture_name;    //他のページ（index.php以外のページで使うために、セッション変数を使い、代入する）
     // check.phpに移動
    header('Location: check.php');
    // これ以下のコードを無駄に処理しないように、このページの処理を終了させる
    exit();
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
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <!--
      designフォルダ内では2つパスの位置を戻ってからcssにアクセスしていることに注意！
     -->

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
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend>会員登録</legend>
        <form method="post" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
          <!-- ニックネーム -->
          <div class="form-group">
            <label class="col-sm-4 control-label">ニックネーム</label>
            <div class="col-sm-8">
              <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun" value="<?php echo $nick_name; ?>">
              <?php if((isset($error["nick_name"])) && ($error['nick_name'] == 'blank')){ ?>
              <p class="error">* ニックネームを入力してください。</p>
              <?php } ?>
            </div>
          </div>
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com" value="<?php echo $email; ?>">
              <?php if((isset($error["email"])) && ($error['email'] == 'blank')) { ?>
              <p class="error">* メールアドレスを入力してください。</p>
              <?php } ?>
            </div>        
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="" value="<?php echo $password; ?>">
              <?php if((isset($error["password"])) && ($error['password'] == 'blank')) { ?>
              <p class="error">* パスワードを入力してください。</p>
              <?php } ?>
              <?php if((isset($error["password"])) && ($error['password'] == 'length')) { ?>
              <p class="error">* パスワードは４文字以上を入力してください。</p>
              <?php } ?>
            </div>
          </div>
          <!-- プロフィール写真 -->
          <div class="form-group">
            <label class="col-sm-4 control-label">プロフィール写真</label>
            <div class="col-sm-8">
              <input type="file" name="picture_path" class="form-control">
            </div>
          </div>

          <input type="submit" class="btn btn-default" value="確認画面へ">
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
  </body>
</html>