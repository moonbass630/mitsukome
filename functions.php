<?php
/*
Plugin Name:mitsukome
Description:original plugin
Version:1.0 
Author:Mitsuki 
License:MIT
*/
//上記のコメントがないとWordPressがプラグインとして読み込んでくれない。


//フックで関数を呼び出すようにする。
//admin_menu()が呼び出された時にwpdocs_register_my_custom_menu_page()が呼び出されるようにする。
add_action( 'admin_menu', 'wpdocs_register_my_custom_menu_page' );
function wpdocs_register_my_custom_menu_page() {
  add_menu_page(
    'みつコメ',         //メニュー選択した後に表示されるページの名前
    'みつコメ',         //管理画面上に表示される名称
    'manage_options',  //表示するユーザの権限
    'mitsukome',      //管理するスラッグ
    'getConfigPage',  //呼び出す関数名
    'dashicons-carrot',//アイコンのURL（ニンジン）
    21                //メニュー位置
  );
} 

function getConfigPage(){

  //ファイルのパス
  $sPath = '/home/moonbass/www/wp/wp-content/plugins/mitsukome/';
  $sFileName = 'sitemap.text';

  //htmlの初期化
  $html = '';

  //保存ボタンが押された場合
  if(isset(($_POST['content']))){
    //入力情報をテキストファイルに保存する
    makeFile($sPath,$sFileName);
      
    //HTMLで作成された画面の情報を代入する
    $html .= initialScreen($html,$sPath,$sFileName);

  }else{
    //HTMLで作成された画面の情報を代入する
    $html .= initialScreen($html,$sPath,$sFileName);
  }

  //ブラウザに表示させる
  print $html;
}
/*
関数：add_shortcode
内容：ワードプレスで用意された関数。プラグイン"みつコメ"で登録した内容をショートコード"mitsukome"で呼び出せるようにする
*/
add_shortcode('mitsukome', 'randShow');

/*
テーマのfunctions.phpにショートコードを追記することで
ウィジェットや投稿画面のテキストrandShow()を実行できる
*/
//ランダム表示させる関数（）
function randShow(){
  $sPath = '/home/moonbass/www/wp/wp-content/plugins/mitsukome/';
  $sFileName = 'sitemap.text';
  //生成したファイルから取得した文字列を格納する
  $sGetFileContents = file_get_contents($sPath.$sFileName);
  //カンマ区切りで配列に格納する
  $sSpritContents = explode("<>",$sGetFileContents);
  //配列の中からランダムでキーを取得
  $sRandKey = array_rand($sSpritContents,1);
  //ランダムで一つ選択した文字列を格納する
  $sRandComment = $sSpritContents[$sRandKey];
  //表示する
  return $sRandComment;
}


//入力情報をテキストファイルに保存する関数
function makeFile($sPath,$sFileName){
  //入力欄にある値を取得する（空の要素は取り除くようにする）    
  $aPostValue = array_filter($_POST['content'],"strlen");
  //カンマ区切りに編集する。
  $sPostValue = implode('<>', $aPostValue);
  //ファイルを生成する
  file_put_contents($sPath.$sFileName, $sPostValue);
}

//みつコメの画面をHTMLで作成してくれる関数
function initialScreen($html,$sPath,$sFileName){
  $html .= '<h2>みつコメ</h2>';
  $html .= '<form action="" method="POST">';

  //ファイルの存在チェック
  if(!file_exists($sPath.$sFileName)){
    $html .='<input type="text" name="content[]" value="">';
    $html .='</br>';
  }else{
    //ファイルの内容チェック
    if(file_get_contents($sPath.$sFileName)){
      //ファイルから取得した文字列を格納する
      $sGetFileContents = file_get_contents($sPath.$sFileName);
      //カンマ区切りで配列に格納する
      $sSpritContents = explode("<>",$sGetFileContents);
      //配列に格納されている文字列をフォームと一緒に出力する
      foreach($sSpritContents as $sSavedComments){
        $html .= "<input type='text' name='content[]' value=$sSavedComments>";
        $html .='</br>';
      }
      $html .='<input type="text" name="content[]" value="">';
      $html .='</br>';
    }else{
      $html .='<input type="text" name="content[]" value="">';
      $html .='</br>';
    }
  }
  //保存ボタン
  $html .='<input type="submit" value="保存">';
  $html .= '</form>';
  
  return $html;
}

//デバッグ関数
function debug($val){
  print '<pre>';
  print_r($val);
  print '</pre>';
}

?>