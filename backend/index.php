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
    $cekemail = mysqli_fetch_array(mysqli_query($mysqli,"select nome from pessoas where nome='$postjson[nome]'"));
    
    if ($postjson['codigo']=='') {
        if($cekemail['nome']== $postjson['nome']){
            $result = json_encode(array
            ('success'=>false, 
            'msg'=>'O nome já está cadastrado'));
        }else{

            $sql = "insert into pessoas set 
                nome     = '$postjson[nome]',
                endereco        = '$postjson[endereco]',
                contatos        = '".json_encode($postjson['contatos'])."'";
                //echo $sql;
            $insert = mysqli_query($mysqli,$sql);


            if($insert){
                $result = json_encode(array('success'=>true, 'msg'=>'Registrado com sucesso!'));
                
            }else{
                $result = json_encode(array
                ('success'=>false, 
                'msg'=>'Não foi possivel registrar! Erro: '.$connection->error));
            }
        }
    }else {
        $update = mysqli_query($mysqli,"update pessoas set 
					nome     = '$postjson[nome]',
					endereco = '$postjson[endereco]',
					contatos    = '".json_encode($postjson['contatos'])."'
					where codigo = '$postjson[codigo]'");

				if($update){
					$result = json_encode(array('success'=>true, 'msg'=>'Atualizado com sucesso!'));
					
				}else{
					$result = json_encode(array
					('success'=>false, 
					'msg'=>'Não foi possivel atualizar! Erro: '.$connection->error));
				}
    }
    

 echo $result;

}elseif ($postjson['operacao']=="listar") {
    $data = array();
		$query = mysqli_query($mysqli,"select * from pessoas
		order by nome desc limit $postjson[start],$postjson[limit]");

		while($rows = mysqli_fetch_array($query)){	
			$data[] = array( 
                'codigo'     => $rows['codigo'],
				'nome'     => $rows['nome'],
				'endereco'     => $rows['endereco']
				);
		}

		if($query){
			$result = json_encode(array('success'=>true, 'result'=>$data));
			
		}else{
			$result = json_encode(array
			('success'=>false));
		}
		

	 echo $result;
}elseif($postjson['operacao']=="deletarpessoa"){
		
    $query = mysqli_query($mysqli,"delete from pessoas
    where codigo = $postjson[codigo]");

    if($query){
        $result = json_encode(array('success'=>true,'result'=>'Pessoa excluida'));
        
    }else{
        $result = json_encode(array
        ('success'=>false,'result'=>'Nao foi possivel excluir. Erro:'.$connection->error));
    }
    

 echo $result;

}elseif($postjson['operacao']=="editarpessoa"){
		
    $data = array();
    $query = mysqli_query($mysqli,"select * from pessoas
    where codigo = $postjson[codigo]");

    while($rows = mysqli_fetch_array($query)){	
        $data = array( 
            'codigo'     => $rows['codigo'],
            'nome'        => $rows['nome'],
            'endereco'    => $rows['endereco'],
            'contatos' => $rows['contatos']
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