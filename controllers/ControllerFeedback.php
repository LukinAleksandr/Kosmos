<?php
class ControllerFeedback
{
    function __construct()
    {
        include_once ROOT . '/models/ModelSendMail.php';
        $this->modMail = new ModelSendMail();
    }

    public function sendMail(){
        if(!empty($_POST['text'])){
            $files = $_FILES['feefback-file'] ?? [];
            $sending = $this->modMail->sendMailFeedback($_SESSION['userKosmos']['user_name'], $_SESSION['userKosmos']['user_email'], $_POST['text'], $files);
            echo json_encode($sending);
        }else{
            echo  json_encode(['status' => false, 'response' => ['message' => 'Введіть текст повідомлення']]);
        }
    }

}
