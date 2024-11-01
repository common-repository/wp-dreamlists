<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <meta name="robots" content="index,follow">
    <title></title>
</head>

<body>

<h3>Послать сообщение</h3><br>
От кого: (from &lt;from@some.mail&gt;) <br>
<form action="mailer.php" method="post" enctype="multipart/form-data">
<input name="from" type="text" value=""><br>
Тема:<br>
<input name="subj" type="text" value=""><br>
Текст сообщения:<br>
<textarea name="text" rows=7 cols=50></textarea><br>
Прикрепить файлы:<br>
<input name="attach[]" type="file" value=""><br>
<input name="attach[]" type="file" value=""><br>
<input name="attach[]" type="file" value=""><br>
<input name="attach[]" type="file" value=""><br>
<input name="attach[]" type="file" value=""><br>
<input name="send" type="submit" value="Отправить!"><br><br>
</form>


<?php

if (isset($_POST['to']))
{
    $admin_mail = 'webmaster@binar-design.biz';

    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    include('htmlMimeMail.php');
    $mail = new htmlMimeMail();
    for ($i=0; $i<count($_FILES['attach']); ++$i)
    {
        $attachment = $mail->getFile($_FILES['attach']['tmp_name'][$i]);
        $mail->addAttachment($attachment, $_FILES['attach']['name'][$i], $mail->getFileType(strval($_FILES['attach']['name'][$i])));
        echo $mail->getFileType($_FILES['attach']['name']);
        echo "<br>\n";
    }
    $mail->setText($_POST['text']);
    $mail->setFrom($_POST['from']);
    $mail->setSubject($_POST['subj']);
    $result = $mail->send(array($admin_mail));

    echo $result ? 'Mail sent!' : 'Failed to send mail';

}

?>

</body>

</html>