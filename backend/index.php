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
    $cekemail = mysqli_query($mysqli,"select nome from pessoas where nome='$postjson[nome]'");
    
    if ($postjson['codigo']=='') {
        if($cekemail->num_rows){
            $cekemail = mysqli_fetch_array($cekemail);
            if ($cekemail['nome']== $postjson['nome']) {

                $result = json_encode(array
                ('success'=>false, 
                'msg'=>'O nome já está cadastrado'));
            }
        }else{
            $mysqli->autocommit(FALSE);

            $sql = "insert into pessoas set 
                nome     = '$postjson[nome]',
                endereco        = '$postjson[endereco]'";
                //echo $sql;

            if($mysqli->query($sql)){

               
                $registro_pai_id = $mysqli->insert_id;

                $registro_filho_sql = "INSERT INTO pessoas_contatos (codigopessoa,tipo, nome,telefone) VALUES";

                for ($i=0; $i < count($postjson['contatos']); $i++) { 
                    $registro_filho_sql .="('$registro_pai_id'
                                            ,'".$postjson['contatos'][$i]['tipo']."'
                                            ,'".$postjson['contatos'][$i]['nome']."'
                                            ,'".$postjson['contatos'][$i]['telefone']."'
                                            ),";
                }
                                     
                $registro_filho_sql= substr($registro_filho_sql,0,-1);
                $registro_filho_sql.=";";

                if ($mysqli->query($registro_filho_sql)) {
                    $result = json_encode(array('success'=>true, 'msg'=>'Registrado com sucesso!'));
                    $mysqli->commit();
                }else {
                    $mysqli->rollback();
                    $result = json_encode(array
                    ('success'=>false, 
                    'msg'=>'Não foi possivel registrar! Erro: '.$mysqli->error));
                }

                
                
            }else{
                $result = json_encode(array
                ('success'=>false, 
                'msg'=>'Não foi possivel registrar! Erro: '.$connection->error));
            }
        }
    }else {

        $mysqli->autocommit(FALSE);


        $sql = "update pessoas set 
					nome     = '$postjson[nome]',
					endereco = '$postjson[endereco]'
					where codigo = '$postjson[codigo]'";


				if($mysqli->query($sql)){

                    try {
                        $mysqli->query("delete from pessoas_contatos where codigopessoa = '$postjson[codigo]'");

                        $registro_pai_id = $postjson['codigo'];

                        $registro_filho_sql = "INSERT INTO pessoas_contatos (codigopessoa,tipo, nome,telefone) VALUES";

                        for ($i=0; $i < count($postjson['contatos']); $i++) { 
                            $registro_filho_sql .="('$registro_pai_id'
                                                    ,'".$postjson['contatos'][$i]['tipo']."'
                                                    ,'".$postjson['contatos'][$i]['nome']."'
                                                    ,'".$postjson['contatos'][$i]['telefone']."'
                                                    ),";
                        }
                                            
                        $registro_filho_sql= substr($registro_filho_sql,0,-1);
                        $registro_filho_sql.=";";

                        $mysqli->query($registro_filho_sql);

                        $mysqli->commit();

                        $result = json_encode(array('success'=>true, 'msg'=>'Atualizado com sucesso!'));
                    } catch (Exception $e) {

                        $mysqli->rollback();

                        $result = json_encode(array
                        ('success'=>false, 
                        'msg'=>'Não foi possivel atualizar! Erro: '.$e->getMessage()));
                    }



					
					
				}else{
					$result = json_encode(array
					('success'=>false, 
					'msg'=>'Não foi possivel atualizar! Erro: '.$mysqli->error));
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

    $mysqli->autocommit(FALSE);
		
    try {

        $mysqli->query("delete from pessoas_contatos where codigopessoa = '$postjson[codigo]'");

        $mysqli->query("delete from pessoas where codigo = '$postjson[codigo]'");

        $mysqli->commit();

        $result = json_encode(array('success'=>true, 'result'=>'Pessoa excluida'));

    } catch (Exception $e) {

        $mysqli->rollback();

        $result = json_encode(array
        ('success'=>false, 
        'result'=>'Nao foi possivel excluir. Erro:' .$e->getMessage()));
    }
    

 echo $result;

}elseif($postjson['operacao']=="editarpessoa"){
		
    $data = array();
    $query = mysqli_query($mysqli,"select * from pessoas
    where codigo = $postjson[codigo]");

    while($rows = mysqli_fetch_array($query)){	
        
            $sql = "select * from pessoas_contatos where codigopessoa= '$postjson[codigo]'";
            $qry = mysqli_query($mysqli,$sql);
            $data2 = array();
            while ($rws = mysqli_fetch_array($qry)) {
                $data2[] = array( 
                    'tipo'     => $rws['tipo'],
                    'nome'        => $rws['nome'],
                    'telefone'    => $rws['telefone'],
                    ); 
            }

            $data = array( 
            'codigo'     => $rows['codigo'],
            'nome'        => $rows['nome'],
            'endereco'    => $rows['endereco'],
            'contatos'    => $data2
            );
    }

    if($query){
        $result = json_encode(array('success'=>true, 'result'=>$data));
        
    }else{
        $result = json_encode(array
        ('success'=>false));
    }
    
 $mysqli->close();
 echo $result;

}