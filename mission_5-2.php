<?php

    
    //まずデーターベースの作成
    //次に各処理とデータを結び付ける。
    //1新規投稿　２削除機能　３編集機能
    
    //5-1-1 新規投稿
    //5-1-2 削除機能
    //5-1-3 編集機能(表示)
    //5-1-4 編集機能（update）
    //5-1-5 エラー表示
    
    
    
    //デバッグの全体的な作戦を検討する必要あり
    //特に削除機能と編集機能

    //SQL:Structured Query Language (質問化された（データーベースへの）質問用言語)
    //https://kinocolog.com/pdo_column_get/
    
    
    //          PDO::FETCH_ASSOC 	  PDO::FETCH_COLUMN
    //fetchAll	  全行全列	            全行1列
    //fetch	      1行全列	              1行1列
    //(配列ではなく変数)
    //fetch=1行　fetchall=全行　ASSOC=全列　COLUMN=1列
    
    //git-hubにあげる時の注意点
    //これらを公開してしまうと、誰でもFTPに接続して中⾝を⾒ることができ、
    //不正にファイルをダウンロードされてしまいます。※情報漏洩の危険
    //また、データベースへの不正書き込みまで⾏われかねない。
    //Gitとは、オープンソースの分散バージョン管理システムの一つ。複数の開発者が共同で一つのソフトウェアを開発する際などに、ソースコードやドキュメントなどの編集履歴を統一的に管理するのに用いられる。
    //ハブとは、車輪やプロペラなどの中心にある部品や構造のこと。転じて、中心地、結節点、集線装置などの意味で用いられる。
    
    //README.mdについて　https://deeeet.com/writing/2014/07/31/readme/
    
    //TECH-BASE-D23_15
  

    // DB接続設定 
    $dsn = 'データーベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


    //tableを作る dataの格納箱
    $sql = "CREATE TABLE IF NOT EXISTS tb5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date DATETIME DEFAULT CURRENT_TIMESTAMP,"
    . "password char(10)"
    .");";
    $stmt = $pdo->query($sql);



    //削除ボタンver
    if(!empty($_POST["delete"]) && !empty($_POST["pass_delete"])){
        //削除番号+パスワードをPOST受信 
        $delete = $_POST["delete"];
        $pass_delete = $_POST["pass_delete"];

        //取得したいデータ
        $id = $delete;
        $password = $pass_delete;

        //削除命令（削除番号＋パスワードの一致）
        $sql = 'DELETE FROM tb5 WHERE id=:id AND password=:password';
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        
        $res = $stmt->execute();
        
        


    }
    
    
    //編集ボタンver
    if(!empty($_POST["editnum"]) && !empty($_POST["pass_edit"])){
        //!emptyはフォームに対して使用　issetは変数に対して
        
        //編集番号のPOST受信
        $editnum = $_POST["editnum"];
        $pass_edit = $_POST["pass_edit"];

        //取得するデータのid,passwordを指定
        $id = $editnum;
        $password = $pass_edit;
        
    
        //sql文　まず編集に該当する1行を指定する。
        $sql = 'SELECT * FROM tb5 WHERE id=:id AND password=:password';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        
        //bindする
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        //実行
        $stmt->execute();                             // ←SQLを実行する。
        
        //一行丸ごとを取得する
        $results = $stmt->fetchAll(); 
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る（＝一行の中から欲しいカラムの情報を取得する）
                $editednum  = $row['id'];
                $editname = $row['name'];
                $editcomm = $row['comment'];
                $editpass = $row['password'];
            }
        
        //一行前列を取得（配列をarydataに代入）
        //fetchが上手くできない fetchは「テーブルの行が選択された上で情報を取得する」
        
        //warning:パラメータの数が違う、というエラー。スペルミスやコードの写しそびれに注目してコードを読み直そう。
        //call to a member function: fetchを行う対象がなかった(取得に失敗した)というエラー。
        //今まで同様、スペルミスやコードの写しそびれに注意。


    }
    




    //投稿モード 
    if(!empty($_POST["name"]) && !empty($_POST["comm"]) && !empty($_POST["pass_post"])){
        //名前・コメントPOST受信 まだ編集判断番号はここで判断しない
     

        
        //編集モード
        if(!empty($_POST["edited"])){
            
            //挿入データの定義
            $editedname = $_POST["name"];
            $editedcomm = $_POST["comm"];
            $pass_edited = $_POST["pass_post"];
            $id = $_POST["edited"];
            
            //sql update文
            $sql = 'UPDATE tb5 SET name=:name,comment=:comment, password=:password WHERE id=:id';
            //prepare文
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':name', $editedname, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $editedcomm, PDO::PARAM_STR);
            $stmt->bindParam(':password', $pass_edited, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            $stmt->execute();
            
            
        }
        
        //新規投稿モード
        else{
            
            //受信データの定義
            $name = $_POST["name"];
            $comm = $_POST["comm"];
            $pass_post = $_POST["pass_post"];
            
            
            //INSERT文：データを入力（データレコードの挿入）
            //カンマ一つで動かなくなるぞ！
            $sql = $pdo -> prepare("INSERT INTO tb5 (name, comment, password) VALUES (:name, :comment, :password)");
            
            $name_post = $_POST["name"];
            $comment_post = $_POST["comm"]; 
            $password_post = $_POST["pass_post"];
            
            $sql -> bindParam(':name', $name_post, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment_post, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password_post, PDO::PARAM_STR);
            
            
            
            $sql -> execute();
            //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう。
            
            
        }
        
        


    }
        
      
            

?>





<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_1-21</title>
</head>
<body>
    
    <span style ="font-size:40px">自分を<span style = "color: mediumvioletred">色</span>で表現すると<span style = "color: palegreen">何色</span>ですか？</span>
    <!--3つのフォーム（名前・コメント・送信ボタン）-->
    <!--PHP変数をHTML領域で表示(htmlにphpを食いこませた-->
    <form action="" method="post">
        <p>【　投稿フォーム　】</p>
        <input type="text" name="name" placeholder="名前" 
                value ="<?php if(isset($editname)){ echo $editname; }?>">
        <br>
        <input type="text" name="comm" placeholder="コメント"
                value ="<?php if(isset($editcomm)){ echo $editcomm; } ?>">
        <br>
        <input type="password" name="pass_post" placeholder="パスワード"
                value ="<?php if(isset($editpass)){ echo $editpass; } ?>">
        <input type="submit" name="submit">
        <input type="hidden" name="edited" placeholder="編集判断番号"　
                value= "<?php if(isset($editednum)){ echo $editednum; } ?>">
        <br>
        <br>
        <p>【　削除フォーム　】</p>
        <input type="number" name="delete" placeholder="削除対象番号">
        <br>
        <input type="password" name="pass_delete" placeholder="パスワード">
        <button type="submit" name="debutton">削除</button>
        <br>
        <br>
        <p>【　編集フォーム　】</p>
        <input type="number" name="editnum" placeholder="編集対象番号">
        <br>
        <input type="password" name="pass_edit" placeholder="パスワード">
        <button type="submit" name="editbutton">編集</button>
        <br>
        <br>
    </form>
    
    <?php
    
        //デバッグ用(エラー表示)
        
        //Ⅰ投稿フォーム 
        if(isset($_POST["submit"])){
            //1-1名前がない
            if(empty($_POST["name"])){
                $name_error = "Name is Empty.";
                echo "!------------------!";
                echo "<br>";
                echo "<br>";
                echo "Error:".$name_error;
                echo "<br>";
                echo "<br>";
                echo "!------------------!";
            }//1-2コメントがない
            elseif(empty($_POST["comm"])){
                $comm_error ="Comm is Empty.";
                echo "!------------------!";
                echo "<br>";
                echo "<br>";
                echo "Error:".$comm_error;
                echo "<br>";
                echo "<br>";
                echo "!------------------!";
    
            }//1-3パスワードがない
            elseif(empty($_POST["pass_post"])){
                $pass_error ="Password is Empty.";
                echo "!------------------!";
                echo "<br>";
                echo "<br>";
                echo "Error:".$pass_error;
                echo "<br>";
                echo "<br>";
                echo "!------------------!";
            }
        }


        //Ⅱ削除フォーム
        if(isset($_POST["debutton"])){
            
            //2-1削除対象番号がない
            if(empty($_POST["delete"])){
                $delete_error = "Delete-number is Empty.";
                echo "!------------------!";
                echo "<br>";
                echo "<br>";
                echo "Error:".$delete_error;
                echo "<br>";
                echo "<br>";
                echo "!------------------!";
            }//2-2パスワードがない
            elseif(empty($_POST["pass_delete"])){
                $Dpass_error1 ="Password is Empty.";
                echo "!------------------!";
                echo "<br>";
                echo "<br>";
                echo "Error:".$Dpass_error1;
                echo "<br>";
                echo "<br>";
                echo "!------------------!";
    
            }
            
            
        }

        //Ⅲ編集フォーム
        if(isset($_POST["editbutton"])){
            //3-1編集対象番号がない
            if(empty($_POST["editnum"])){
                $editnum_error = "Edit-number is Empty.";
                echo "!------------------!";
                echo "<br>";
                echo "<br>";
                echo "Error:".$editnum_error;
                echo "<br>";
                echo "<br>";
                echo "!------------------!";
            }//3-2パスワードがない
            elseif(empty($_POST["pass_edit"])){
                $Epass_error1 ="Password is Empty.";
                echo "!------------------!";
                echo "<br>";
                echo "<br>";
                echo "Error:".$Epass_error1;
                echo "<br>";
                echo "<br>";
                echo "!------------------!";
    
            }
          
            
        }
         

    
       
    
    
    
    
    
    ?>



        
    
    
    

    <br>
    -----------------------------------
    <p>【　投稿一覧　】</p>
    <?php 
        //配列関数　データーベースをブラウザ上に表示(explode関数)
        //フォームの下に表示させたいから、順次進行に則りブラウザ表示のみhtmlの下にコードを記載
    
        
        //SELECT文：入力したデータレコードを抽出し、表示する
        //$rowの添字（[ ]内）は、4-2で作成したカラムの名称に併せる必要があります。
        $sql = 'SELECT * FROM tb5';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].'.'.'   ';
            echo $row['name'].'   ';
            echo '['.$row['comment'].']'.'   ';
            echo $row['date'].'<br>';
            echo "<hr>";
        }


    

    
    
    ?>


</body>


    








