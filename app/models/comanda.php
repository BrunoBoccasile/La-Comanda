<?php
include_once __DIR__ . "/../controladores/productoController.php";
include_once __DIR__ . "/../controladores/empleadoController.php";

class Comanda
{
    public $id;//alfanumerico 5 caracteres
    public $detalle;//array asociativo ej:(trago => 1, cerveza => 3, comida => 4, postre => 2)
    public $estado;//pendiente/en preparacion/listo para servir
    public $tiempoEstimadoFinalizacion;//varia segun cantidad de empleados
    public $idMesa;
    public $costoTotal;
    public $fechaHoraCreacion;

    public function mostrarDatos()
    {
        return json_encode(array("ID" => $this->id, "DETALLE" => $this->detalle, "ESTADO" => $this->estado, "TIEMPO ESTIMADO FINALIZACION" => $this->tiempoEstimadoFinalizacion, "ID MESA" => $this->idMesa, "COSTO TOTAL" => $this->costoTotal, "FECHA Y HORA CREACION" => $this->fechaHoraCreacion));
    }

    public static function generarIdAlfanumerico() 
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $cadenaAleatoria = '';
        $maxCaracteres = strlen($caracteres) - 1;
    
        for ($i = 0; $i < 5; $i++) {
            $indiceAleatorio = rand(0, $maxCaracteres);
            $cadenaAleatoria .= $caracteres[$indiceAleatorio];
        }
    
        return $cadenaAleatoria;
    }

    public static function calcularTiempoEstimadoFinalizacion($detalle)
    {
        $controladorProductos = new ProductoController();
        $objetoProducto = $controladorProductos->buscarProductoPorNombre($detalle);

        //inicializo cantidades
        $cantidadBartender = 0;
        $cantidadCerveceros = 0;
        $cantidadMozos = 0; 
        $cantidadCocineros = 0;

        //obtengo cantidad de empleados de cada rubro
        $controladorEmpelados = new EmpleadoController();
        $arrayEmpleados = $controladorEmpelados->listarEmpleados();
        foreach($arrayEmpleados as $empleado)
        {
            switch($empleado->tipo)
            {
                case "bartender":
                    $cantidadBartender++;
                    break;
                case "cervecero":
                    $cantidadCerveceros++;
                    break;
                case "cocinero":
                    $cantidadCocineros++;
                    break;
                case "mozo":
                    $cantidadMozos++;
                    break;
            }
        }

        switch($objetoProducto->tipo)
        {
            case "trago":
                //trago 5 minutos cada uno
                $tiempo = 5 / $cantidadBartender;
                break;
            case "cerveza":
                //cerveza 2 minutos
                $tiempo = 2 / $cantidadCerveceros;
                break;
            case "comida":
                //comida 30 minutos, base de 10 minutos porque no hay tiempos que, a pesar de la cantidad de cocineros, no se pueden acelerar
                $tiempo = (20 / $cantidadCocineros) + 10;
                break;
            case "postre":
                //postre 10 minutos, base de 5 minutos porque no hay tiempos que, a pesar de la cantidad de cocineros, no se pueden acelerar
                $tiempo = (5 / $cantidadCocineros) + 5;
                    break;
        }
        //por cada mozo, tardara 5 minutos por comanda.

        $tiempo += (5 / $cantidadMozos);
        
        return $tiempo; 
    }
    
    public static function ValidarDatos($detalle)
    {
        $controladorProductos = new ProductoController();
        $controladorEmpleados = new EmpleadoController();
        $retorno = 1;
            $objetoProducto = $controladorProductos->buscarProductoPorNombre($detalle);
            if($objetoProducto == false)
            {
                $retorno = 0;
            }
            else
            {
                switch($objetoProducto->tipo)
                {
                    case "trago":
                        if($controladorEmpleados->listarEmpleadosPorTipo("bartender") == false)
                        {
                            $retorno = -1;
                            break;
                        }
                        break;
                    case "cerveza":
                        if($controladorEmpleados->listarEmpleadosPorTipo("cervecero") == false)
                        {
                            $retorno = -2;
                            break;
                        }
                        break;
                    case "comida":
                        if($controladorEmpleados->listarEmpleadosPorTipo("cocinero") == false)
                        {
                            $retorno = -3;
                            break;
                        }
                        break;
                    case "postre":
                        if($controladorEmpleados->listarEmpleadosPorTipo("cocinero") == false)
                        {
                            $retorno = -3;
                            break;
                        }
                        break;     
                }

            }
        return $retorno;
    }

    public function InsertarLaComanda()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into comandas (id,detalle,estado,tiempo_estimado_finalizacion,id_mesa,costo_total,fecha_hora_creacion)values('$this->id','$this->detalle','$this->estado','$this->tiempoEstimadoFinalizacion','$this->idMesa','$this->costoTotal','$this->fechaHoraCreacion')");
        $consulta->execute();
        return $this->id;
    }

    public function InsertarLaComandaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into comandas (id,detalle,estado,tiempo_estimado_finalizacion,id_mesa,costo_total,fecha_hora_creacion)values(:id,:detalle,:estado,:tiempo_estimado_finalizacion,:id_mesa,:costo_total,:fecha_hora_creacion)");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':detalle', $this->detalle, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado_finalizacion', $this->tiempoEstimadoFinalizacion, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':costo_total', $this->costoTotal, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_hora_creacion', $this->fechaHoraCreacion, PDO::PARAM_STR);
        $consulta->execute();
        return $this->id;
    }

    public static function TraerTodasLasComandas()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado != 'concluida'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public static function TraerTodasLasComandasPorMesa($idMesa)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where estado != 'concluida' and id_mesa =:idmesa");
        $consulta->bindValue(':idmesa', $idMesa, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public static function TraerTodasLasComandasPorTipo($tipo)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select c.id,c.detalle,c.estado,c.tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,c.id_mesa as idMesa,c.costo_total as costoTotal,c.fecha_hora_creacion as fechaHoraCreacion from comandas c JOIN menu m ON c.detalle = m.nombre
        WHERE c.estado != 'concluida'  AND m.tipo = :tipo");
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");
    }

    public static function TraerUnaComanda($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,detalle,estado,tiempo_estimado_finalizacion as tiempoEstimadoFinalizacion,id_mesa as idMesa,costo_total as costoTotal,fecha_hora_creacion as fechaHoraCreacion from comandas where id=:id and estado != 'concluida'");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        $comandaBuscada = $consulta->fetchObject('comanda');
        return $comandaBuscada;
    }

    public function ModificarComanda()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update comandas 
				set detalle='$this->detalle',
				estado='$this->estado',
				tiempo_estimado_finalizacion='$this->tiempoEstimadoFinalizacion',
                id_mesa='$this->idMesa',
                costo_total='$this->costoTotal',
                fecha_hora_creacion='$this->fechaHoraCreacion'
                WHERE id='$this->id'");
        return $consulta->execute();
    }

    public function ModificarComandaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update comandas 
				set detalle=:detalle,
				estado=:estado,
				tiempo_estimado_finalizacion=:tiempo_estimado_finalizacion,
                id_mesa=:id_mesa,
                costo_total=:costo_total,
                fecha_hora_creacion=:fecha_hora_creacion
                WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':detalle', $this->detalle, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado_finalizacion', $this->tiempoEstimadoFinalizacion, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':costo_total', $this->costoTotal, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_hora_creacion', $this->fechaHoraCreacion, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public function BorrarComanda()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				delete 
				from comandas				
				WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }
}
?>