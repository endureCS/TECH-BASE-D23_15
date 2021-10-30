<?php

//GETは、ページを「取得」するためのリクエスト(丸見えでセキュリティが不安)
//POSTは、情報を「送信」するためのリクエスト（セキュリティが安心　どちらもリクエスト）-->
    
//URLの末尾に「?」（クエスチョンマーク）を付け、続けて「名前=値」の形式で内容を記述する。値が複数あるときは「&」
//（アンパサンド）で区切り、「?名前1=値1&名前2=値2&名前3=値3」のように続ける。-->

//ブラウザで見る場合のURL　https://tech-base.net/tb-230570/　＋α
        
//post関数の変数を用意する
//→*問題：最初に変数の値がないのでerrorが出る
//解決策として、if文のisset関数下に設置することで、入力時にのみPOST受信させることができる
    //$name = $_POST["name"];
    //$comm = $_POST["comm"];
    //$delete = $_POST["delete"];
    //$editnum = $_POST["editnum"];
    //$edited =$_POST["edited"];
    
//Error表示
    $error1 = "Error:the number  is not founded.";
    
    
//変数の定義
    $date = date("Y/m/d h:i:s");
    $filename = "mission_3-5.txt";
    
    
//投稿番号の取得
//最初はcount関数を用いて、配列全体を数え上がるようにした。
//しかし！、それだと削除した後の投稿番号が前と重複する
//よって、最終投稿番号＋１という式に変更してみる
    if(file_exists($filename)){
        //ファイル関数で配列の読み込み
        $lines = file($filename,FILE_IGNORE_NEW_LINES);

        //行が空でなければ（既に投稿があれば）
        if(!empty($lines)){
            //最終行の抽出(<>を外す)
            $lastline = explode("<>",$lines[count($lines)-1]);
            //最終行の投稿番号の取得
            $lastcount = $lastline[0];
            //投稿番号＝最終行の投稿番号＋１
            $count = $lastcount + 1;
        }else{
            $count = 1;
        }
    }else{
         echo "file no exists";
    }
    
    
    //+削除ボタンver
    if(!empty($_POST["delete"]) && !empty($_POST["pass_delete"])){
        //削除番号+パスワードをPOST受信 
        $delete = $_POST["delete"];
        $pass_delete = $_POST["pass_delete"];

        
        $lines = file($filename,FILE_IGNORE_NEW_LINES);
        $fp = fopen($filename,"w");
        //ファイルの要素を繰り返し処理
        for($i=0; $i<count($lines); $i++){
        //line（行）の抽出　<>で区切る→投稿番号[0]、名前[1]、コメント[2]をそれぞれ区切る  
            $line = explode("<>",$lines[$i]);
        //$line[0]は投稿番号なので、line[0]=$postnumとする　既に投稿されているパスワードはline[4]
            $postnum = $line[0];
            $pass_posted = $line[4];
        
            
            //もし$postnumが削除番号でなかったら（!＝は否定の意味）書き写す
            if($postnum != $delete){
                
                    fwrite($fp,$lines[$i].PHP_EOL);
            }
            //もし$postnum=削除番号なら、パスワードが一致しなければただ書き写す（＝×削除）
            else{
                if($pass_posted != $pass_delete){
                    $Dicon = "Dpass is error" ;
                    fwrite($fp,$lines[$i].PHP_EOL);
                }
            }
        }
        fclose($fp);
        
    
        
     //編集ボタンver
    }elseif(!empty($_POST["editnum"]) && !empty($_POST["pass_edit"])){
        //!emptyはフォームに対して使用　issetは変数に対して
        
        //編集番号のPOST受信
        $editnum = $_POST["editnum"];
        $pass_edit = $_POST["pass_edit"];

        //パスワードを取得しないと、変数の定義ができない！！！！！

        $lines = file($filename,FILE_IGNORE_NEW_LINES);
        
        //ファイルの要素を繰り返し処理
        for($i=0; $i<count($lines); $i++){
        //処理の内容として<>で区切る→投稿番号[0]、名前[1]、コメント[2]をそれぞれ区切る    
            $line = explode("<>",$lines[$i]);
        //$line[0]は投稿番号なので、line[0]=$postnumとする +パスワードの取得　パスワードはline[4]
            $postnum = $line[0];
            $pass_posted =$line[4];

        //もし$postnumが編集番号なら
            if($postnum == $editnum){
                //パスワードが一致していれば
                if($pass_posted == $pass_edit){
                    $editname = $line[1];
                    $editcomm = $line[2];
                    $editpass = $line[4];
                }
                
                
        //$postnumは複数存在する値であり、それをfor関数以降に使用する場合
        //表示される$postnumは、最後の$line[$i]として扱われるため、結果的に最終投稿しか表示されなかった！
            }
        
        }   
    }

    //送信ボタンver（新規＋編集） 
    elseif(!empty($_POST["name"]) && !empty($_POST["comm"]) && !empty($_POST["pass_post"])){
        //名前・コメントPOST受信 まだ編集判断番号はここで判断しない
        $name = $_POST["name"];
        $comm = $_POST["comm"];
        $pass_post = $_POST["pass_post"];
        
        //中身が問題なし（＝×空白（名前＆コメント））
        // 要チェック　isset=!empty
        
        //編集モード（＝編集判断番号あり）
        if(!empty($_POST["edited"])){
            $edited = $_POST["edited"];
            //投稿番号と編集対象番号の比較
            $lines = file($filename,FILE_IGNORE_NEW_LINES);
            $fp = fopen($filename,"w");
            
            for($i=0; $i<count($lines); $i++){
                $line = explode("<>",$lines[$i]);
                $postnum = $line[0];
                
                if($postnum == $edited){
                    //編集後のデータを再定義（$count→$editedに変更）
                    $editeddata = $edited."<>".$name."<>".$comm."<>".$date."<>".$pass_post."<>";
                    fwrite($fp,$editeddata.PHP_EOL);
                }else{
                    fwrite($fp,$lines[$i].PHP_EOL);
                }  
            }
            fclose($fp);
        }
        //新規投稿モード（＝編集判断番号なし）
        else{ 
            $fp = fopen($filename,"a");
            
            //文字列の結合　[.]を活用
            //投稿内容をテキストファイルに保存する際、パスワードの後ろ(右側)にも区切り文字「<>」を付けておくと安全
            //文字列の最後にある「改行」も一種の文字として扱われるため、「入力されたパスワード(改行なし)」と「テキストファイルに保存されたパスワード(改行あり)」を比較したときに一致しないと判断される。
            $maindata = $count."<>".$name."<>".$comm."<>".$date."<>".$pass_post."<>";
            
            fwrite($fp,$maindata.PHP_EOL);
            fclose($fp);
        }
        
    }

//var_dump($editname);
//var_dump($editcomm);


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
                value ="<?php if(isset($editname) &&  isset($editcomm) && isset($editpass)){echo $editname;} ?>">
        <br>
        <input type="text" name="comm" placeholder="コメント"
                value ="<?php if(isset($editname) &&  isset($editcomm) && isset($editpass)){echo $editcomm;} ?>">
        <br>
        <input type="password" name="pass_post" placeholder="パスワード"
                value ="<?php if(isset($editname) &&  isset($editcomm) && isset($editpass)){echo $editpass;} ?>">
        <input type="submit" name="submit">
        <input type="hidden" name="edited" placeholder="編集判断番号"　
                value= "<?php if(isset($editname) &&  isset($editcomm)){echo $editnum;} ?>">
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
        //エラー表示
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
    
            }//2-3パスワードが一致しない
            elseif(isset($Dicon)){
                $Dpass_error2 ="Password is invalid.";
                echo "!------------------!";
                echo "<br>";
                echo "<br>";
                echo "Error:".$Dpass_error2;
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
    
            }//3-3パスワードが一致しない
            elseif(empty($editname)){
                $Epass_error2 ="Password is invalid.";
                echo "!------------------!";
                echo "<br>";
                echo "<br>";
                echo "Error:".$Epass_error2;
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
     //配列関数　テキストファイルをブラウザ上に表示(explode関数)
     //フォームの下に表示させたいから、順次進行に則りブラウザ表示のみhtmlの下にコードを記載
    $filename = "mission_3-5.txt";
    
    if(file_exists($filename)){
        $lines = file($filename,FILE_IGNORE_NEW_LINES);
        foreach($lines as $line){
            list($count,$name,$comm,$date) = explode('<>',$line);
            echo $count."."."　".$name."　"."[".$comm."]"."　".$date."<br>";
        }
    }
    
    ?>


</body>


