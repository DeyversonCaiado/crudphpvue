<?php
	
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: PUT,GET,POST,DELETE,OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
header("Content-Type: application/json; charset-utf-8");

include "config.php";

$postjson = json_decode(file_get_contents('php://input'),true);
$today = date('Y-m-d H:i:s');

	if($postjson['operacao']=="gravar"){
		$cekemail = mysqli_fetch_array(mysqli_query($mysqli,"select nome from pessoas where pessoas='$postjson[nome]'"));
		
		if($cekemail['nome']== $postjson['nome']){
			$result = json_encode(array
			('success'=>false, 
			'msg'=>'O nome já está cadastrado'));
		}else{
		$password = md5($postjson['password']);
		
		$insert = mysqli_query($mysqli,"insert into users set 
			your_name     = '$postjson[your_name]',
			gender        = '$postjson[gender]',
			date_birth    = '$postjson[date_birth]',
			email_address = '$postjson[email_address]',
			password      = '$password',
			created_at     = '$today'");


		if($insert){
			$result = json_encode(array('success'=>true, 'msg'=>'Registrado com sucesso!'));
			
		}else{
			$result = json_encode(array
			('success'=>false, 
			'msg'=>'Não foi possivel registrar!'));
		}
		}

	 echo $result;

	}
	elseif($postjson['aksi']=="proses_login"){
		$password = md5($postjson['password']);
		$msg=" senha original: $postjson[password]  ##select * from users where email_address='$postjson[email_address]' and password='$password'";
		$logindata = mysqli_fetch_array(mysqli_query($mysqli,"select * from users where email_address='$postjson[email_address]' and password='$password'"));
		
		
		
		
		$data = array( 
			'user_id'     => $logindata['user_id'],
			'your_name'     => $logindata['your_name'],
			'gender'        => $logindata['gender'],
			'date_birth'    => $logindata['date_birth'],
			'email_address' => $logindata['email_address']
			);


		if($logindata){
			$result = json_encode(array('success'=>true, 'result'=>$data));
			
		}else{
			$result = json_encode(array
			('success'=>false,'msg'=>$msg));
		}
		

	 echo $result;

	}
	elseif($postjson['aksi']=="load_users"){
		
		$data = array();
		$query = mysqli_query($mysqli,"select * from users
		order by user_id desc limit $postjson[start],$postjson[limit]");

		while($rows = mysqli_fetch_array($query)){	
			$data[] = array( 
				'user_id'     => $rows['user_id'],
				'your_name'     => $rows['your_name'],
				'gender'        => $rows['gender'],
				'date_birth'    => $rows['date_birth'],
				'email_address' => $rows['email_address']
				);
		}

		if($query){
			$result = json_encode(array('success'=>true, 'result'=>$data));
			
		}else{
			$result = json_encode(array
			('success'=>false));
		}
		

	 echo $result;

	}
	elseif($postjson['aksi']=="del_users"){
		
		$query = mysqli_query($mysqli,"delete from users
		where user_id = $postjson[id]");

		if($query){
			$result = json_encode(array('success'=>true));
			
		}else{
			$result = json_encode(array
			('success'=>false));
		}
		

	 echo $result;

	}
	elseif($postjson['aksi']=="proses_crud"){
		
		$cekpass = mysqli_fetch_array(mysqli_query($mysqli,"select password from users where user_id='$postjson[id]'"));
		
		if($postjson['password']==""){
			$password=$cekpass['password'];
		}else{
			$password = md5($postjson['password']);

		}
		
		if($postjson['action']=="Create"){
			$cekemail = mysqli_fetch_array(mysqli_query($mysqli,"select email_address from users where email_address='$postjson[email_address]'"));
			
			if($cekemail['email_address']== $postjson['email_address']){
				$result = json_encode(array
				('success'=>false, 
				'msg'=>'E-mail já está cadastrado'));
			}else{
			
				$insert = mysqli_query($mysqli,"insert into users set 
					your_name     = '$postjson[your_name]',
					gender        = '$postjson[gender]',
					date_birth    = '$postjson[date_birth]',
					email_address = '$postjson[email_address]',
					password      = '$password',
					created_at     = '$today'");


				if($insert){
					$result = json_encode(array('success'=>true, 'msg'=>'Registrado com sucesso!'));
					
				}else{
					$result = json_encode(array
					('success'=>false, 
					'msg'=>'Não foi possivel registrar!'));
				}
			}
		}else{
	
					$update = mysqli_query($mysqli,"update users set 
					your_name     = '$postjson[your_name]',
					gender        = '$postjson[gender]',
					date_birth    = '$postjson[date_birth]',
					password      = '$password'
					where user_id = '$postjson[id]'");


				if($update){
					$result = json_encode(array('success'=>true, 'msg'=>'Atualizado com sucesso!'));
					
				}else{
					$result = json_encode(array
					('success'=>false, 
					'msg'=>'Não foi possivel atualizada!'));
				}
			
		}
	 echo $result;
	}
	
	elseif($postjson['aksi']=="load_single_data"){
		
		$data = array();
		$query = mysqli_query($mysqli,"select * from users
		where user_id = $postjson[id]");

		while($rows = mysqli_fetch_array($query)){	
			$data = array( 
				'your_name'     => $rows['your_name'],
				'gender'        => $rows['gender'],
				'date_birth'    => $rows['date_birth'],
				'email_address' => $rows['email_address']
				);
		}

		if($query){
			$result = json_encode(array('success'=>true, 'result'=>$data));
			
		}else{
			$result = json_encode(array
			('success'=>false));
		}
		

	 echo $result;

	}
?>	