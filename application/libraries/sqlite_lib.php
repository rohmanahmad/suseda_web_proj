<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Sqlite_lib {
	
	var $sqlt3;
	
	function __construct($param=array()){
		$this->sqlt3=new SQLite3($param['sqlitedbname']);
		
	}
	
	function export_users(){
		$sql="SELECT * FROM userpins AS up LEFT JOIN users AS usr ON usr.UserId=up.UserId";
		$result=$this->sqlt3->query($sql) or die('Query failed');
		$i=1;
		while ($row = $result->fetchArray())
		 {
			$id=$row['UserId'];
			$pin=$row['Pin'];
			$name=$row['DisplayName'];
			if($name !== ""){
				$data['data'.$i]=array('ID'=>$id,'pin'=>$pin,'name'=>$name);
				$i++;
			}
		 }
		 if(is_array($data)){
			return json_encode($data);
		 }
		 return json_encode(array('data'=>'null'));
	}
	
	function export_participants(){
		$sql="SELECT * FROM participants AS p LEFT JOIN userpins AS up ON up.UserId=p.UserId";
		$result=$this->sqlt3->query($sql) or die('Query failed');
		$i=1;
		while ($row = $result->fetchArray())
		 {
			$userid=$row['UserId'];
			$pin=$row['Pin'];
			$partid=$row['ParticipantId'];
			$converid=$row['ConversationId'];
			if($pin !== ""){
				$data['data'.$i]=array(
					'partid'=>$partid,
					'pin'=>$pin,
					'userid'=>$userid,
					'converid'=>$converid
				);
				$i++;
			}
		 }
		 if(is_array($data)){
			return json_encode($data);
		 }
		 return json_encode(array('data'=>'null'));
	}
	
	function export_bbm_message(){
		$sql="SELECT up.Pin,u.UserId,p.ParticipantId,tm.ConversationId,
				tm.TextMessageId,tm.Content,tm.State FROM textmessages AS tm 
				LEFT JOIN participants AS p ON p.ParticipantId=tm.ParticipantId
				LEFT JOIN users AS u ON u.UserId=p.UserId
				LEFT JOIN userpins AS up ON up.UserId=u.UserId";
		$result=$this->sqlt3->query($sql) or die('Query failed');
		$i=1;
		while ($row = $result->fetchArray())
		 {
			$messageid=$row['TextMessageId'];
			$pin=$row['Pin'];
			$userid=$row['UserId'];
			$participantid=$row['ParticipantId'];
			$conversationid=$row['ConversationId'];
			$message=$row['Content'];
			$status=$row['State'];
			//print_r($messageid.'--'.$pin.'--'.$userid.'--'.$participantid.'--'.$conversationid.'--'.$message);
			//echo "<hr />";
			if(!empty($pin) and !empty($conversationid) and !empty($participantid) and !empty($message)){
				$data['data'.$i]=array(
					'message_id'=>$messageid,
					'conversation_id'=>$conversationid,
					'participant_id'=>$participantid,
					'text_messages'=>$message,
					'status'=>$status,
				);
				$i++;
			}
		 }
		 if(is_array($data)){
			return json_encode($data);
		 }
		 return json_encode(array('data'=>'null'));
	}

}
?>