<?php

class room
{
  public static function post($peticion)
  {
    $body = file_get_contents('php://input');
    $room = json_decode($body);

    $idUser = usuarios::autorizar();
    $name = $room->nombre;

    $ret = self::verificar($idUser, $name);

    if($ret > 0)
    {
      http_response_code(400);
      return ["Mensaje" => "El nombre de esa sala ya existe"];
    }
    $cmd = "INSERT INTO Room VALUES (null,?,?)";
    $st = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($cmd);
    $st->bindParam(1, $idUser, PDO::PARAM_INT);
    $st->bindParam(2, $name);

    if($st->execute())
      return ["Mensaje" => "Grupo Creado", 
              "idRoom" => self::getIDROOM($idUser, $name)];
    else
      return ["Mensaje" => "Error al crear el grupo"];
  }
  public function getIDROOM($idUser, $name)
  {
    $cmd = "SELECT idRoom FROM Room WHERE idUser=? AND name=?";
    $st = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($cmd);
    $st->bindParam(1, $idUser, PDO::PARAM_INT);
    $st->bindParam(2, $name);
    $st->execute();
    return $st->fetchColumn();
  }
  public function verificar($idUser, $name)
  {
    $cmd = "SELECT count(*) FROM Room WHERE idUser=? AND name=?";
    $st = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($cmd);
    $st->bindParam(1, $idUser);
    $st->bindParam(2, $name);
    $st->execute();
    return $st->fetchColumn();
  }
}

